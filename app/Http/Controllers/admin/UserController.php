<?php

namespace App\Http\Controllers\admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
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
        $search = trim((string) $request->input('search', ''));
        $generation = $request->input('generation');
        $department_id = $request->input('department_id');
        $faculty_id = $request->input('faculty_id');

        $admins = User::where('role', 'admin')
            ->with('profile')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhereHas('profile', function ($q2) use ($search) {
                            $q2->where('full_name_km', 'LIKE', "%{$search}%")
                                ->orWhere('full_name_en', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->orderBy('name')
            ->paginate(10, ['*'], 'adminsPage')
            ->withQueryString();

        $professors = User::where('role', 'professor')
            ->with(['profile', 'department', 'professorProfile'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhereHas('profile', function ($q2) use ($search) {
                            $q2->where('full_name_km', 'LIKE', "%{$search}%")
                                ->orWhere('full_name_en', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('professorProfile', function ($q2) use ($search) {
                            $q2->where('full_name_km', 'LIKE', "%{$search}%")
                                ->orWhere('full_name_en', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('department', function ($q3) use ($search) {
                            $q3->where('name_km', 'LIKE', "%{$search}%")
                                ->orWhere('name_en', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->when($faculty_id, function ($query, $faculty_id) {
                $query->whereHas('department', fn ($q) => $q->where('faculty_id', $faculty_id));
            })
            ->when($department_id, function ($query, $department_id) {
                $query->where('department_id', $department_id);
            })
            ->orderBy('name', 'asc')
            ->get();

        $professorsGrouped = $professors->groupBy(function ($item) {
            return $item->department->name_km ?? 'មិនទាន់មានដេប៉ាតឺម៉ង់';
        });

        $students = User::where('role', 'student')
            ->with(['studentProfile', 'department', 'studentDepartmentEnrollments'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('student_id_code', 'LIKE', "%{$search}%")
                        ->orWhereHas('studentProfile', function ($q2) use ($search) {
                            $q2->where('full_name_km', 'LIKE', "%{$search}%")
                                ->orWhere('full_name_en', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('department', function ($q3) use ($search) {
                            $q3->where('name_km', 'LIKE', "%{$search}%")
                                ->orWhere('name_en', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->when($generation, function ($query, $generation) {
                return $query->where('generation', $generation);
            })
            ->when($department_id, function ($query, $department_id) {
                return $query->where('department_id', $department_id);
            })
            ->orderBy('generation', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        $progressionService = app(\App\Services\StudentProgressionService::class);
        $students->each(function ($student) use ($progressionService) {
            if ($student->department) {
                $student->computed_year_level = $progressionService->getYearLevel($student, $student->department);
            } else {
                $student->computed_year_level = null;
            }
        });

        $studentsGrouped = $students->groupBy([
            'generation',
            function ($item) {
                return $item->department->name_km ?? 'មិនទាន់មានដេប៉ាតឺម៉ង់';
            },
        ]);

        $generations = \App\Models\Generation::where('is_active', true)
            ->orderByDesc('name')
            ->pluck('name')
            ->toArray();

        $faculties = Faculty::all();
        $departments = Department::with('faculty')->get();

        return view('admin.users.index', compact(
            'admins',
            'professors',
            'students',
            'studentsGrouped',
            'professorsGrouped',
            'generations',
            'faculties',
            'departments'
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
        $transitionDepartments = collect();

        if ($user->role === 'professor') {
            $user->load(['taughtCourseOfferings' => function ($query) {
                $query->with(['course', 'department'])->orderBy('academic_year', 'desc');
            }]);
        } elseif ($user->role === 'student') {
            $user->load(['studentCourseEnrollments' => function ($query) {
                $query->with(['courseOffering.course', 'courseOffering.department'])->orderBy('created_at', 'desc');
            }]);
            $user->load('studentDepartmentEnrollments.department');

            $progressionService = app(\App\Services\StudentProgressionService::class);
            $isEligibleForTransition = $progressionService->isEligibleForTransition($user);
            $transitionDepartments = $progressionService->getTransitionDepartments($user);
        }

        return view('admin.users.show', compact('user', 'isEligibleForTransition', 'transitionDepartments'));
    }

    public function createUser()
    {
        $faculties = Faculty::all();
        $departments = Department::all();
        $generations = \App\Models\Generation::where('is_active', true)->orderByDesc('name')->pluck('name')->toArray();

        return view('admin.users.create', compact('departments', 'faculties', 'generations'));
    }

    public function previewStudentId(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'generation' => 'required|string',
            'degree_level' => 'required|string',
        ]);

        $studentId = $this->studentIdGenerator->generate(
            $request->department_id,
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
            'profile_picture' => 'nullable|image|max:5120',
        ];
        $messages = [
            'profile_picture.max' => 'រូបភាពមិនអាចធំជាង 5MB ឡើយ!',
            'profile_picture.image' => 'ឯកសារត្រូវតែជាប្រភេទរូបភាព!',
        ];

        if ($request->role === 'student') {
            $rules['department_id'] = 'required|exists:departments,id';
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
        } else {
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

        $user = User::create([
            'name' => $request->name,
            'role' => $request->role,
            'department_id' => in_array($request->role, ['student', 'professor']) ? $request->department_id : null,
            'email' => ($request->role !== 'student') ? $request->email : null,
            'password' => ($request->role !== 'student') ? Hash::make($request->password) : null,
            'generation' => ($request->role === 'student') ? $request->generation : null,
        ]);

        if ($request->role === 'student') {
            $studentId = $this->studentIdGenerator->generate($request->department_id, $request->generation, $request->degree_level);
            $user->student_id_code = $studentId;
            $user->save();

            \App\Models\StudentDepartmentEnrollment::create([
                'student_user_id' => $user->id,
                'department_id' => $request->department_id,
                'degree_level' => $request->degree_level,
                'starting_year_level' => 1,
                'enrollment_date' => now(),
                'status' => 'active',
            ]);

            $matchingOfferings = \App\Models\CourseOffering::where('department_id', $request->department_id)
                ->when($request->generation, fn ($q) => $q->where('generation', $request->generation))
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

        $profileData = $request->only(['full_name_km', 'full_name_en', 'gender', 'date_of_birth', 'phone_number', 'address']);

        if (count(array_filter($profileData)) > 0 || $request->hasFile('profile_picture')) {

            if ($request->role === 'student') {
                $profile = new StudentProfile($profileData);
                $profile->generation = $request->generation;
            } else {
                $profile = new UserProfile($profileData);
            }

            $profile->user_id = $user->id;

            if ($request->filled('profile_picture_base64')) {
                $url = $this->imageKitService->uploadProfilePictureBase64($request->input('profile_picture_base64'));
                if ($url) {
                    $profile->profile_picture_url = $url;
                }
            } elseif ($request->hasFile('profile_picture')) {
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

        return redirect()->route('admin.manage-users')->with('success', __('user_created_successfully'));
    }

    public function editUser(User $user)
    {
        $user->load('profile', 'studentProfile', 'department.faculty');
        $faculties = Faculty::all();
        $departments = Department::all();
        $generations = \App\Models\Generation::where('is_active', true)->orderByDesc('name')->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'departments', 'faculties', 'generations'));
    }

    public function ajaxEditUser(User $user)
    {
        $user->load('profile', 'studentProfile', 'department.faculty');

        $profile = $user->role === 'student' ? $user->studentProfile : $user->profile;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
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
            'departments' => Department::all()->map(fn ($d) => ['id' => $d->id, 'name' => $d->name_km ?? $d->name_en, 'faculty_id' => $d->faculty_id]),
            'faculties' => Faculty::all()->map(fn ($f) => ['id' => $f->id, 'name' => $f->name_km ?? $f->name_en]),
            'generations' => \App\Models\Generation::where('is_active', true)->orderByDesc('name')->get()->map(fn ($g) => ['name' => $g->name, 'join_year' => $g->join_year ?? '']),
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
            'profile_picture' => 'nullable|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:5120',
            'generation' => 'nullable|string|max:255',
        ];
        $messages = [
            'profile_picture.max' => 'រូបភាពមិនអាចធំជាង 5MB ឡើយ!',
            'profile_picture.mimetypes' => 'ឯកសារត្រូវតែជាប្រភេទរូបភាព!',
        ];

        if ($request->role === 'student') {
            $rules['department_id'] = 'required|exists:departments,id';
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
        $user->department_id = in_array($request->role, ['student', 'professor']) ? $request->department_id : null;
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

        if ($request->role === 'student' && $request->filled('degree_level')) {
            $user->studentDepartmentEnrollments()
                ->where('status', 'active')
                ->update(['degree_level' => $request->degree_level]);
        }

        $user->load('profile', 'studentProfile', 'department.faculty');
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
                    'department_name' => $user->department?->name_km ?? '',
                    'profile_picture_url' => $profile->profile_picture_url ?? '',
                ],
            ]);
        }

        return redirect()->route('admin.manage-users')->with('success', __('information_updated_successfully'));
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'អ្នកមិនអាចលុបគណនីផ្ទាល់ខ្លួនបានទេ។']);
            }

            return redirect()->route('admin.manage-users')
                ->with('error', __('cannot_delete_own_account'));
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
                ->with('success', __('user_and_related_data_deleted'));

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'មានបញ្ហាបច្ចេកទេស៖ '.$e->getMessage()]);
            }

            return redirect()->route('admin.manage-users')
                ->with('error', __('technical_error').$e->getMessage());
        }
    }

    public function exportUsers(Request $request)
    {
        $filters = [
            'tab' => $request->query('tab'),
            'search' => $request->query('search'),
            'generation' => $request->query('generation'),
            'department_id' => $request->query('department_id'),
        ];

        $fileName = 'users_'.($filters['tab'] ?? 'list').'_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new UsersExport($filters), $fileName);
    }

    public function printStudents(Request $request)
    {
        $generation = $request->input('generation');
        $department_id = $request->input('department_id');

        $progressionService = app(\App\Services\StudentProgressionService::class);

        $students = User::where('role', 'student')
            ->with(['studentProfile', 'department.faculty', 'studentDepartmentEnrollments', 'profile'])
            ->when($generation, fn ($q) => $q->where('generation', $generation))
            ->when($department_id, fn ($q) => $q->where('department_id', $department_id))
            ->orderBy('generation', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        $students->each(function ($student) use ($progressionService) {
            $student->computed_year_level = $student->department
                ? $progressionService->getYearLevel($student, $student->department)
                : null;
        });

        $department = $department_id ? Department::with('faculty')->find($department_id) : null;
        $currentAcademicYear = \App\Models\AcademicYear::getCurrent();

        return view('admin.users.print-students', compact('students', 'generation', 'department', 'currentAcademicYear'));
    }

    public function printProfessors(Request $request)
    {
        $faculty_id = $request->input('faculty_id');
        $department_id = $request->input('department_id');

        $query = User::where('role', 'professor')
            ->with(['profile', 'department.faculty', 'professorProfile']);

        if ($faculty_id) {
            $query->whereHas('department', fn ($q) => $q->where('faculty_id', $faculty_id));
        }
        if ($department_id) {
            $query->where('department_id', $department_id);
        }

        $professors = $query->orderBy('name', 'asc')->get();

        $faculty = $faculty_id ? Faculty::find($faculty_id) : null;
        $department = $department_id ? Department::find($department_id) : null;

        return view('admin.users.print-professors', compact('professors', 'faculty', 'department'));
    }
}
