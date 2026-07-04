<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PhoneLoginController extends Controller
{
    public function checkPhone(Request $request)
    {
        $phone = trim($request->input('phone', ''));

        if (! preg_match('/^\+?[0-9]{6,15}$/', $phone)) {
            return response()->json(['exists' => false]);
        }

        $user = User::where('phone', $phone)->first();

        if (! $user) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists' => true,
        ]);
    }
}
