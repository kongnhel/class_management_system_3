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

    public function notifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications;

        $courseOfferingIds = StudentCourseEnrollment::where('student_user_id', $user->id)->pluck('course_offering_id');
        $announcements = Announcement::where('target_role', 'all')
            ->orWhere('target_role', 'student')
            ->orWhereIn('course_offering_id', $courseOfferingIds)
            ->with('poster')
            ->get();

        $combinedFeed = collect();

        foreach ($notifications as $notification) {
            $combinedFeed->push((object) [
                'id' => $notification->id,
                'type' => 'notification',
                'title' => $notification->data['title'] ?? 'ការជូនដំណឹងថ្មី',
                'content' => $notification->data['message'] ?? '',
                'created_at' => $notification->created_at,
                'is_read' => $notification->read_at ? true : false,
            ]);
        }

        foreach ($announcements as $announcement) {
            $isRead = AnnouncementRead::where('announcement_id', $announcement->id)
                ->where('user_id', $user->id)
                ->exists();

            $combinedFeed->push((object) [
                'id' => $announcement->id,
                'type' => 'announcement',
                'title' => $announcement->title_km ?? $announcement->title_en,
                'content' => $announcement->content_km ?? $announcement->content_en,
                'created_at' => $announcement->created_at,
                'poster' => $announcement->poster,
                'is_read' => $isRead,
            ]);
        }

        $combinedFeed = $combinedFeed->sortByDesc('created_at')->sortBy('is_read');

        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $currentItems = $combinedFeed->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedFeed = new LengthAwarePaginator(
            $currentItems,
            $combinedFeed->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        return view('student.notifications.index', compact('paginatedFeed'));
    }
}
