<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCard;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceCardController extends Controller
{
    public function show()
    {
        $user = request()->user();
        $card = $user->attendanceCard;

        $photoUrl = $user->studentProfile?->profile_picture_url
            ?? $user->avatar
            ?? $user->profile?->profile_picture_url
            ?? '';
        $photoCenter = false;

        return view('student.attendance-card', compact('user', 'card', 'photoUrl', 'photoCenter'));
    }

    public function create()
    {
        $user = request()->user();
        $rawToken = Str::random(64);

        $card = $user->attendanceCard;
        if ($card) {
            $card->update([
                'token_hash' => hash('sha256', $rawToken),
                'token_encrypted' => Crypt::encryptString($rawToken),
                'revoked_at' => null,
                'last_used_at' => null,
            ]);
        } else {
            $card = AttendanceCard::create([
                'user_id' => $user->id,
                'token_hash' => hash('sha256', $rawToken),
                'token_encrypted' => Crypt::encryptString($rawToken),
            ]);
        }

        return redirect()->route('student.attendance-card')->with('success', __('Attendance card created successfully.'));
    }

    public function revoke()
    {
        $card = request()->user()->attendanceCard;

        if ($card) {
            $card->update(['revoked_at' => now()]);
        }

        return redirect()->route('student.attendance-card')->with('success', __('Attendance card revoked.'));
    }

    public function qr()
    {
        $card = request()->user()->attendanceCard;
        abort_unless($card && ! $card->revoked_at, 404);

        $rawToken = Crypt::decryptString($card->token_encrypted);
        $svg = (string) QrCode::size(420)->margin(2)->generate('NMU-ATTENDANCE-CARD:'.$rawToken);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }
}
