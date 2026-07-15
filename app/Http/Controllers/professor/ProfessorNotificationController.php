<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use App\Models\Notification;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ProfessorNotificationController extends Controller
{
    public function createNotificationForm()
    {
        $user = Auth::user();
        $courseOfferings = CourseOffering::where('lecturer_user_id', $user->id)->with('course')->whereHas('course')->get();

        $allStudentsByCourse = [];
        foreach ($courseOfferings as $offering) {
            $students = StudentCourseEnrollment::where('course_offering_id', $offering->id)
                ->with('student.studentProfile')
                ->get()
                ->map(function ($enrollment) {
                    return [
                        'id' => $enrollment->student->id,
                        'name' => $enrollment->student->studentProfile->full_name_km ?? $enrollment->student->name,
                        'student_id_code' => $enrollment->student->student_id_code,
                    ];
                });
            $allStudentsByCourse[$offering->id] = $students;
        }

        return view('professor.notifications.create', compact('courseOfferings', 'allStudentsByCourse'));
    }

    public function getStudentsForCourseOffering(CourseOffering $courseOffering)
    {

        $students = StudentCourseEnrollment::where('course_offering_id', $courseOffering->id)
            ->with('student.studentProfile')
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->student->id,
                    'name' => $enrollment->student->studentProfile->full_name_km ?? $enrollment->student->name,
                ];
            });

        return response()->json($students);
    }

    public function notificationsStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'recipient_ids' => 'required|array|min:1',
            'recipient_ids.*' => 'exists:users,id',
            'message' => 'required|string|max:2000',
        ], [
            'title.required' => 'សូមបញ្ចូលចំណងជើង។',
            'recipient_ids.required' => 'សូមជ្រើសរើសយ៉ាងហោចណាស់និស្សិតម្នាក់។',
            'message.required' => 'សារមិនអាចទទេបានទេ។',
        ]);

        $sender = Auth::user();
        $recipientIds = $request->input('recipient_ids');
        $recipients = User::whereIn('id', $recipientIds)->get();

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No valid recipients found.');
        }

        $batchUuid = Str::uuid()->toString();

        foreach ($recipients as $recipient) {
            $notificationData = [
                'from_user_id' => $sender->id,
                'from_user_name' => $sender->name,
                'title' => $request->title,
                'message' => $request->message,
                'batch_uuid' => $batchUuid,
                'recipient_ids' => $recipientIds,
            ];

            $recipient->notify(new GeneralNotification($notificationData));
        }

        Session::flash('success', 'ការជូនដំណឹងត្រូវបានផ្ញើដោយជោគជ័យ!');

        return redirect()->route('professor.notifications.index');
    }

    public function notificationsIndex()
    {
        $user = Auth::user();

        $sentNotifications = Notification::where('data->from_user_id', Auth::id())
            ->select('notifications.*')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn ($item) => $item->data['batch_uuid'] ?? $item->id)
            ->map(fn ($group) => $group->first());

        $receivedNotifications = $user->notifications()->latest()->get();

        $courseOfferingIds = \App\Models\StudentCourseEnrollment::where('student_user_id', $user->id)->pluck('course_offering_id');
        $announcements = \App\Models\Announcement::where(function ($q) use ($user) {
            $q->where('target_role', 'all')
              ->orWhere('target_role', $user->role);
        })->with('poster')->get();

        $combinedReceived = collect();
        foreach ($receivedNotifications as $notification) {
            $combinedReceived->push((object) [
                'id' => $notification->id,
                'type' => 'notification',
                'title' => $notification->data['title'] ?? 'ការជូនដំណឹងថ្មី',
                'content' => $notification->data['message'] ?? '',
                'from_user_name' => $notification->data['from_user_name'] ?? 'System',
                'created_at' => $notification->created_at,
                'is_read' => $notification->read_at ? true : false,
            ]);
        }
        foreach ($announcements as $announcement) {
            $isRead = \App\Models\AnnouncementRead::where('announcement_id', $announcement->id)
                ->where('user_id', $user->id)
                ->exists();
            $combinedReceived->push((object) [
                'id' => $announcement->id,
                'type' => 'announcement',
                'title' => $announcement->title_km ?? $announcement->title_en,
                'content' => $announcement->content_km ?? $announcement->content_en,
                'from_user_name' => $announcement->poster->name ?? 'Admin',
                'created_at' => $announcement->created_at,
                'is_read' => $isRead,
            ]);
        }
        $combinedReceived = $combinedReceived->sortByDesc('created_at')->values();

        return view('professor.notifications.index', [
            'sentNotifications' => $sentNotifications,
            'receivedNotifications' => $combinedReceived,
        ]);
    }

    public function notificationsDestroy($notification_id)
    {
        $notification = DatabaseNotification::findOrFail($notification_id);

        if (($notification->data['from_user_id'] ?? null) != Auth::id()) {
            Session::flash('error', 'អ្នកមិនមានសិទ្ធិលុបការជូនដំណឹងនេះទេ។');

            return redirect()->route('professor.notifications.index');
        }

        $batchUuid = $notification->data['batch_uuid'] ?? null;

        DB::transaction(function () use ($batchUuid, $notification) {
            if ($batchUuid) {
                DatabaseNotification::where('data->batch_uuid', $batchUuid)->delete();
            } else {
                $notification->delete();
            }
        });

        Session::flash('success', 'ការជូនដំណឹងត្រូវបានលុបដោយជោគជ័យ!');

        return redirect()->route('professor.notifications.index');
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    public function getStudentsInCourseOffering($offering_id)
    {
        $user = Auth::user();

        $courseOffering = CourseOffering::where('id', $offering_id)
            ->where('lecturer_user_id', $user->id)
            ->with([
                'course',
                'studentCourseEnrollments.student.studentProfile',
                'studentCourseEnrollments.student.studentProgramEnrollments.program', //
            ])
            ->firstOrFail();

        $stats = [
            'total' => $courseOffering->studentCourseEnrollments->count(),
            'male' => 0,
            'female' => 0,
            'leaders' => 0,
        ];

        $students = $courseOffering->studentCourseEnrollments->map(function ($enrollment) use (&$stats) {
            $student = $enrollment->student;

            $gender = strtoupper($student->studentProfile->gender ?? '');
            if (in_array($gender, ['M', 'MALE', 'ប្រុស'])) {
                $stats['male']++;
            } elseif (in_array($gender, ['F', 'FEMALE', 'ស្រី'])) {
                $stats['female']++;
            }

            if ($enrollment->is_class_leader) {
                $stats['leaders']++;
            }

            return $student;
        });

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('studentsPage');
        $currentItems = $students->slice(($currentPage - 1) * $perPage, $perPage)->values()->all();

        $paginatedStudents = new LengthAwarePaginator($currentItems, $students->count(), $perPage, $currentPage, [
            'path' => request()->url(),
            'pageName' => 'studentsPage',
        ]);

        return view('professor.students.index', compact('courseOffering', 'paginatedStudents', 'stats'));
    }
}
