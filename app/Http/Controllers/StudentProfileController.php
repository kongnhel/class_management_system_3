<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\StudentProgramEnrollment;
use App\Services\ImageKitService;
use App\Services\StudentProgressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Academic info
        $studentProgramEnrollment = StudentProgramEnrollment::where('student_user_id', $user->id)
            ->where('status', 'active')
            ->with('program.department.faculty')
            ->first();
        $computedYearLevel = null;
        if ($studentProgramEnrollment?->program) {
            $computedYearLevel = app(StudentProgressionService::class)
                ->getYearLevel($user, $studentProgramEnrollment->program);
        }

        return view('student.profile.show', compact('user', 'studentProfile', 'studentProgramEnrollment', 'computedYearLevel'));
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
            'profile_picture_base64' => ['nullable', 'string', 'max:5242880'],
        ]);

        $uploaded = false;

        // Prefer base64 upload (bypasses PHP upload_max_filesize)
        if ($request->filled('profile_picture_base64')) {
            try {
                $imageKitService = app(ImageKitService::class);
                $imageUrl = $imageKitService->uploadProfilePictureBase64(
                    $request->input('profile_picture_base64')
                );

                if ($imageUrl) {
                    $studentProfile->profile_picture_url = $imageUrl;
                    $uploaded = true;
                } else {
                    Session::flash('error', 'ImageKit upload failed. Check logs.');
                }
            } catch (\Exception $e) {
                Session::flash('error', 'Upload error: '.$e->getMessage());
            }
        } elseif ($request->hasFile('profile_picture')) {
            // Fallback: direct file upload
            try {
                $imageKitService = app(ImageKitService::class);
                $imageUrl = $imageKitService->uploadProfilePicture(
                    $request->file('profile_picture')
                );

                if ($imageUrl) {
                    $studentProfile->profile_picture_url = $imageUrl;
                    $uploaded = true;
                } else {
                    Session::flash('error', 'ImageKit upload failed. Check logs.');
                }
            } catch (\Exception $e) {
                Session::flash('error', 'Upload error: '.$e->getMessage());
            }
        } else {
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
