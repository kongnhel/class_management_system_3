<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
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
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        Session::flash('success', 'ព័ត៌មានប្រវត្តិរូបត្រូវបានអាប់ដេតដោយជោគជ័យ!');

        return redirect()->back();
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'profile_picture.required' => 'សូមជ្រើសរើសរូបភាពសម្រាប់ Profile Picture ។',
            'profile_picture.image' => 'File ដែលបាន upload ត្រូវតែជារូបភាព។',
            'profile_picture.max' => 'ទំហំរូបភាពមិនត្រូវលើស 2MB ទេ។',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_picture')) {
            try {
                $image = $request->file('profile_picture');

                $response = Http::asMultipart()->post('https://api.imgbb.com/1/upload', [
                    'key' => env('IMGBB_API_KEY'),
                    'image' => base64_encode(file_get_contents($image->getRealPath())),
                ]);

                if ($response->successful()) {
                    $imageUrl = $response->json()['data']['url'];

                    UserProfile::updateOrCreate(
                        ['user_id' => $user->id],
                        ['profile_picture_url' => $imageUrl]
                    );

                    Session::flash('success', 'រូបភាព Profile ត្រូវបានអាប់ដេតដោយជោគជ័យ!');
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
