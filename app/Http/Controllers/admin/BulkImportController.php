<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\User;
use App\Services\StudentIdGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BulkImportController extends Controller
{
    protected $studentIdGenerator;

    public function __construct(StudentIdGeneratorService $studentIdGenerator)
    {
        $this->studentIdGenerator = $studentIdGenerator;
    }

    public function index()
    {
        $faculties = Faculty::all();
        $departments = Department::all();
        $programs = Program::all();
        $generations = \App\Models\Generation::orderByDesc('name')->get();

        return view('admin.import.index', compact('faculties', 'departments', 'programs', 'generations'));
    }

    /**
     * Direct import — no preview step
     */
    public function importUsers(Request $request)
    {
        $rules = [
            'import_file' => 'required|file|extensions:xlsx,xls,csv|max:10240',
            'role' => 'required|in:student,professor',
            'program_id' => 'nullable|required_if:role,student|exists:programs,id',
            'department_id' => 'nullable|required_if:role,professor|exists:departments,id',
            'generation' => 'nullable|string',
        ];

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $messages = collect($e->validator->errors()->all())->implode(' • ');
            return redirect()->route('admin.import.index')->with('error', $messages);
        }

        try {
            $file = $request->file('import_file');
            $data = Excel::toCollection(null, $file)->first();

            if ($data->isEmpty()) {
                return redirect()->route('admin.import.index')->with('error', 'ឯកសារមិនមានទិន្នន័យ។');
            }

            $headers = $data->first()->map(fn ($h) => trim(str_replace(["\n", "\r"], '', strtolower($h))));

            // Map Khmer template headers to English keys
            $headerMap = [
                'ឈ្មោះ *' => 'name',
                'ឈ្មោះ' => 'name',
                'អ៊ីម៉ែល' => 'email',
                'ឈ្មោះពេញខ្មែរ' => 'full_name_km',
                'ឈ្មោះពេញអង់គ្លេស' => 'full_name_en',
                'ភេទ' => 'gender',
                'លេខទូរស័ព្ទ' => 'phone',
                'អាសយដ្ឋាន' => 'address',
                'ថ្ងៃខែឆ្នាំកំណើត' => 'date_of_birth',
                // Also support English headers
                'name' => 'name',
                'email' => 'email',
                'full_name_km' => 'full_name_km',
                'full_name_en' => 'full_name_en',
                'gender' => 'gender',
                'phone' => 'phone',
                'phone_number' => 'phone',
                'address' => 'address',
                'date_of_birth' => 'date_of_birth',
                'dob' => 'date_of_birth',
            ];

            $mappedHeaders = $headers->map(fn ($h) => $headerMap[$h] ?? $h);
            $rows = $data->slice(1);

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    $rowData = array_combine($mappedHeaders->toArray(), $row->toArray());

                    // Skip empty rows
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    // Only name is required
                    if (empty($rowData['name'])) {
                        $errors[] = 'Row '.($index + 1).': ឈ្មោះមិនអាចទទេបាន';
                        $skipped++;
                        continue;
                    }

                    // Generate student_id_code first (for students with generation)
                    $studentIdCode = null;
                    if ($request->role === 'student' && $request->generation) {
                        $studentIdCode = $this->studentIdGenerator->generate((int) $request->program_id, $request->generation);
                    }

                    // Only use email from Excel — no auto-generation
                    $email = !empty($rowData['email']) ? $rowData['email'] : null;

                    // Check for duplicate email only if provided
                    if ($email && User::where('email', $email)->exists()) {
                        $errors[] = 'Row '.($index + 1).": អ៊ីម៉ែលមានរួចហើយ ({$email})";
                        $skipped++;
                        continue;
                    }

                    // Create user — only data from Excel, no password
                    $user = User::create([
                        'name' => $rowData['name'],
                        'email' => $email,
                        'role' => $request->role,
                        'password' => null,
                        'student_id_code' => $studentIdCode,
                        'program_id' => $request->role === 'student' ? $request->program_id : null,
                        'department_id' => $request->role === 'professor' ? $request->department_id : null,
                        'generation' => $request->role === 'student' ? $request->generation : null,
                    ]);

                    // Auto-enroll student in program and course offerings
                    if ($request->role === 'student' && $request->generation) {
                        // Create student_program_enrollments record
                        \App\Models\StudentProgramEnrollment::create([
                            'student_user_id' => $user->id,
                            'program_id' => $request->program_id,
                            'starting_year_level' => 1,
                            'enrollment_date' => now(),
                            'status' => 'active',
                        ]);

                        // Auto-enroll in all matching course offerings for this program
                        $matchingOfferings = \App\Models\CourseOffering::whereHas('targetPrograms', function ($q) use ($request) {
                            $q->where('course_offering_program.program_id', $request->program_id)
                              ->where('course_offering_program.generation', $request->generation);
                        })->get();

                        foreach ($matchingOfferings as $offering) {
                            \App\Models\StudentCourseEnrollment::firstOrCreate([
                                'student_user_id' => $user->id,
                                'course_offering_id' => $offering->id,
                            ], [
                                'student_id' => $user->id,
                                'enrollment_date' => now(),
                                'status' => 'enrolled',
                            ]);
                        }
                    }

                    // Normalize gender
                    $gender = strtolower($rowData['gender'] ?? '');
                    if (in_array($gender, ['ប្រុស', 'male'])) {
                        $gender = 'male';
                    } elseif (in_array($gender, ['ស្រី', 'female'])) {
                        $gender = 'female';
                    } else {
                        $gender = null;
                    }

                    // Create profile
                    $profileData = [
                        'full_name_km' => !empty($rowData['full_name_km']) ? $rowData['full_name_km'] : null,
                        'full_name_en' => !empty($rowData['full_name_en']) ? $rowData['full_name_en'] : null,
                        'gender' => $gender,
                        'phone_number' => !empty($rowData['phone']) ? $rowData['phone'] : null,
                        'address' => !empty($rowData['address']) ? $rowData['address'] : null,
                        'date_of_birth' => !empty($rowData['date_of_birth']) ? $rowData['date_of_birth'] : null,
                    ];

                    if ($request->role === 'student') {
                        $user->studentProfile()->create($profileData);
                    } else {
                        $user->profile()->create($profileData);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = 'Row '.($index + 1).': '.$e->getMessage();
                    $skipped++;
                }
            }

            $message = "បាននាំចូល {$imported} នាក់ដោយជោគជ័យ។";
            if ($skipped > 0) {
                $message .= " រំលង {$skipped} នាក់។";
            }

            return redirect()->route('admin.import.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            Log::error('Bulk import error: '.$e->getMessage()."\n".$e->getTraceAsString());

            return redirect()->route('admin.import.index')
                ->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Download XLSX template
     */
    public function downloadTemplate()
    {
        $fileName = 'import_template_'.date('Y-m-d').'.xlsx';

        return Excel::download(new \App\Exports\ImportTemplateExport, $fileName);
    }
}
