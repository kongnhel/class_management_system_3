<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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

        $userProfile = $user->userProfile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        return view('student.profile.show', compact('user', 'userProfile'));
    }

    public function edit()
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            Session::flash('error', 'អ្នកមិនត្រូវបានអនុញ្ញាតឱ្យចូលប្រើទំព័រនេះទេ។');

            return redirect()->route('dashboard');
        }

        $userProfile = $user->userProfile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $programs = Program::all();

        return view('student.profile.edit', compact('user', 'userProfile', 'programs'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            Session::flash('error', 'អ្នកមិនត្រូវបានអនុញ្ញាតឱ្យអនុវត្តសកម្មភាពនេះទេ។');

            return redirect()->route('dashboard');
        }

        $userProfile = $user->userProfile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $validatedData = $request->validate([
            'full_name_km' => ['nullable', 'string', 'max:255'],
            'full_name_en' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_picture')) {
            try {
                $image = $request->file('profile_picture');

                $response = Http::withBasicAuth(env('IMAGEKIT_PRIVATE_KEY'), '')
                    ->attach(
                        'file',
                        file_get_contents($image->getRealPath()),
                        $image->getClientOriginalName()
                    )
                    ->post('https://upload.imagekit.io/api/v1/files/upload', [
                        'fileName' => 'student_'.time(),
                        'useUniqueFileName' => 'true',
                        'folder' => '/student_profiles',
                    ]);

                if ($response->successful()) {
                    $userProfile->profile_picture_url = $response->json()['url'];
                } else {
                    Log::error('ImageKit Upload Error: '.$response->body());
                }

            } catch (\Exception $e) {
                Log::error('Upload Error: '.$e->getMessage());
            }
        } elseif ($request->has('remove_profile_picture') && $request->input('remove_profile_picture') === '1') {
            $userProfile->profile_picture_url = null;
        }

        $userProfile->fill($validatedData);
        $userProfile->save();

        Session::flash('success', 'ព័ត៌មាន Profile ត្រូវបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ!');

        return redirect()->route('student.profile.show');
    }
}
