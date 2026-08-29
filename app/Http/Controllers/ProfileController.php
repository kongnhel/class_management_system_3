<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Services\ImageKitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private ImageKitService $imageKitService
    ) {}

    public function edit(Request $request): View
    {
        $user = Auth::user();

        $userProfile = UserProfile::where('user_id', $user->id)->first();
        $profilePictureUrl = $userProfile?->profile_picture_url;

        return view('profile.edit', [
            'user' => $user,
            'profilePictureUrl' => $profilePictureUrl,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        $emailChanged = $user->email !== $request->input('email');

        $user->forceFill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            ...($emailChanged ? ['email_verified_at' => null] : []),
        ])->save();

        Session::flash('success', 'ព័ត៌មានប្រវត្តិរូបត្រូវបានអាប់ដេតដោយជោគជ័យ!');

        return redirect()->back();
    }

    public function updateProfilePicture(Request $request)
    {
        // Accept either base64 or file upload
        $request->validate([
            'profile_picture_base64' => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp', 'max:5120'],
        ], [
            'profile_picture.mimetypes' => 'File ដែលបាន upload ត្រូវតែជារូបភាព។',
            'profile_picture.max' => 'ទំហំរូបភាពមិនត្រូវលើស 5MB ទេ។',
        ]);

        $user = Auth::user();

        if ($request->filled('profile_picture_base64')) {
            try {
                $imageUrl = $this->imageKitService->uploadProfilePictureBase64(
                    $request->input('profile_picture_base64')
                );

                if ($imageUrl) {
                    UserProfile::updateOrCreate(
                        ['user_id' => $user->id],
                        ['profile_picture_url' => $imageUrl]
                    );

                    Session::flash('success', 'រូបភាព Profile ត្រូវបានអាប់ដេតដោយជោគជ័យ!');
                } else {
                    return redirect()->back()->withErrors(['profile_picture' => 'មានបញ្ហាក្នុងការ upload រូបភាព។']);
                }
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['profile_picture' => 'មានបញ្ហាបច្ចេកទេស៖ '.$e->getMessage()]);
            }
        } elseif ($request->hasFile('profile_picture')) {
            try {
                $imageUrl = $this->imageKitService->uploadProfilePicture(
                    $request->file('profile_picture')
                );

                if ($imageUrl) {
                    UserProfile::updateOrCreate(
                        ['user_id' => $user->id],
                        ['profile_picture_url' => $imageUrl]
                    );

                    Session::flash('success', 'រូបភាព Profile ត្រូវបានអាប់ដេតដោយជោគជ័យ!');
                } else {
                    return redirect()->back()->withErrors(['profile_picture' => 'មានបញ្ហាក្នុងការ upload រូបភាព។']);
                }
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['profile_picture' => 'មានបញ្ហាបច្ចេកទេស៖ '.$e->getMessage()]);
            }
        }

        return redirect()->back();
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();

        if ($user->userProfile) {
            $user->userProfile->delete();
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
