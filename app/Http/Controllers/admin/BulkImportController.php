<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Services\StudentIdGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

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
        $generations = \App\Models\Generation::where('is_active', true)->orderByDesc('name')->get();

        return view('admin.import.index', compact('faculties', 'departments', 'generations'));
    }

    public function previewUsers(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|extensions:xlsx,xls,csv|max:10240',
            'role' => 'required|in:student,professor',
            'department_id' => 'nullable|exists:departments,id',
            'generation' => 'nullable|string',
            'duplicate_action' => 'required|in:skip,reject',
            'enroll_in_matching_courses' => 'nullable|boolean',
        ]);

        $data = Excel::toCollection(null, $request->file('import_file'))->first();
        if ($data->isEmpty()) {
            return redirect()->route('admin.import.index')->with('error', 'The import file is empty.');
        }

        $headers = $data->first()->map(fn ($header) => trim(str_replace(["\n", "\r"], '', strtolower((string) $header))));
        $mappedHeaders = $this->mapHeaders($headers);
        $previewData = [];
        $errors = collect();
        $seenEmails = [];

        foreach ($data->slice(1) as $index => $row) {
            $rowNumber = $index + 2;
            $rowData = array_combine($mappedHeaders->toArray(), array_pad($row->toArray(), count($mappedHeaders), null));
            // The downloadable template may contain translated headers. Keep
            // preview usable by falling back to the documented column order.
            if (! array_key_exists('name', $rowData)) {
                $values = $row->toArray();
                $rowData = array_merge($rowData ?: [], [
                    'name' => $values[0] ?? '',
                    'email' => $values[1] ?? '',
                    'full_name_km' => $values[2] ?? '',
                    'full_name_en' => $values[3] ?? '',
                    'gender' => $values[4] ?? '',
                ]);
            }
            if (empty(array_filter($rowData ?: []))) {
                continue;
            }

            $rowErrors = [];
            $name = trim((string) ($rowData['name'] ?? ''));
            $email = trim((string) ($rowData['email'] ?? ''));

            if ($name === '') {
                $rowErrors[] = 'Student name is required.';
            }
            if ($email !== '' && (! filter_var($email, FILTER_VALIDATE_EMAIL))) {
                $rowErrors[] = 'Email format is invalid.';
            }
            if ($email !== '' && in_array(strtolower($email), $seenEmails, true)) {
                $rowErrors[] = 'Email is duplicated in this file.';
            }
            if ($email !== '') {
                $seenEmails[] = strtolower($email);
            }

            $previewData[$rowNumber] = [
                'row' => $rowNumber,
                'name' => $name,
                'email' => $email,
                'full_name_km' => $rowData['full_name_km'] ?? '',
                'full_name_en' => $rowData['full_name_en'] ?? '',
                'gender' => $rowData['gender'] ?? '',
                'status' => $rowErrors ? 'error' : 'valid',
            ];

            if ($rowErrors) {
                $errors->push(['row' => $rowNumber, 'errors' => $rowErrors]);
            }
        }

        $token = Str::random(40);
        $storedPath = $request->file('import_file')->storeAs('import-previews', $token.'.'.$request->file('import_file')->getClientOriginalExtension());
        session()->put('import_previews.'.$token, [
            'path' => $storedPath,
            'role' => $request->input('role'),
            'department_id' => $request->input('department_id'),
            'generation' => $request->input('generation'),
            'duplicate_action' => $request->input('duplicate_action', 'skip'),
            'enroll_in_matching_courses' => $request->boolean('enroll_in_matching_courses'),
        ]);

        return view('admin.import.preview', [
            'previewData' => $previewData,
            'validCount' => collect($previewData)->where('status', 'valid')->count(),
            'errorCount' => $errors->count(),
            'errors' => $errors,
            'previewToken' => $token,
            'role' => $request->input('role'),
            'departmentId' => $request->input('department_id'),
            'generation' => $request->input('generation'),
            'duplicateAction' => $request->input('duplicate_action', 'skip'),
            'enrollInMatchingCourses' => $request->boolean('enroll_in_matching_courses'),
        ]);
    }

    public function importUsers(Request $request)
    {
        $rules = [
            'import_file' => 'required_without:import_token|nullable|file|extensions:xlsx,xls,csv|max:10240',
            'import_token' => 'nullable|string',
            'role' => 'required|in:student,professor',
            'department_id' => 'nullable|exists:departments,id',
            'generation' => 'nullable|string',
            'duplicate_action' => 'required|in:skip,reject',
            'enroll_in_matching_courses' => 'nullable|boolean',
        ];

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $messages = collect($e->validator->errors()->all())->implode(' • ');

            return redirect()->route('admin.import.index')->with('error', $messages);
        }

        try {
            $preview = $request->input('import_token')
                ? session()->pull('import_previews.'.$request->input('import_token'))
                : null;

            if ($preview) {
                $request->merge([
                    'role' => $preview['role'],
                    'department_id' => $preview['department_id'],
                    'generation' => $preview['generation'],
                    'duplicate_action' => $preview['duplicate_action'],
                    'enroll_in_matching_courses' => $preview['enroll_in_matching_courses'],
                ]);
                $file = new \Illuminate\Http\UploadedFile(
                    Storage::disk('local')->path($preview['path']),
                    basename($preview['path']),
                    null,
                    null,
                    true
                );
            } else {
                $file = $request->file('import_file');
            }
            $data = Excel::toCollection(null, $file)->first();

            if ($preview) {
                Storage::disk('local')->delete($preview['path']);
            }

            if ($data->isEmpty()) {
                return redirect()->route('admin.import.index')->with('error', 'ឯកសារមិនមានទិន្នន័យ។');
            }

            $headers = $data->first()->map(fn ($h) => trim(str_replace(["\n", "\r"], '', strtolower($h))));

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
            $selectedRows = collect($request->input('student_ids', []))->map(fn ($row) => (int) $row)->all();

            foreach ($rows as $index => $row) {
                $transactionStarted = false;
                try {
                    if ($selectedRows && ! in_array($index + 2, $selectedRows, true)) {
                        continue;
                    }

                    $rowData = array_combine(
                        $mappedHeaders->toArray(),
                        array_pad($row->toArray(), count($mappedHeaders), null)
                    );

                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $rowData['name'] = trim((string) ($rowData['name'] ?? ''));
                    if ($rowData['name'] === '') {
                        $errors[] = 'Row '.($index + 1).': ឈ្មោះមិនអាចទទេបាន';
                        $skipped++;

                        continue;
                    }

                    $studentIdCode = null;
                    if ($request->role === 'student' && $request->generation && $request->department_id) {
                        $studentIdCode = $this->studentIdGenerator->generate((int) $request->department_id, $request->generation);
                    }

                    $email = ! empty($rowData['email']) ? trim((string) $rowData['email']) : null;

                    if ($email && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = 'Row '.($index + 1).': Invalid email format.';
                        $skipped++;

                        continue;
                    }

                    if ($email && $request->duplicate_action === 'skip' && User::where('email', $email)->exists()) {
                        $skipped++;
                        continue;
                    }

                    if ($email && User::where('email', $email)->exists()) {
                        $errors[] = 'Row '.($index + 1).": អ៊ីម៉ែលមានរួចហើយ ({$email})";
                        $skipped++;

                        continue;
                    }

                    DB::beginTransaction();
                    $transactionStarted = true;

                    $user = User::create([
                        'name' => $rowData['name'],
                        'email' => $email,
                        'role' => $request->role,
                        'password' => null,
                        'student_id_code' => $studentIdCode,
                        'department_id' => $request->department_id,
                        'generation' => $request->role === 'student' ? $request->generation : null,
                        'profile_status' => $request->role === 'student' && (! $request->department_id || ! $request->generation)
                            ? 'pending'
                            : 'complete',
                    ]);

                    if ($request->role === 'student' && $request->department_id) {
                        \App\Models\StudentDepartmentEnrollment::create([
                            'student_user_id' => $user->id,
                            'department_id' => $request->department_id,
                            'starting_year_level' => 1,
                            'enrollment_date' => now(),
                            'status' => 'active',
                        ]);

                        if ($request->generation && $request->boolean('enroll_in_matching_courses')) {
                            $matchingOfferings = \App\Models\CourseOffering::where('department_id', $request->department_id)
                                ->where('generation', $request->generation)
                                ->get();

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
                    }

                    $gender = strtolower($rowData['gender'] ?? '');
                    if (in_array($gender, ['ប្រុស', 'male'])) {
                        $gender = 'male';
                    } elseif (in_array($gender, ['ស្រី', 'female'])) {
                        $gender = 'female';
                    } else {
                        $gender = null;
                    }

                    $profileData = [
                        'full_name_km' => ! empty($rowData['full_name_km']) ? $rowData['full_name_km'] : null,
                        'full_name_en' => ! empty($rowData['full_name_en']) ? $rowData['full_name_en'] : null,
                        'gender' => $gender,
                        'phone_number' => ! empty($rowData['phone']) ? $rowData['phone'] : null,
                        'address' => ! empty($rowData['address']) ? $rowData['address'] : null,
                        'date_of_birth' => $this->normalizeDateOfBirth($rowData['date_of_birth'] ?? null),
                    ];

                    if ($request->role === 'student') {
                        $user->studentProfile()->create($profileData);
                    } else {
                        $user->profile()->create($profileData);
                    }

                    DB::commit();
                    $transactionStarted = false;
                    $imported++;
                } catch (\Exception $e) {
                    if ($transactionStarted && DB::transactionLevel() > 0) {
                        DB::rollBack();
                    }
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

    private function mapHeaders($headers)
    {
        $headerMap = [
            'ážˆáŸ’áž˜áž„áŸ‹ *' => 'name',
            'ážˆáŸ’áž˜áŸ„áž‡ *' => 'name',
            'ážˆáŸ’áž˜áŸ„áž‡' => 'name',
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

        return $headers->map(fn ($header) => $headerMap[$header] ?? $header);
    }

    private function normalizeDateOfBirth(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable $e) {
                throw new \InvalidArgumentException('ថ្ងៃខែឆ្នាំកំណើតក្នុង Excel មិនត្រឹមត្រូវ។');
            }
        }

        $value = trim((string) $value);
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y', 'd.m.Y'];

        foreach ($formats as $format) {
            $date = \DateTimeImmutable::createFromFormat('!'.$format, $value);
            if ($date && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }

        try {
            return (new \DateTimeImmutable($value))->format('Y-m-d');
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException('ថ្ងៃខែឆ្នាំកំណើតក្នុង Excel មិនត្រឹមត្រូវ។');
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'import_template_'.date('Y-m-d').'.xlsx';

        return Excel::download(new \App\Exports\ImportTemplateExport, $fileName);
    }
}
