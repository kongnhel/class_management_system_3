<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    public function handleCallback(Request $request)
    {
        $googleEmail = $request->email;
        $googleId = $request->uid;

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
            $user->update(['google_id' => $googleId]);
        }

        Auth::login($user);

        return response()->json(['status' => 'success']);
    }

    public function linkAccount(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->update([
                'google_id' => $request->uid,
                'avatar' => $request->photoURL,
            ]);

            return response()->json([
                'status' => 'linked',
                'message' => 'គណនីត្រូវបានភ្ជាប់ដោយជោគជ័យ',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'មិនមានសិទ្ធិចូលប្រើប្រាស់',
        ], 401);
    }
}
