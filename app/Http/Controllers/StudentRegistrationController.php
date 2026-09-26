<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\StudentCourseEnrollment;
use App\Models\StudentDepartmentEnrollment;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentRegistrationController extends Controller
{
    public function create()
    {
        $departments = Department::all();
        $generations = \App\Models\Generation::where('is_active', true)
            ->orderByDesc('name')
            ->pluck('name')
            ->toArray();

        return view('auth.register', compact('departments', 'generations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id_code' => [
                'required',
                'string',
                Rule::exists('users', 'student_id_code')->where(function ($query) {
                    return $query->where('role', 'student')->whereNull('password');
                }),
            ],
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'password' => ['required', 'confirmed', 'min:8'],
            'generation' => 'required|string',
            'degree_level' => 'required|string|max:50',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::where('student_id_code', $request->student_id_code)->firstOrFail();

                $user->forceFill([
                    'name' => $request->name,
                    'email' => $request->email,
                    'department_id' => $request->department_id,
                    'generation' => $request->generation,
                    'password' => Hash::make($request->password),
                ])->save();

                StudentDepartmentEnrollment::firstOrCreate([
                    'student_user_id' => $user->id,
                    'department_id' => $request->department_id,
                ], [
                    'degree_level' => $request->degree_level,
                    'enrollment_date' => now(),
                    'status' => 'active',
                ]);

                $courseOfferings = CourseOffering::where('department_id', $request->department_id)
                    ->where('generation', $request->generation)
                    ->get();

                foreach ($courseOfferings as $offering) {
                    $alreadyEnrolled = StudentCourseEnrollment::where('student_user_id', $user->id)
                        ->where('course_offering_id', $offering->id)
                        ->exists();
                    if (! $alreadyEnrolled) {
                        StudentCourseEnrollment::create([
                            'student_user_id' => $user->id,
                            'student_id' => $user->id,
                            'course_offering_id' => $offering->id,
                            'enrollment_date' => now(),
                            'status' => 'enrolled',
                        ]);
                    }
                }

                event(new Registered($user));
                Auth::login($user);
            });

            $user = Auth::user();

            return redirect()->intended(route('dashboard', absolute: false))
                ->with('success', 'ចុះឈ្មោះជោគជ័យ!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    public function checkStudent($code): JsonResponse
    {
        // Only return data for students who haven't registered yet
        // (password is null = pre-created by admin, no account yet).
        // Registered students are invisible to this endpoint.
        $student = User::where('student_id_code', $code)
            ->where('role', 'student')
            ->whereNull('password')
            ->with('department')
            ->first();

        if ($student) {
            return response()->json([
                'success' => true,
                'name' => $student->name,
                'department_id' => $student->department_id,
                'department_name' => $student->department->name_km ?? '',
                'generation' => $student->generation,
            ]);
        }

        return response()->json(['success' => false]);
    }
}
