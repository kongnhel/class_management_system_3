<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        @php
            $today = now()->startOfDay();
            $status = match(true) {
                $today->lt($courseOffering->start_date) => 'upcoming',
                $today->gt($courseOffering->end_date) => 'expired',
                default => 'active',
            };
            $enrollmentCount = $courseOffering->studentCourseEnrollments->count();
        @endphp
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-24 pt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.manage-course-offerings') }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                            <i class="fas fa-arrow-left text-white"></i>
                        </a>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                                <i class="fas fa-book-open text-emerald-300 text-xl"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h2 class="text-3xl font-bold tracking-tight">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</h2>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold {{ match($status) { 'active' => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30', 'upcoming' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30', default => 'bg-red-500/20 text-red-300 border border-red-500/30' } }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ match($status) { 'active' => 'bg-emerald-400', 'upcoming' => 'bg-amber-400', default => 'bg-red-400' } }}"></span>
                                        {{ match($status) { 'active' => __('active'), 'upcoming' => __('upcoming'), default => __('expired') } }}
                                    </span>
                                </div>
                                <p class="text-slate-400 text-sm">{{ $courseOffering->semester }} / {{ $courseOffering->academic_year }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.edit-course-offering', $courseOffering->id) }}" class="flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg transition-all">
                            <i class="fas fa-edit"></i> <span>{{ __('edit_2') }}</span>
                        </a>
                        <a href="{{ route('admin.manage-course-offerings') }}" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all">
                            <i class="fas fa-arrow-left"></i> <span>{{ __('go_back') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-12 relative z-10">
            {{-- Stats Row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-users text-emerald-500"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $enrollmentCount }}</p>
                            <p class="text-xs text-gray-500">{{ __('enrolled_students') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-user-tie text-emerald-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 truncate max-w-[120px]">{{ $courseOffering->lecturer->name ?? __('not_set') }}</p>
                            <p class="text-xs text-gray-500">{{ __('professor') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                            <i class="fas fa-calendar text-amber-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $courseOffering->semester }}</p>
                            <p class="text-xs text-gray-500">{{ $courseOffering->academic_year }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                            <i class="fas fa-chair text-purple-500"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $courseOffering->capacity }}</p>
                            <p class="text-xs text-gray-500">{{ __('capacity') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Course Info + Lecturer --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Course Info --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <i class="fas fa-book text-emerald-500 text-sm"></i>
                            </div>
                            {{ __('course_information') }}
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('course_name') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->course->title_km }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('semester') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->semester }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('academic_year') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->academic_year }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('capacity') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->capacity }} {{ __('students') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('start_date') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ \Carbon\Carbon::parse($courseOffering->start_date)->format('d/m/Y') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('end_date') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ \Carbon\Carbon::parse($courseOffering->end_date)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Student List --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                                    <i class="fas fa-list text-emerald-500 text-sm"></i>
                                </div>
                                {{ __('student_list') }}
                            </h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700">{{ $enrollmentCount }} {{ __('students_2') }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('name') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('email') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('registration_date') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse($courseOffering->studentCourseEnrollments as $index => $enrollment)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-gray-900 text-sm">{{ $enrollment->student->name ?? '-' }}</div>
                                            @if($enrollment->student->profile)
                                            <div class="text-xs text-gray-500">{{ $enrollment->student->profile->full_name_km ?? '' }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $enrollment->student->email ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($enrollment->enrollment_date)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold {{ $enrollment->status === 'enrolled' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                                {{ $enrollment->status === 'enrolled' ? __('register') : $enrollment->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center">
                                            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-user-slash text-gray-300 text-2xl"></i>
                                            </div>
                                            <p class="text-gray-400 text-sm">{{ __('no_enrolled_students') }}</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Professor Attendance History --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                                    <i class="fas fa-clipboard-check text-blue-500 text-sm"></i>
                                </div>
                                {{ __('lecturer_attendance_history') }}
                            </h3>
                            @php
                                $totalRecords = $attendanceRecords->flatten()->count();
                                $totalDates = $attendanceRecords->count();
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700">{{ $totalDates }} {{ __('days') }} | {{ $totalRecords }} {{ __('records') }}</span>
                        </div>

                        @if($attendanceRecords->isEmpty())
                            <div class="text-center py-12">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-clipboard text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-gray-400 text-sm">{{ __('no_attendance_records_yet') }}</p>
                            </div>
                        @else
                            <div class="space-y-4 max-h-[600px] overflow-y-auto">
                                @foreach($attendanceRecords as $date => $records)
                                    @php
                                        $presentCount = $records->where('status', 'present')->count();
                                        $absentCount = $records->where('status', 'absent')->count();
                                        $lateCount = $records->where('status', 'late')->count();
                                        $permissionCount = $records->where('status', 'permission')->count();
                                        $dayTotal = $records->count();
                                        $dayPercent = $dayTotal > 0 ? round((($presentCount + $lateCount) / $dayTotal) * 100) : 0;
                                    @endphp
                                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                                        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between cursor-pointer hover:bg-gray-100 transition-colors" onclick="this.parentElement.querySelector('.attendance-detail').classList.toggle('hidden')">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center">
                                                    <i class="fas fa-calendar-day text-gray-500"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <div class="flex items-center gap-2 text-xs">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-bold">
                                                        <i class="fas fa-check-circle"></i> {{ $presentCount }}
                                                    </span>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-bold">
                                                        <i class="fas fa-clock"></i> {{ $lateCount }}
                                                    </span>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold">
                                                        <i class="fas fa-file-alt"></i> {{ $permissionCount }}
                                                    </span>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-bold">
                                                        <i class="fas fa-times-circle"></i> {{ $absentCount }}
                                                    </span>
                                                </div>
                                                <span class="text-xs font-bold {{ $dayPercent >= 75 ? 'text-emerald-600' : ($dayPercent >= 50 ? 'text-amber-600' : 'text-rose-600') }}">{{ $dayPercent }}%</span>
                                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="attendance-detail hidden">
                                            <table class="w-full text-sm">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">#</th>
                                                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">{{ __('student_name') }}</th>
                                                        <th class="px-4 py-2 text-center text-xs font-bold text-gray-500">{{ __('status') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @foreach($records as $i => $record)
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-4 py-2 text-gray-500">{{ $i + 1 }}</td>
                                                            <td class="px-4 py-2 font-medium text-gray-900">{{ $record->student->studentProfile->full_name_km ?? $record->student->name }}</td>
                                                            <td class="px-4 py-2 text-center">
                                                                @php
                                                                    $statusColors = [
                                                                        'present' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                                        'late' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                                        'permission' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                                        'absent' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                                    ];
                                                                    $statusLabels = [
                                                                        'present' => __('attendance'),
                                                                        'permission' => __('permission_2'),
                                                                        'absent' => __('absent'),
                                                                    ];
                                                                @endphp
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold border {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                                                    {{ $statusLabels[$record->status] ?? $record->status }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column: Lecturer + Department & Generation + Schedules --}}
                <div class="space-y-6">
                    {{-- Lecturer --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <i class="fas fa-user-tie text-emerald-500 text-sm"></i>
                            </div>
                            {{ __('professor') }}
                        </h3>
                        <div class="space-y-3">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('name') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->lecturer->name ?? __('not_set') }}</p>
                            </div>
                            @if($courseOffering->lecturer->profile)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('khmer_name') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->lecturer->profile->full_name_km ?? '-' }}</p>
                            </div>
                            @endif
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('email') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->lecturer->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Department & Generation --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-amber-500 text-sm"></i>
                            </div>
                            {{ __('enrolled_students') }}
                        </h3>
                        <div class="space-y-2">
                            @if($courseOffering->department)
                            <div class="flex items-center justify-between bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                                <span class="font-semibold text-emerald-800 text-sm">{{ $courseOffering->department->name_km }}</span>
                                <span class="text-xs bg-emerald-200 text-emerald-800 px-2.5 py-0.5 rounded-lg font-bold">G{{ $courseOffering->generation }}</span>
                            </div>
                            @else
                            <p class="text-gray-400 text-sm italic text-center py-4">{{ __('no_schedules_assigned_yet') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Schedules --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-purple-500 text-sm"></i>
                            </div>
                            {{ __('class_schedule') }}
                        </h3>
                        <div class="space-y-2">
                            @forelse($courseOffering->schedules as $schedule)
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-700">{{ substr($schedule->day_of_week, 0, 3) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $schedule->day_of_week }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                    {{ $schedule->room->room_number ?? '-' }}
                                </span>
                            </div>
                            @empty
                            <p class="text-gray-400 text-sm italic text-center py-4">{{ __('no_schedule_yet') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
