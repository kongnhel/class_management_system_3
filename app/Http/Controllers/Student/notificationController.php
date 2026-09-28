<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        $notifications = $student->notifications()->latest()->paginate(10);

        return view('student.notifications.index', compact('notifications'));
    }

    public function markAllAsRead()
    {
        $student = Auth::user();
        $student->unreadNotifications->markAsRead();

        return back()->with('success', 'បានអានការជូនដំណឹងទាំងអស់!');
    }

    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();

        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification not found.',
        ], 404);
    }

    public function markAnnouncementAsRead(Request $request, $id)
    {
        $user = Auth::user();

        $announcement = Announcement::find($id);

        if ($announcement) {
            $readRecord = AnnouncementRead::where('announcement_id', $id)->where('user_id', $user->id)->first();

            if (! $readRecord) {
                AnnouncementRead::create([
                    'announcement_id' => $id,
                    'user_id' => $user->id,
                    'read_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Announcement marked as read.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Announcement already marked as read.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Announcement not found.',
        ], 404);
    }

}
