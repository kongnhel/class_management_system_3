<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Auth as FirebaseAuth;

class GoogleAuthController extends Controller
{
    public function handleCallback(Request $request, FirebaseAuth $firebaseAuth)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $verifiedToken = $firebaseAuth->verifyIdToken($request->id_token);
            $googleId = $verifiedToken->claims()->get('user_id');
            $googleEmail = $verifiedToken->claims()->get('email');
            $photoURL = $verifiedToken->claims()->get('picture');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'ត្រូវបានបដិសេធ! លិខិតសម្គាល់ Google មិនត្រឹមត្រូវ។',
            ], 401);
        }

        $user = User::where('google_id', $googleId)
            ->orWhere('email', $googleEmail)
            ->first();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'គណនី Google នេះមិនទាន់បានចុះឈ្មោះក្នុងប្រព័ន្ធ NMU ឡើយ។ សូមចុះឈ្មោះជាមុនសិន!',
            ], 403);
        }

        if (! $user->google_id) {
            $user->update([
                'google_id' => $googleId,
                'avatar' => $photoURL,
            ]);
        }

        Auth::login($user);

        $user->update([
            'is_verified' => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        return response()->json(['status' => 'success']);
    }

    public function linkAccount(Request $request, FirebaseAuth $firebaseAuth)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'មិនមានសិទ្ធិចូលប្រើប្រាស់',
            ], 401);
        }

        try {
            $verifiedToken = $firebaseAuth->verifyIdToken($request->id_token);
            $googleId = $verifiedToken->claims()->get('user_id');
            $photoURL = $verifiedToken->claims()->get('picture');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'ត្រូវបានបដិសេធ! លិខិតសម្គាល់ Google មិនត្រឹមត្រូវ។',
            ], 401);
        }

        $user->update([
            'google_id' => $googleId,
            'avatar' => $photoURL,
        ]);

        return response()->json([
            'status' => 'linked',
            'message' => 'គណនីត្រូវបានភ្ជាប់ដោយជោគជ័យ',
        ]);
    }
}