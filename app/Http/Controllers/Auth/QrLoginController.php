<?php

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use App\Events\QrLoginSuccessful;
// use Illuminate\Http\Request;
// use Illuminate\Support\Str;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\Auth;

// class QrLoginController extends Controller
// {
//     public function showQrForm()
//     {
//         $token = (string) Str::uuid();
//         Cache::put('login_token_' . $token, true, now()->addMinutes(5)); // បង្កើនពេលវេលា

//         $qrCode = QrCode::size(250)
//             ->color(16, 185, 129)
//             ->margin(1)
//             ->generate($token);

//         return view('auth.login', compact('qrCode', 'token'));
//     }

//     public function handleScan(Request $request)
//     {
//         try {
//             $user = Auth::user();

//             if (!$user) {
//                 return response()->json([
//                     'status' => 'error',
//                     'message' => 'Unauthorized - សូមចូលគណនីជាមុន'
//                 ], 401);
//             }

//             $token = $request->token;

//             if (!Cache::has('login_token_' . $token)) {
//                 return response()->json([
//                     'status' => 'error',
//                     'message' => 'QR Code នេះផុតកំណត់ ឬមិនត្រឹមត្រូវឡើយ!'
//                 ], 400);
//             }

//             // ✅ សំខាន់: លុប token ដើម្បីការពារ replay attack
//             Cache::forget('login_token_' . $token);
            
//             Cache::put('authorized_user_' . $token, $user->id, now()->addMinutes(2));

//             broadcast(new QrLoginSuccessful($token, $user->id));

//             return response()->json(['status' => 'success']);

//         } catch (\Exception $e) {
//             Log::error("QR Scan Error: " . $e->getMessage());
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'មានបញ្ហាម៉ាស៊ីនបម្រើ (Server Error)'
//             ], 500);
//         }
//     }

//     public function finalizeLogin($token)
//     {
//         $userId = Cache::pull('authorized_user_' . $token);

//         if ($userId) {
//             Auth::loginUsingId($userId);
//             return redirect()->intended(route('dashboard', absolute: false));
//         }

//         return redirect()->route('login')
//             ->with('error', 'ការចូលប្រើប្រាស់ផុតកំណត់ ឬមិនត្រឹមត្រូវ។');
//     }

//     public function refreshQr()
//     {
//         $token = (string) Str::uuid();
//         Cache::put('login_token_' . $token, true, now()->addMinutes(5));

//         $qrCode = QrCode::size(200)
//             ->color(16, 185, 129)
//             ->margin(1)
//             ->generate($token);

//         return response()->json([
//             'qrCode' => (string) $qrCode,
//             'token' => $token
//         ]);
//     }
// }


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Events\QrLoginSuccessful;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class QrLoginController extends Controller
{
    public function showQrForm()
    {
        $token = (string) Str::uuid();
        
        // រក្សាទុក token សម្រាប់ស្កែន
        Cache::put('login_token_' . $token, true, now()->addMinutes(5));

        $qrCode = QrCode::size(250)
            ->color(16, 185, 129)
            ->margin(1)
            ->generate($token);

        return view('auth.login', compact('qrCode', 'token'));
    }

    public function handleScan(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized - សូមចូលគណនីជាមុន'
                ], 401);
            }

            $token = trim($request->token);

            if (empty($token) || !Cache::has('login_token_' . $token)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code នេះផុតកំណត់ ឬមិនត្រឹមត្រូវឡើយ!'
                ], 400);
            }

            // ✅ លុប token ភ្លាម ដើម្បីការពារ replay
            Cache::forget('login_token_' . $token);
            
            // រក្សាទុក User ID សម្រាប់ finalize
            Cache::put('authorized_user_' . $token, $user->id, now()->addMinutes(2));

            // Broadcast ទៅ Desktop
            broadcast(new QrLoginSuccessful($token, $user->id));

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error("QR Scan Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'មានបញ្ហាម៉ាស៊ីនបម្រើ (Server Error)'
            ], 500);
        }
    }

    public function finalizeLogin($token)
    {
        $userId = Cache::pull('authorized_user_' . $token);

        if ($userId) {
            Auth::loginUsingId($userId);
            return redirect()->intended(route('dashboard', absolute: false));
        }

        return redirect()->route('login')
            ->with('error', 'ការចូលប្រើប្រាស់ផុតកំណត់ ឬមិនត្រឹមត្រូវ។');
    }

    public function refreshQr()
    {
        $token = (string) Str::uuid();
        
        Cache::put('login_token_' . $token, true, now()->addMinutes(5));

        $qrCode = QrCode::size(200)                    
            ->color(16, 185, 129)
            ->margin(1)
            ->generate($token);

        return response()->json([
            'qrCode' => (string) $qrCode,
            'token' => $token
        ]);
    }
}