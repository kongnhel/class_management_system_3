<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Services\ImageKitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'profile_picture' => 'nullable|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:5120',
        ]);

        $userProfile = $user->userProfile()->firstOrNew(['user_id' => $user->id]);

        if ($request->hasFile('profile_picture')) {
            try {
                $imageKitService = app(ImageKitService::class);
                $imageUrl = $imageKitService->uploadProfilePicture(
                    $request->file('profile_picture')
                );

                if ($imageUrl) {
                    $userProfile->profile_picture_url = $imageUrl;
                } else {
                    Log::error('ImageKit Upload returned null for professor '.$user->id);
                }
            } catch (\Exception $e) {
                Log::error('Upload Error: '.$e->getMessage());
            }
        }

        $userProfile->fill($request->except(['profile_picture']));
        $userProfile->save();

        return redirect()
            ->route('professor.profile.show')
            ->with('success', __('ប្រវត្តិរូបរបស់អ្នកត្រូវបានកែប្រែដោយជោគជ័យ។'));
    }
}
