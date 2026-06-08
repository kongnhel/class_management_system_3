<?php

namespace App\Http\Controllers\professor;

use App\Http\Controllers\Controller;
use App\Models\AttendanceProfessor;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfessorAttendanceController extends Controller
{
    /**
     * Store a newly created attendance record in storage.
     */
    public function storeAttendance(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'student_user_id' => [
                'required',
                Rule::exists('student_course_enrollments', 'student_user_id')
                    ->where('course_offering_id', $request->course_offering_id),
            ],
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,permission',
            'remarks' => 'nullable|string|max:255',
        ], [
            'student_user_id.required' => 'អត្តសញ្ញាណសិស្សតម្រូវឱ្យបញ្ចូល។',
            'student_user_id.exists' => 'អត្តសញ្ញាណសិស្សមិនមានឈ្មោះក្នុងបញ្ជីរៀននៃវគ្គសិក្សានេះទេ។',
            'course_offering_id.required' => 'អត្តសញ្ញាណវគ្គសិក្សាតម្រូវឱ្យបញ្ចូល។',
            'date.required' => 'កាលបរិច្ឆេទតម្រូវឱ្យបញ្ចូល។',
            'status.required' => 'ស្ថានភាពវត្តមានតម្រូវឱ្យបញ្ចូល។',
        ]);

        AttendanceRecord::create([
            'course_offering_id' => $request->input('course_offering_id'),
            'student_user_id' => $request->input('student_user_id'),
            'date' => $request->input('date'),
            'status' => $request->input('status'),
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->route('professor.manage-attendance', ['offering_id' => $request->input('course_offering_id')])
            ->with('success', __('កំណត់ត្រាវត្តមានត្រូវបានបន្ថែមដោយជោគជ័យ។'));
    }

    /**
     * Update the specified attendance record in storage.
     */
    public function updateAttendance(Request $request, AttendanceRecord $attendance)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'student_user_id' => [
                'required',
                Rule::exists('student_course_enrollments', 'student_user_id')
                    ->where('course_offering_id', $request->course_offering_id),
            ],
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,permission',
            'remarks' => 'nullable|string|max:255',
        ], [
            'student_user_id.required' => 'អត្តសញ្ញាណសិស្សតម្រូវឱ្យបញ្ចូល។',
            'student_user_id.exists' => 'អត្តសញ្ញាណសិស្សមិនមានឈ្មោះក្នុងបញ្ជីរៀននៃវគ្គសិក្សានេះទេ។',
            'course_offering_id.required' => 'អត្តសញ្ញាណវគ្គសិក្សាតម្រូវឱ្យបញ្ចូល។',
            'date.required' => 'កាលបរិច្ឆេទតម្រូវឱ្យបញ្ចូល។',
            'status.required' => 'ស្ថានភាពវត្តមានតម្រូវឱ្យបញ្ចូល។',
        ]);

        $attendance->update($request->only(['course_offering_id', 'student_user_id', 'date', 'status', 'remarks']));

        return redirect()->route('professor.manage-attendance', ['offering_id' => $attendance->course_offering_id])
            ->with('success', __('កំណត់ត្រាវត្តមានត្រូវបានកែប្រែដោយជោគជ័យ។'));
    }

    /**
     * Remove the specified attendance record from storage.
     */
    public function destroyAttendance(AttendanceRecord $attendance)
    {
        $courseOfferingId = $attendance->course_offering_id;
        $attendance->delete();

        return redirect()->route('professor.manage-attendance', ['offering_id' => $courseOfferingId])
            ->with('success', __('កំណត់ត្រាវត្តមានត្រូវបានលុបដោយជោគជ័យ។'));
    }

    // /**
    //      * Verify professor's location and check-in.
    //      */
    //     public function verifyLocation(Request $request)
    //     {
    //         $request->validate([
    //             'course_offering_id' => 'required|exists:course_offerings,id',
    //             'session_id' => 'required|integer',
    //             'lat' => 'required|numeric|between:-90,90',
    //             'lng' => 'required|numeric|between:-180,180',
    //         ]);

    //         $schoolLat = config('services.nmu.lat', env('NMU_LAT', 13.57952292));
    //         $schoolLng = config('services.nmu.lng', env('NMU_LNG', 102.92898894));
    //         $allowedRadius = config('services.nmu.radius', env('NMU_RADIUS', 100));

    //         $professorId = auth()->id();
    //         $now = Carbon::now('Asia/Phnom_Penh');
    //         $today = $now->toDateString();

    //         $exists = AttendanceProfessor::where([
    //             'professor_id' => $professorId,
    //             'course_offering_id' => $request->course_offering_id,
    //             'verified_date' => $today,
    //             'session_id' => $request->session_id,
    //         ])->exists();

    //         if ($exists) {
    //             return response()->json([
    //                 'success' => true,
    //                 'already_checked_in' => true,
    //                 'message' => 'លោកគ្រូបានចុះវត្តមានសម្រាប់ម៉ោងនេះរួចរាល់ហើយ!'
    //             ]);
    //         }

    //         $distance = $this->calculateDistance($request->lat, $request->lng, $schoolLat, $schoolLng);

    //         if ($distance > $allowedRadius) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'លោកគ្រូនៅឆ្ងាយពីសាលាពេកហើយ! ចម្ងាយបច្ចុប្បន្ន៖ ' . round($distance) . ' ម៉ែត្រ។ មកឱ្យជិតសិនលោកគ្រូ!'
    //             ], 403);
    //         }

    //         AttendanceProfessor::create([
    //             'professor_id' => $professorId,
    //             'course_offering_id' => $request->course_offering_id,
    //             'session_id' => $request->session_id,
    //             'verified_date' => $today,
    //             'lat' => $request->lat,
    //             'lng' => $request->lng,
    //             'verified_at' => $now,
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'already_checked_in' => false,
    //             'distance' => round($distance),
    //             'message' => 'ចុះវត្តមានបានសម្រេច!'
    //         ]);
    //     }

    /**
     * Verify professor's location and check-in (កែប្រែថ្មី)
     */
    public function verifyLocation(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'session_id' => 'required|integer',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $professorId = auth()->id();
        $now = Carbon::now('Asia/Phnom_Penh');
        $today = $now->toDateString();

        // === CHECK មុនគេថាបាន Check-in រួចហើយឬនៅ ===
        $existing = AttendanceProfessor::where([
            'professor_id' => $professorId,
            'course_offering_id' => $request->course_offering_id,
            'session_id' => $request->session_id,
        ])->whereDate('verified_at', $today)->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'already_checked_in' => true,
                'message' => 'លោកគ្រូបានចុះវត្តមានរួចរាល់ហើយ!',
            ]);
        }

        // គណនាចម្ងាយ
        $schoolLat = config('services.nmu.lat', env('NMU_LAT', 13.57952292));
        $schoolLng = config('services.nmu.lng', env('NMU_LNG', 102.92898894));
        $allowedRadius = config('services.nmu.radius', env('NMU_RADIUS', 100));

        $distance = $this->calculateDistance($request->lat, $request->lng, $schoolLat, $schoolLng);

        if ($distance > $allowedRadius) {
            return response()->json([
                'success' => false,
                'message' => 'លោកគ្រូនៅឆ្ងាយពីសាលាពេក! ចម្ងាយបច្ចុប្បន្ន៖ '.round($distance).' ម៉ែត្រ។',
            ], 403);
        }

        // បង្កើត Check-in តែលើកដំបូងប៉ុណ្ណោះ
        AttendanceProfessor::create([
            'professor_id' => $professorId,
            'course_offering_id' => $request->course_offering_id,
            'session_id' => $request->session_id,
            'verified_date' => $today,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'verified_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'already_checked_in' => false,
            'distance' => round($distance),
            'message' => 'ចុះវត្តមានបានសម្រេច!',
        ]);
    }

    /**
     * Precheck ដើម្បីមើលថាបាន Check-in រួចហើយឬនៅ
     */
    public function precheck(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'session_id' => 'required|integer',
        ]);

        $exists = AttendanceProfessor::where([
            'professor_id' => auth()->id(),
            'course_offering_id' => $request->course_offering_id,
            'session_id' => $request->session_id,
        ])->whereDate('verified_at', Carbon::now('Asia/Phnom_Penh')->toDateString())->exists();

        return response()->json(['checked_in' => $exists]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // public function precheck(Request $request)
    // {
    //     $request->validate([
    //         'course_offering_id' => 'required|exists:course_offerings,id',
    //         'session_id' => 'required|integer',
    //     ]);

    //     $exists = AttendanceProfessor::where([
    //         'professor_id' => auth()->id(),
    //         'course_offering_id' => $request->course_offering_id,
    //         'verified_date' => Carbon::now('Asia/Phnom_Penh')->toDateString(),
    //         'session_id' => $request->session_id,
    //     ])->exists();

    //     return response()->json(['checked_in' => $exists]);
    // }

    /**
     * Display professor's attendance history
     */
    public function history()
    {
        $attendances = AttendanceProfessor::with(['courseOffering.course', 'courseOffering.targetPrograms'])
            ->where('professor_id', auth()->id())
            ->orderBy('verified_at', 'desc')
            ->paginate(15);

        return view('professor.attendance.history', compact('attendances'));
    }
}
