<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\Program;
use App\Models\StudentCourseEnrollment;
use App\Models\StudentProgramEnrollment;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
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
                    'generation' => $request->generation,
                    'password' => Hash::make($request->password),
                ])->save();

                StudentProgramEnrollment::firstOrCreate([
                    'student_user_id' => $user->id,
                    'program_id' => $request->program_id,
                ], [
                    'enrollment_date' => now(),
                    'status' => 'active',
                ]);

                $courseOfferings = CourseOffering::where('generation', $request->generation)
                    ->whereHas('course', function ($query) use ($request) {
                        $query->where('program_id', $request->program_id);
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

            return redirect()->route('dashboard')->with('success', 'ចុះឈ្មោះជោគជ័យ!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
