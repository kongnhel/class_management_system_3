<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentAttendanceController extends Controller
{
    public function myAttendance()
    {
        $user = Auth::user();

        $attendances = AttendanceRecord::where('student_user_id', $user->id)
            ->with(['courseOffering.course'])
            ->withCount(['courseOffering as total_absent' => function ($query) use ($user) {
                $query->whereHas('attendanceRecords', function ($q) use ($user) {
                    $q->where('student_user_id', $user->id)
                        ->whereIn('status', ['absent', 'អវត្តមាន']);
                });
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.my-attendance', compact('user', 'attendances'));
    }

    public function leaderAttendance($courseOfferingId)
    {
        $isLeader = DB::table('student_course_enrollments')
            ->where('course_offering_id', $courseOfferingId)
            ->where('student_user_id', auth()->id())
            ->where('is_class_leader', 1)
            ->exists();

        if (! $isLeader) {
            abort(403, 'អ្នកមិនមែនជាប្រធានថ្នាក់សម្រាប់មុខវិជ្ជានេះទេ។');
        }

        $courseOffering = CourseOffering::with('students.studentProfile')->findOrFail($courseOfferingId);
        $students = $courseOffering->students;
        $today = now()->format('Y-m-d');
        $leaderId = auth()->id();

        return view('student.leader.attendance', compact('courseOffering', 'students', 'today', 'leaderId'));
    }

    public function storeLeaderAttendance(Request $request, $courseOfferingId)
    {
        $isLeader = DB::table('student_course_enrollments')
            ->where('course_offering_id', $courseOfferingId)
            ->where('student_user_id', auth()->id())
            ->where('is_class_leader', 1)
            ->exists();

        if (! $isLeader) {
            abort(403);
        }

        $request->validate([
            'attendance' => 'required|array',
            'attendance_date' => 'required|date',
        ]);

        $date = $request->attendance_date;

        $enrolledStudentIds = DB::table('student_course_enrollments')
            ->where('course_offering_id', $courseOfferingId)
            ->pluck('student_user_id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        DB::transaction(function () use ($courseOfferingId, $date, $request, $enrolledStudentIds) {
            foreach ($request->attendance as $studentUserId => $status) {
                if (! in_array((string) $studentUserId, $enrolledStudentIds)) {
                    continue;
                }
                if (! in_array($status, ['present', 'absent', 'late', 'permission'])) {
                    continue;
                }

                DB::table('attendances')->updateOrInsert(
                    [
                        'course_offering_id' => $courseOfferingId,
                        'student_user_id' => $studentUserId,
                        'user_id' => $studentUserId,
                        'date' => $date,
                    ],
                    [
                        'status' => $status,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'រក្សាទុកវត្តមានបានជោគជ័យ!');
    }

    public function leaderAttendanceReport($courseOfferingId)
    {
        $courseOffering = CourseOffering::with('course')->findOrFail($courseOfferingId);

        $isLeader = DB::table('student_course_enrollments')
            ->where('student_user_id', auth()->id())
            ->where('course_offering_id', $courseOfferingId)
            ->where('is_class_leader', 1)
            ->exists();

        if (! $isLeader) {
            abort(403, 'អ្នកមិនមានសិទ្ធិចូលមើលរបាយការណ៍នេះទេ។');
        }

        $students = User::whereHas('enrolledCourses', function ($query) use ($courseOfferingId) {
            $query->where('course_offering_id', $courseOfferingId);
        })
            ->with(['enrolledCourses' => function ($q) use ($courseOfferingId) {
                $q->where('course_offering_id', $courseOfferingId)->with('course');
            }])
            ->withCount([
                'attendanceRecords as present_count' => function ($query) use ($courseOfferingId) {
                    $query->where('course_offering_id', $courseOfferingId)->where('status', 'present');
                },
                'attendanceRecords as absent_count' => function ($query) use ($courseOfferingId) {
                    $query->where('course_offering_id', $courseOfferingId)->where('status', 'absent');
                },
                'attendanceRecords as permission_count' => function ($query) use ($courseOfferingId) {
                    $query->where('course_offering_id', $courseOfferingId)->where('status', 'permission');
                },
                'attendanceRecords as late_count' => function ($query) use ($courseOfferingId) {
                    $query->where('course_offering_id', $courseOfferingId)->where('status', 'late');
                },
            ])
            ->get();

        return view('student.leader.report', compact('courseOffering', 'students'));
    }

    public function getAttendanceScore($studentId, $courseOfferingId)
    {
        $student = \App\Models\User::find($studentId);
        if (! $student) {
            return 0;
        }

        return $student->getAttendanceScoreByCourse($courseOfferingId);
    }
}
