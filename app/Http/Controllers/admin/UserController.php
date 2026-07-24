<?php

namespace App\Http\Controllers\admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ImageKitService;
use App\Services\StudentIdGeneratorService;
use App\Traits\AuditableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    use AuditableTrait;

    protected $imageKitService;

    protected $studentIdGenerator;

    public function __construct(ImageKitService $imageKitService, StudentIdGeneratorService $studentIdGenerator)
    {
        $this->imageKitService = $imageKitService;
        $this->studentIdGenerator = $studentIdGenerator;
    }

    public function manageUsers(Request $request)
    {
        $search = $request->input('search');
        $generation = $request->input('generation');
        $program_id = $request->input('program_id');

        $admins = User::where('role', 'admin')
            ->with('profile')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhereHas('profile', function ($q2) use ($search) {
                            $q2->where('full_name_km', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->orderBy('name')
            ->paginate(10, ['*'], 'adminsPage');

        $professors = User::where('role', 'professor')
            ->with(['profile', 'department'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhereHas('profile', function ($q2) use ($search) {
                            $q2->where('full_name_km', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('department', function ($q3) use ($search) {
                            $q3->where('name_km', 'LIKE', "%{$search}%")
                                ->orWhere('name_en', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        $professorsGrouped = $professors->groupBy(function ($item) {
            return $item->department->name_km ?? 'មិនទាន់មានដេប៉ាតឺម៉ង់';
        });

        $students = User::where('role', 'student')
            ->with(['studentProfile', 'program', 'studentProgramEnrollments'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhereHas('studentProfile', function ($q2) use ($search) {
                            $q2->where('full_name_km', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('program', function ($q3) use ($search) {
                            $q3->where('name_km', 'LIKE', "%{$search}%")
                                ->orWhere('name_en', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->when($generation, function ($query, $generation) {
                return $query->where('generation', $generation);
            })
            ->when($program_id, function ($query, $program_id) {
                return $query->where('program_id', $program_id);
            })
            ->orderBy('generation', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        $progressionService = app(\App\Services\StudentProgressionService::class);
        $students->each(function ($student) use ($progressionService) {
            if ($student->program) {
                $student->computed_year_level = $progressionService->getYearLevel($student, $student->program);
            } else {
                $student->computed_year_level = null;
            }
        });

        $studentsGrouped = $students->groupBy([
            'generation',
            function ($item) {
                return $item->program->name_km ?? 'មិនទាន់មានកម្មវិធីសិក្សា';
            },
        ]);

        $generations = User::where('role', 'student')
            ->whereNotNull('generation')
            ->distinct()
            ->pluck('generation')
            ->sortDesc();

        $programs = \App\Models\Program::all();

        return view('admin.users.index', compact(
            'admins',
            'professors',
            'students',
            'studentsGrouped',
            'professorsGrouped',
            'generations',
            'programs'
        ));
    }

    public function searchUsers(Request $request)
    {
        $search = $request->input('q');

        $users = User::with('profile')
            ->where('name', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->orWhereHas('profile', function ($q) use ($search) {
                $q->where('full_name_km', 'LIKE', "%{$search}%");
            })
            ->limit(5)
            ->get();

        return response()->json($users);
    }

    public function getDepartmentsByFaculty(Faculty $faculty)
    {
        $departments = $faculty->departments()->select('id', 'name_km', 'name_en')->get();

        return response()->json($departments);
    }

    public function showUser(User $user)
    {
        $user->load(['profile', 'studentProfile']);

        $isEligibleForTransition = false;
        $transitionPrograms = collect();

        if ($user->role === 'professor') {
            $user->load(['taughtCourseOfferings' => function ($query) {
                $query->with(['course', 'program'])->orderBy('academic_year', 'desc');
            }]);
        } elseif ($user->role === 'student') {
            $user->load(['studentCourseEnrollments' => function ($query) {
                $query->with(['courseOffering.course', 'courseOffering.program'])->orderBy('created_at', 'desc');
            }]);
            $user->load('studentProgramEnrollments.program');

            $progressionService = app(\App\Services\StudentProgressionService::class);
            $isEligibleForTransition = $progressionService->isEligibleForTransition($user);
            $transitionPrograms = $progressionService->getTransitionPrograms($user);
        }

        return view('admin.users.show', compact('user', 'isEligibleForTransition', 'transitionPrograms'));
    }

    public function createUser()
    {
        $faculties = Faculty::all();
        $departments = Department::all();
        $programs = Program::all();
        $generations = \App\Models\Generation::orderByDesc('name')->pluck('name')->toArray();

        return view('admin.users.create', compact('departments', 'programs', 'faculties', 'generations'));
    }

    public function previewStudentId(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'generation' => 'required|string',
            'degree_level' => 'required|string',
        ]);

        $studentId = $this->studentIdGenerator->generate(
            $request->program_id,
            $request->generation,
            $request->degree_level
        );

        return response()->json(['student_id' => $studentId]);
    }

    public function storeUser(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'role' => ['required', 'string', Rule::in(['admin', 'professor', 'student'])],
            'full_name_km' => 'nullable|string|max:255',
            'full_name_en' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:10',
            'date_of_birth' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ];
        $messages = [
            'profile_picture.max' => 'រូបភាពមិនអាចធំជាង ២MB ឡើយ!',
            'profile_picture.image' => 'ឯកសារត្រូវតែជាប្រភេទរូបភាព!',
        ];

        if ($request->role === 'student') {
            $rules['program_id'] = 'required|exists:programs,id';
            $rules['generation'] = 'required|string|max:255';
            $rules['degree_level'] = 'required|string|max:50';
        } elseif ($request->role === 'professor') {
            $rules['email'] = 'required|string|email|max:255|unique:users';
            $rules['password'] = [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ];
            $rules['department_id'] = 'required|exists:departments,id';
        } else { // Admin
            $rules['email'] = 'required|string|email|max:255|unique:users';
            $rules['password'] = [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ];
        }

        $request->validate($rules);

        // --- Create the core User model ---
        $user = User::create([

            'name' => $request->name,
            'role' => $request->role,
            'department_id' => ($request->role === 'professor') ? $request->department_id : null,
            'program_id' => ($request->role === 'student') ? $request->program_id : null,
            'email' => ($request->role !== 'student') ? $request->email : null,
            'password' => ($request->role !== 'student') ? Hash::make($request->password) : null,
            'generation' => ($request->role === 'student') ? $request->generation : null,
        ]);

        // Auto-generate student_id_code and create program enrollment for students
        if ($request->role === 'student') {
            $studentId = $this->studentIdGenerator->generate($request->program_id, $request->generation, $request->degree_level);
            $user->student_id_code = $studentId;
            $user->save();

            // Create student_program_enrollments record so progression page can find them
            \App\Models\StudentProgramEnrollment::create([
                'student_user_id' => $user->id,
                'program_id' => $request->program_id,
                'degree_level' => $request->degree_level,
                'starting_year_level' => 1,
                'enrollment_date' => now(),
                'status' => 'active',
            ]);

            // Auto-enroll in all matching existing course offerings
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

        $profileData = $request->only(['full_name_km', 'full_name_en', 'gender', 'date_of_birth', 'phone_number', 'address']);

        if (count(array_filter($profileData)) > 0 || $request->hasFile('profile_picture')) {

            if ($request->role === 'student') {
                $profile = new StudentProfile($profileData);
                $profile->generation = $request->generation;
            } else {
                $profile = new UserProfile($profileData);
            }

            $profile->user_id = $user->id;

            if ($request->hasFile('profile_picture')) {
                $url = $this->imageKitService->uploadProfilePicture($request->file('profile_picture'));
                if ($url) {
                    $profile->profile_picture_url = $url;
                }
            }

            if ($request->role === 'student') {
                $user->studentProfile()->save($profile);
            } else {
                $user->profile()->save($profile);
            }
        }

        $this->logCreated($user);

        return redirect()->route('admin.manage-users')->with('success', 'អ្នកបានបង្កើតអ្នកប្រើប្រាស់ថ្មីដោយជោគជ័យ។');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function editUser(User $user)
    {
        $user->load('profile', 'studentProfile', 'department.faculty', 'program');
        $faculties = Faculty::all();
        $departments = Department::all();
        $programs = Program::all();
        $generations = \App\Models\Generation::orderByDesc('name')->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'departments', 'programs', 'faculties', 'generations'));
    }

    public function ajaxEditUser(User $user)
    {
        $user->load('profile', 'studentProfile', 'department.faculty', 'program');

        $profile = $user->role === 'student' ? $user->studentProfile : $user->profile;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'program_id' => $user->program_id,
            'department_id' => $user->department_id,
            'generation' => $user->generation,
            'student_id_code' => $user->student_id_code,
            'full_name_km' => $profile->full_name_km ?? '',
            'full_name_en' => $profile->full_name_en ?? '',
            'gender' => $profile->gender ?? '',
            'phone_number' => $profile->phone_number ?? '',
            'address' => $profile->address ?? '',
            'date_of_birth' => $profile->date_of_birth ?? '',
            'faculty_id' => $user->department?->faculty_id ?? '',
            'profile_picture_url' => $profile->profile_picture_url ?? '',
            'programs' => Program::all()->map(fn($p) => ['id' => $p->id, 'name' => $p->name_km ?? $p->name_en]),
            'departments' => Department::all()->map(fn($d) => ['id' => $d->id, 'name' => $d->name_km ?? $d->name_en, 'faculty_id' => $d->faculty_id]),
            'faculties' => \App\Models\Faculty::all()->map(fn($f) => ['id' => $f->id, 'name' => $f->name_km ?? $f->name_en]),
            'generations' => \App\Models\Generation::orderByDesc('name')->get()->map(fn($g) => ['name' => $g->name, 'join_year' => $g->join_year ?? '']),
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'role' => ['required', 'string', Rule::in(['admin', 'professor', 'student'])],
            'full_name_km' => 'nullable|string|max:255',
            'full_name_en' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:10',
            'date_of_birth' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'generation' => 'nullable|string|max:255',
        ];
        $messages = [
            'profile_picture.max' => 'រូបភាពមិនអាចធំជាង ២MB ឡើយ!',
            'profile_picture.image' => 'ឯកសារត្រូវតែជាប្រភេទរូបភាព!',
            'profile_picture.mimes' => 'រូបភាពត្រូវតែជាប្រភេទ: jpeg, png, jpg!',
        ];

        if ($request->role === 'student') {
            $rules['program_id'] = 'required|exists:programs,id';
            $rules['degree_level'] = 'nullable|string|max:50';
        } else {
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)];
            $rules['password'] = 'nullable|string|min:8|confirmed';
            if ($request->role === 'professor') {
                $rules['department_id'] = 'required|exists:departments,id';
            }
        }

        $request->validate($rules, $messages);

        $oldAttributes = $user->attributesToArray();

        $user->name = $request->name;
        $user->role = $request->role;
        $user->department_id = ($request->role === 'professor') ? $request->department_id : null;
        $user->program_id = ($request->role === 'student') ? $request->program_id : null;
        $user->generation = ($request->role === 'student') ? $request->generation : null;

        if ($request->role !== 'student') {
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
        }
        $user->save();

        $profile = null;
        if ($request->role === 'student') {
            $profile = $user->studentProfile()->firstOrNew(['user_id' => $user->id]);
            if ($user->profile) {
                $user->profile->delete();
            }
        } else {
            $profile = $user->profile()->firstOrNew(['user_id' => $user->id]);
            if ($user->studentProfile) {
                $user->studentProfile->delete();
            }
        }

        $profile->fill($request->only(['full_name_km', 'full_name_en', 'gender', 'date_of_birth', 'phone_number', 'address']));

        if ($request->hasFile('profile_picture')) {
            $url = $this->imageKitService->uploadProfilePicture($request->file('profile_picture'));
            if ($url) {
                $profile->profile_picture_url = $url;
            }
        } elseif ($request->input('remove_picture')) {
            $profile->profile_picture_url = null;
        }

        $profile->save();

        $this->logUpdated($user, $oldAttributes);

        // Update degree_level on active enrollment if provided
        if ($request->role === 'student' && $request->filled('degree_level')) {
            $user->studentProgramEnrollments()
                ->where('status', 'active')
                ->update(['degree_level' => $request->degree_level]);
        }

        $user->load('profile', 'studentProfile', 'department.faculty', 'program');
        $profile = $user->role === 'student' ? $user->studentProfile : $user->profile;

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'ព័ត៌មានត្រូវបានធ្វើបច្ចុប្បន្នភាព។',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'full_name_km' => $profile->full_name_km ?? '',
                    'full_name_en' => $profile->full_name_en ?? '',
                    'gender' => $profile->gender ?? '',
                    'phone_number' => $profile->phone_number ?? '',
                    'student_id_code' => $user->student_id_code ?? '',
                    'program_name' => $user->program?->name_km ?? '',
                    'department_name' => $user->department?->name_km ?? '',
                    'profile_picture_url' => $profile->profile_picture_url ?? '',
                ],
            ]);
        }

        return redirect()->route('admin.manage-users')->with('success', 'ព័ត៌មានត្រូវបានធ្វើបច្ចុប្បន្នភាព។');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'អ្នកមិនអាចលុបគណនីផ្ទាល់ខ្លួនបានទេ។']);
            }
            return redirect()->route('admin.manage-users')
                ->with('error', 'អ្នកមិនអាចលុបគណនីផ្ទាល់ខ្លួនបានទេ។');
        }

        try {
            \DB::transaction(function () use ($user) {
                \App\Models\CourseOffering::where('lecturer_user_id', $user->id)->update(['lecturer_user_id' => null]);
                \App\Models\StudentCourseEnrollment::where('student_user_id', $user->id)->delete();
                $user->load(['profile', 'studentProfile']);

                if ($user->profile) {
                    $user->profile->delete();
                }

                if ($user->studentProfile) {
                    $user->studentProfile->delete();
                }

                $user->forceDelete();
            });

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'អ្នកប្រើប្រាស់ និងទិន្នន័យពាក់ព័ន្ធត្រូវបានលុបដោយជោគជ័យ។']);
            }

            return redirect()->route('admin.manage-users')
                ->with('success', 'អ្នកប្រើប្រាស់ និងទិន្នន័យពាក់ព័ន្ធត្រូវបានលុបដោយជោគជ័យ។');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'មានបញ្ហាបច្ចេកទេស៖ '.$e->getMessage()]);
            }

            return redirect()->route('admin.manage-users')
                ->with('error', 'មានបញ្ហាបច្ចេកទេស៖ '.$e->getMessage());
        }
    }

    public function exportUsers(Request $request)
    {
        $filters = [
            'tab' => $request->query('tab'),
            'search' => $request->query('search'),
            'generation' => $request->query('generation'),
            'program_id' => $request->query('program_id'),
        ];

        $fileName = 'users_'.($filters['tab'] ?? 'list').'_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new UsersExport($filters), $fileName);
    }

    public function printStudents(Request $request)
    {
        $generation = $request->input('generation');
        $program_id = $request->input('program_id');

        $progressionService = app(\App\Services\StudentProgressionService::class);

        $students = User::where('role', 'student')
            ->with(['studentProfile', 'program', 'studentProgramEnrollments', 'profile'])
            ->when($generation, fn ($q) => $q->where('generation', $generation))
            ->when($program_id, fn ($q) => $q->where('program_id', $program_id))
            ->orderBy('generation', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        $students->each(function ($student) use ($progressionService) {
            $student->computed_year_level = $student->program
                ? $progressionService->getYearLevel($student, $student->program)
                : null;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.users.print-students', compact('students'))
            ->setPaper('a4', 'landscape');

        $domPdf = $pdf->getDomPDF();
        $fontDir = $domPdf->getOptions()->getFontDir();

        $srcFont = 'C:\Windows\Fonts\KhmerOSbattambang.ttf';
        if (!file_exists($srcFont)) {
            $srcFont = base_path('storage/fonts/KhmerOSbattambang.ttf');
        }
        $localFont = $fontDir . '/KhmerOSbattambang.ttf';

        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }

        if (!file_exists($localFont) && file_exists($srcFont)) {
            copy($srcFont, $localFont);
        }

        if (file_exists($localFont)) {
            $chroot = $domPdf->getOptions()->getChroot();
            if (is_array($chroot)) {
                $chroot = $chroot[0] ?? realpath('.');
            }
            $relativeFontPath = str_replace($chroot . '\\', '', str_replace($chroot . '/', '', $localFont));
            $fontUrl = 'file://' . str_replace('\\', '/', $relativeFontPath);

            $domPdf->getFontMetrics()->registerFont(
                ['family' => 'KhmerOSbattambang', 'weight' => 'normal', 'style' => 'normal'],
                $fontUrl
            );
            $domPdf->getFontMetrics()->registerFont(
                ['family' => 'KhmerOSbattambang', 'weight' => 'bold', 'style' => 'normal'],
                $fontUrl
            );
        }

        return $pdf->stream('student_list.pdf');
    }
}
