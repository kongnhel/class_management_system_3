<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\Program;
use App\Models\StudentCourseEnrollment;
use App\Models\StudentProgramEnrollment;
use App\Models\User;
use App\Services\OtpService;
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
        $programs = Program::all();
        $generations = User::select('generation')->distinct()->pluck('generation')->filter()->all();

        return view('auth.register', compact('programs', 'generations'));
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
            'program_id' => 'required|exists:programs,id',
            'password' => ['required', 'confirmed', 'min:8'],
            'generation' => 'required|string',
        ]);

try {
                DB::transaction(function () use ($request) {
                    $user = User::where('student_id_code', $request->student_id_code)->firstOrFail();

                    $user->forceFill([
                        'name' => $request->name,
                        'email' => $request->email,
                        'program_id' => $request->program_id,
                        'generation' => $request->generation,
                        'password' => Hash::make($request->password),
                        'is_verified' => false,
                    ])->save();

                    StudentProgramEnrollment::firstOrCreate([
                        'student_user_id' => $user->id,
                        'program_id' => $request->program_id,
                    ], [
                        'enrollment_date' => now(),
                        'status' => 'active',
                    ]);

                    $courseOfferings = CourseOffering::whereHas('targetPrograms', function ($query) use ($request) {
                        $query->where('course_offering_program.program_id', $request->program_id)
                              ->where('course_offering_program.generation', $request->generation);
                    })->get();

                    foreach ($courseOfferings as $offering) {
                        StudentCourseEnrollment::create([
                            'student_user_id' => $user->id,
                            'student_id' => $user->id,
                            'course_offering_id' => $offering->id,
                            'enrollment_date' => now(),
                            'status' => 'enrolled',
                        ]);
                    }

                    event(new Registered($user));
                    Auth::login($user);
                });

                $otpService = app(OtpService::class);
                $otpService->sendOtp(Auth::user(), 'email');

                return redirect()->route('otp.show')
                    ->with('success', 'ចុះឈ្មោះជោគជ័យ! សូមពិនិត្យអ៊ីមែលរបស់អ្នកសម្រាប់កូដផ្ទៀងផ្ទាត់។');

            } catch (\Exception $e) {
                return back()->with('error', 'Error: '.$e->getMessage());
            }
    }

    public function checkStudent($code): JsonResponse
    {
        $student = User::where('student_id_code', $code)
            ->where('role', 'student')
            ->with('program')
            ->first();

        if ($student) {
            return response()->json([
                'success' => true,
                'name' => $student->name,
                'program_id' => $student->program_id,
                'program_name' => $student->program->name_km ?? '',
                'generation' => $student->generation,
            ]);
        }

        return response()->json(['success' => false]);
    }
}
