<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $programs = Program::all();

        return view('auth.register', compact('programs'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate->Validation->ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $user = User::where('student_id_code', $request->student_id_code)->first();

        if (! $user) {
            return back()->with('error', 'ប្រតិបត្តិការមិនជោគជ័យ! ទិន្នន័យសិស្សមិនត្រឹមត្រូវតាមប្រព័ន្ធរដ្ឋបាល។');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules->Password::defaults()],
            'student_id_code' => ['required', 'string', 'max:20', 'unique:users,student_id_code'],
            'program_id' => ['required', 'exists:programs,id'],
            'generation' => ['required', 'string', 'max:255'],
        ]);

        $program = Program::findOrFail($request->program_id);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'student_id_code' => $request->student_id_code,
            'program_id' => $request->program_id,
            'department_id' => $program->department_id,
            'email_verified_at' => null,
            'generation' => $request->generation,
        ]);

        $user->update([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);
        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard'))->with('success', 'សូមស្វាគមន៍មកកាន់ NMU Portal!');
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
