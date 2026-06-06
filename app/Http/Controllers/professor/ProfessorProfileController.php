<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProfessorProfileController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user();
        $userProfile = $user->userProfile;
        if (! $userProfile) {
            $userProfile = new UserProfile;
            $userProfile->user_id = $user->id;
        }

        return view('professor.profile.show', compact('user', 'userProfile'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        $userProfile = $user->userProfile;
        if (! $userProfile) {
            $userProfile = new UserProfile;
            $userProfile->user_id = $user->id;
        }

        return view('professor.profile.edit', compact('user', 'userProfile'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name_km' => 'required|string|max:255',
            'full_name_en' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'telegram_user' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userProfile = $user->userProfile()->firstOrNew(['user_id' => $user->id]);

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
                        'fileName' => 'student_'.time().'_'.auth()->id(),
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
        }

        $userProfile->fill($request->except(['profile_picture']));
        $userProfile->save();

        return redirect()
            ->route('professor.profile.show')
            ->with('success', 'ប្រវត្តិរូបរបស់អ្នកត្រូវបានកែប្រែដោយជោគជ័យ!');
    }
}
