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
            try {
                $imageKitService = app(ImageKitService::class);
                Log::info('Student profile upload: file received', [
                    'user_id' => $user->id,
                    'size' => $request->file('profile_picture')->getSize(),
                    'mime' => $request->file('profile_picture')->getMimeType(),
                    'original_name' => $request->file('profile_picture')->getClientOriginalName(),
                ]);
                $imageUrl = $imageKitService->uploadProfilePicture(
                    $request->file('profile_picture')
                );

                if ($imageUrl) {
                    $studentProfile->profile_picture_url = $imageUrl;
                    Log::info('Student profile upload: success', ['url' => $imageUrl]);
                } else {
                    Log::error('ImageKit Upload returned null for student '.$user->id);
                }
            } catch (\Exception $e) {
                Log::error('Upload Error: '.$e->getMessage());
            }
        } else {
            Log::info('Student profile update: no file uploaded', ['user_id' => $user->id]);
            if ($request->has('remove_profile_picture') && $request->input('remove_profile_picture') === '1') {
                $studentProfile->profile_picture_url = null;
            }
        }

        $studentProfile->fill($validatedData);
        $studentProfile->save();

        Session::flash('success', 'ព័ត៌មាន Profile ត្រូវបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ!');

        return redirect()->route('student.profile.show');
    }
}
