<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\StudentProfile;
use App\Services\ImageKitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class StudentProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            Session::flash('error', 'អ្នកមិនត្រូវបានអនុញ្ញាតឱ្យចូលប្រើទំព័រនេះទេ។');

            return redirect()->route('dashboard');
        }

        $studentProfile = $user->studentProfile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        return view('student.profile.show', compact('user', 'studentProfile'));
    }

    public function edit()
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            Session::flash('error', 'អ្នកមិនត្រូវបានអនុញ្ញាតឱ្យចូលប្រើទំព័រនេះទេ។');

            return redirect()->route('dashboard');
        }

        $studentProfile = $user->studentProfile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $programs = Program::all();

        return view('student.profile.edit', compact('user', 'studentProfile', 'programs'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            Session::flash('error', 'អ្នកមិនត្រូវបានអនុញ្ញាតឱ្យអនុវត្តសកម្មភាពនេះទេ។');

            return redirect()->route('dashboard');
        }

        $studentProfile = $user->studentProfile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $validatedData = $request->validate([
            'full_name_km' => ['nullable', 'string', 'max:255'],
            'full_name_en' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            \Log::info('STUDENT UPLOAD DEBUG', [
                'has_file' => true,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'name' => $file->getClientOriginalName(),
                'real_path' => $file->getRealPath(),
                'valid' => $file->isValid(),
            ]);
            dd('File received: '.$file->getClientOriginalName().' size='.$file->getSize().' mime='.$file->getMimeType());
        } else {
            dd('No file received. hasFile=false. Keys: '.implode(', ', array_keys($request->all())));
        }

        $studentProfile->fill($validatedData);
        $studentProfile->save();

        Session::flash('success', 'ព័ត៌មាន Profile ត្រូវបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ!');

        return redirect()->route('student.profile.show');
    }
}
