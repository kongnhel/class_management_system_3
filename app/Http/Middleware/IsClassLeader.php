<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IsClassLeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $courseOfferingId = $request->route('courseOffering') ?? $request->route('courseOfferingId');

        if (! $courseOfferingId) {
            abort(403, 'មិនអនុញ្ញាត។');
        }

        $isLeader = auth()->check() && DB::table('student_course_enrollments')
            ->where('course_offering_id', $courseOfferingId)
            ->where('student_user_id', auth()->id())
            ->where('is_class_leader', 1)
            ->exists();

        if (! $isLeader) {
            abort(403, 'អ្នកមិនមែនជាប្រធានថ្នាក់សម្រាប់មុខវិជ្ជានេះទេ។');
        }

        return $next($request);
    }
}
