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

        return view('admin.import.index', compact('faculties', 'departments', 'programs'));
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'role' => 'required|in:student,professor',
            'program_id' => 'required_if:role,student|exists:programs,id',
            'department_id' => 'required_if:role,professor|exists:departments,id',
            'generation' => 'nullable|string',
        ]);

        try {
            $file = $request->file('import_file');
            $data = Excel::toCollection(null, $file)->first();

            if ($data->isEmpty()) {
                return back()->with('error', 'ឯកសារមិនមានទិន្នន័យ។');
            }

            $headers = $data->first();
            $rows = $data->slice(1);

            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    $rowData = array_combine($headers, $row->toArray());

                    // Validate required fields
                    if (empty($rowData['name']) || empty($rowData['email'])) {
                        $errors[] = 'Row '.($index + 2).': Name and Email are required';

                        continue;
                    }

                    // Check if user already exists
                    $existingUser = User::where('email', $rowData['email'])->first();
                    if ($existingUser) {
                        $errors[] = 'Row '.($index + 2).": Email already exists ({$rowData['email']})";

                        continue;
                    }

                    // Create user
                    $user = User::create([
                        'name' => $rowData['name'],
                        'email' => $rowData['email'],
                        'role' => $request->role,
                        'password' => Hash::make($rowData['password'] ?? 'password123'),
                        'program_id' => $request->role === 'student' ? $request->program_id : null,
                        'department_id' => $request->role === 'professor' ? $request->department_id : null,
                        'generation' => $request->role === 'student' ? $request->generation : null,
                    ]);

                    // Auto-generate student_id_code for students
                    if ($request->role === 'student' && $request->generation) {
                        $studentId = $this->studentIdGenerator->generate($request->program_id, $request->generation);
                        $user->student_id_code = $studentId;
                        $user->save();
                    }

                    // Create profile
                    $profileData = [
                        'full_name_km' => $rowData['full_name_km'] ?? null,
                        'full_name_en' => $rowData['full_name_en'] ?? null,
                        'gender' => $rowData['gender'] ?? null,
                        'phone_number' => $rowData['phone'] ?? null,
                    ];

                    if ($request->role === 'student') {
                        $user->studentProfile()->create($profileData);
                    } else {
                        $user->profile()->create($profileData);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = 'Row '.($index + 2).': '.$e->getMessage();
                }
            }

            $message = "Imported {$imported} users successfully.";
            if (! empty($errors)) {
                $message .= ' '.count($errors).' errors encountered.';
            }

            return redirect()->route('admin.import.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            Log::error('Bulk import error: '.$e->getMessage());

            return redirect()->route('admin.import.index')
                ->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = ['name', 'email', 'password', 'student_id', 'full_name_km', 'full_name_en', 'gender', 'phone'];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers);

            // Add sample row
            fputcsv($file, [
                'John Doe',
                'john@example.com',
                'password123',
                'STU001',
                'ជួន ដូ',
                'John Doe',
                'Male',
                '012345678',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="import_template.csv"',
        ]);
    }
}
