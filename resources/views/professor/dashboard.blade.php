<x-app-layout>
<div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- ============================================================ --}}
    {{-- HERO BANNER - greeting + key metrics                        --}}
    {{-- ============================================================ --}}
    @php
        $todayClassCount = $todaySchedules->pluck('course_offering_id')->unique()->count();
    @endphp

    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white shadow-lg shadow-emerald-600/20">
        {{-- soft decorative glow --}}
        <div class="absolute -top-24 -right-16 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative px-8 sm:px-12 py-8">

            {{-- greeting row --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                <div class="flex items-center gap-5 min-w-0">
                    @php
                        $profilePic = $user->profile?->profile_picture_url
                            ?? $user->userProfile?->profile_picture_url
                            ?? $user->avatar;
                    @endphp
                    <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 overflow-hidden flex items-center justify-center text-2xl font-bold">
                        @if($profilePic)
                            <img src="{{ $profilePic }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ mb_substr($user->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-emerald-200/80 text-[11px] font-semibold uppercase tracking-widest">{{ now()->translatedFormat('l, d F Y') }}</p>
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 truncate">
                            {{ __('hello') }}, {{ $user->name }}
                        </h1>
                        <p class="text-emerald-100/70 text-sm mt-1">{{ __('lecturer_teaching_dashboard') }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    {{-- telegram connect / connected badge --}}
                    @if(!auth()->user()->telegram_chat_id)
                        <!-- <button type="button"
                            onclick="document.getElementById('telegramEntryModal').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 text-white px-4 py-2 rounded-xl font-semibold text-xs transition-all">
                            <i class="fab fa-telegram-plane"></i>
                            {{ __('connect_telegram') }}
                        </button> -->
                    @else
                        <span class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 text-emerald-100 px-4 py-2 rounded-xl font-semibold text-xs">
                            <i class="fas fa-check-circle"></i>
                            {{ __('telegram_connected') }}
                        </span>
                    @endif

                    @if($currentDeviceTrusted ?? false)
                        <span class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 text-emerald-100 px-4 py-2 rounded-xl font-semibold text-xs"
                              title="{{ __('trusted_devices') }}">
                            <i class="fas fa-shield-halved"></i> {{ __('trusted_devices') }}
                        </span>
                    @else
                        <form method="POST" action="{{ route('professor.security.trusted-device.store') }}"
                              onsubmit="const button = this.querySelector('button[type=submit]'); if (button) { button.disabled = true; button.setAttribute('aria-busy', 'true'); button.setAttribute('aria-disabled', 'true'); button.classList.add('opacity-60', 'cursor-not-allowed'); }">
                            @csrf
                            <button type="submit"
                                aria-busy="false"
                                class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 text-white px-4 py-2 rounded-xl font-semibold text-xs transition-all">
                                <i class="fas fa-shield-halved"></i> {{ __('trust_device') }}
                            </button>
                        </form>
                    @endif
                    <a wire:navigate href="{{ route('professor.security.trusted-devices') }}"
                        class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 text-white rounded-xl transition-all"
                        title="{{ __('Manage trusted devices') }}">
                        <i class="fas fa-gear"></i>
                    </a>
                </div>
            </div>

            {{-- metrics row --}}
            <div class="mt-8 pt-6 border-t border-white/15 grid grid-cols-3 divide-x divide-white/10">
                <div class="pr-5 sm:pr-8">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-200/80">{{ __('classes_today') }}</p>
                    <p class="text-2xl sm:text-3xl font-bold tracking-tight tabular-nums mt-1 leading-none">{{ $todayClassCount }}</p>
                </div>
                <div class="px-5 sm:px-8">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-200/80">{{ __('total_students_2') }}</p>
                    <p class="text-2xl sm:text-3xl font-bold tracking-tight tabular-nums mt-1 leading-none">{{ $totalStudents }}</p>
                </div>
                <div class="pl-5 sm:pl-8">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-200/80">{{ __('attendance_today') }}</p>
                    <p class="text-2xl sm:text-3xl font-bold tracking-tight tabular-nums mt-1 leading-none">{{ $todayAttendanceCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- FLASH / ALERT STRIP                                          --}}
    {{-- ============================================================ --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center gap-3 text-sm font-semibold">
            <i class="fas fa-check-circle text-emerald-500"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(($ungradedSubmissionsCount ?? 0) > 0 || ($pendingAssessments ?? 0) > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-circle-exclamation"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-amber-800">{{ __('pending_tasks') }}</p>
                    <p class="text-xs text-amber-600 mt-0.5">
                        @if($ungradedSubmissionsCount > 0){{ $ungradedSubmissionsCount }} {{ __('ungraded_submissions') }}@endif
                        @if($ungradedSubmissionsCount > 0 && $pendingAssessments > 0) &middot; @endif
                        @if($pendingAssessments > 0){{ $pendingAssessments }} {{ __('ungraded_assignments') }}@endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MAIN GRID: left 2/3 (schedule + quick actions) | right 1/3  --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== LEFT COLUMN ===== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- TODAY'S SCHEDULE --}}
            <section class="bg-white rounded-2xl border border-slate-200/70 overflow-hidden">

                {{-- section header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-calendar-day text-sm"></i>
                        </div>
                        <h2 class="text-[15px] font-bold text-slate-800">{{ __('today_s_teaching_schedule') }}</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs text-slate-500 font-medium">{{ now()->translatedFormat('l') }}</span>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600">
                            {{ $todayClassCount }} {{ __('course') }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    @php $groupedSchedules = $todaySchedules->groupBy('course_offering_id'); @endphp

                    @if($groupedSchedules->isEmpty())
                        {{-- empty state --}}
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-mug-hot text-2xl"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-500">{{ __('no_teaching_hours_today') }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ __('please_rest_or_plan_for_tomorrow') }}</p>
                        </div>
                    @endif

                    <div class="space-y-4">
                        @foreach($groupedSchedules as $offeringId => $schedules)
                            @php
                                $firstSchedule   = $schedules->first();
                                $courseOffering  = $firstSchedule->courseOffering;
                                $startTime       = \Carbon\Carbon::parse($schedules->min('start_time'));
                                $endTime         = \Carbon\Carbon::parse($schedules->max('end_time'));
                                $isCompletedToday= $schedules->contains('is_completed_today', true);
                                $enrolledCount   = $courseOffering->studentCourseEnrollments->count() ?? 0;
                                $departmentName  = $courseOffering->department?->name_km ?? '...';
                                $now             = \Carbon\Carbon::now('Asia/Phnom_Penh');
                                $scanWindowStart = $startTime->copy()->subMinutes(5);
                                $scanWindowEnd   = $endTime->copy()->addMinutes(10);
                                $isScanActive    = $now->gte($scanWindowStart) && $now->lte($scanWindowEnd);
                                $isScanNotStarted= $now->lt($scanWindowStart);
                                $isScanEnded     = $now->gt($scanWindowEnd);

                                // single accent color per status
                                if ($isCompletedToday)    { $accent = 'emerald'; }
                                elseif ($isScanActive)    { $accent = 'emerald'; }
                                elseif ($isScanNotStarted){ $accent = 'amber';   }
                                else                      { $accent = 'slate';   }
                            @endphp

                            {{-- schedule card with accent stripe --}}
                            <div class="flex rounded-2xl border border-slate-200/70 overflow-hidden bg-white hover:shadow-md hover:border-slate-300 transition-all">

                                {{-- accent stripe --}}
                                <div class="w-1.5 flex-shrink-0
                                    @if($accent === 'emerald') bg-emerald-500
                                    @elseif($accent === 'amber')   bg-amber-400
                                    @else                          bg-slate-200 @endif">
                                </div>

                                {{-- card body --}}
                                <div class="flex-1 p-5 min-w-0">

                                    {{-- time + course row --}}
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-4 min-w-0">
                                            {{-- time block --}}
                                            <div class="flex-shrink-0 text-center w-14">
                                                <p class="text-base font-bold text-slate-800 leading-tight">{{ $startTime->format('H:i') }}</p>
                                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">{{ $endTime->format('H:i') }}</p>
                                            </div>
                                            {{-- divider --}}
                                            <div class="w-px self-stretch bg-slate-100 flex-shrink-0"></div>
                                            {{-- course info --}}
                                            <div class="min-w-0">
                                                <h3 class="text-sm font-semibold text-slate-800 leading-snug">
                                                    {{ $courseOffering->course->title_km ?? ($courseOffering->course->title_en ?? $courseOffering->course->name) }}
                                                </h3>
                                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                                    <span class="text-[11px] font-medium bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md">{{ $departmentName }}</span>
                                                    <span class="text-[11px] text-slate-500 font-medium flex items-center gap-1">
                                                        <i class="fas fa-user-graduate text-slate-300"></i> {{ $enrolledCount }} {{ __('students_2') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- status badge --}}
                                        @if($isCompletedToday)
                                            <span class="flex-shrink-0 inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <i class="fas fa-check-double text-[10px]"></i> {{ __('scanned') }}
                                            </span>
                                        @elseif($isScanActive)
                                            <span class="flex-shrink-0 inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> {{ __('scanning') }}
                                            </span>
                                        @elseif($isScanNotStarted)
                                            <span class="flex-shrink-0 inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">
                                                <i class="fas fa-clock text-[10px]"></i> {{ __('waiting_for_time') }}
                                            </span>
                                        @else
                                            <span class="flex-shrink-0 inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-slate-50 text-slate-500 border border-slate-200">
                                                <i class="fas fa-minus-circle text-[10px]"></i> {{ __('completed') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- room chips --}}
                                    <div class="flex flex-wrap gap-1.5 mt-4">
                                        @foreach($schedules as $sched)
                                            <span class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-100 px-2.5 py-1 rounded-lg text-[11px] font-medium text-slate-500">
                                                <i class="fas fa-door-open text-slate-300 text-[10px]"></i>
                                                {{ __('shift_no') }} {{ $loop->iteration }}
                                                <span class="text-slate-300">·</span>
                                                <span class="font-semibold text-slate-700">{{ $sched->room->room_number ?? __('Online') }}</span>
                                            </span>
                                        @endforeach
                                    </div>

                                    {{-- action button --}}
                                    @if($isCompletedToday)
                                        <button type="button"
                                            onclick="openAttendanceListOnly({{ $courseOffering->id }})"
                                            id="btn-scan-{{ $courseOffering->id }}"
                                            class="mt-4 w-full py-2.5 rounded-xl font-semibold text-xs border-2 border-emerald-400 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-clipboard-list"></i> {{ __('review_attendance') }}
                                        </button>
                                    @elseif($isScanActive)
                                        <div class="mt-4 grid grid-cols-2 gap-2">
                                            <button type="button"
                                                onclick="verifyTeacherLocationBeforeScan({{ $courseOffering->id }}, {{ $firstSchedule->id }})"
                                                id="btn-scan-{{ $courseOffering->id }}"
                                                class="w-full py-2.5 rounded-xl font-semibold text-xs bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors flex items-center justify-center gap-2">
                                                <i class="fas fa-qrcode"></i> {{ __('start_attendance_scan') }}
                                            </button>
                                            <button type="button"
                                                onclick="startOnlineCheckin({{ $courseOffering->id }}, {{ $firstSchedule->id }})"
                                                id="btn-online-{{ $courseOffering->id }}"
                                                class="w-full py-2.5 rounded-xl font-semibold text-xs bg-sky-600 hover:bg-sky-700 text-white shadow-sm transition-colors flex items-center justify-center gap-2">
                                                <i class="fas fa-video"></i> Online check-in
                                            </button>
                                        </div>
                                    @elseif($isScanNotStarted)
                                        <div class="mt-4 w-full py-2.5 rounded-xl font-semibold text-xs bg-amber-50 text-amber-500 border border-amber-200 flex items-center justify-center gap-2 cursor-not-allowed">
                                            <i class="fas fa-clock"></i> {{ __('please_wait_until_the_scheduled_time') }} {{ $startTime->format('H:i') }}
                                        </div>
                                    @else
                                        <div class="mt-4 w-full py-2.5 rounded-xl font-semibold text-xs bg-slate-50 text-slate-500 border border-slate-200 flex items-center justify-center gap-2 cursor-not-allowed">
                                            <i class="fas fa-times-circle"></i> {{ __('scan_window_has_ended') }}
                                        </div>
                                    @endif

                                </div>{{-- /card body --}}
                            </div>{{-- /card --}}
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- QUICK ACTIONS --}}
            <section class="grid grid-cols-3 gap-4">
                <a wire:navigate href="{{ route('professor.my-course-offerings') }}"
                    class="bg-white rounded-2xl border border-slate-200/70 p-6 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/30 active:scale-[0.98] hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5 transition-all group flex flex-col gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ __('my_courses') }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $myCourseOfferings->count() }} {{ __('course') }}</p>
                    </div>
                </a>
                <a wire:navigate href="{{ route('professor.grades.all') }}"
                    class="bg-white rounded-2xl border border-slate-200/70 p-6 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/30 active:scale-[0.98] hover:border-sky-300 hover:shadow-md hover:-translate-y-0.5 transition-all group flex flex-col gap-4">
                    <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ __('total_score') }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ __('view_all_grades') }}</p>
                    </div>
                </a>
                <a wire:navigate href="{{ route('professor.all-attendance') }}"
                    class="bg-white rounded-2xl border border-slate-200/70 p-6 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/30 active:scale-[0.98] hover:border-violet-300 hover:shadow-md hover:-translate-y-0.5 transition-all group flex flex-col gap-4">
                    <div class="w-11 h-11 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ __('total_attendance_2') }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ __('view_all_attendance') }}</p>
                    </div>
                </a>
            </section>
        </div>

        {{-- ===== RIGHT COLUMN ===== --}}
        <div class="space-y-6">

            {{-- AT-RISK STUDENTS --}}
            <section class="bg-white rounded-2xl border border-slate-200/70 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center">
                            <i class="fas fa-triangle-exclamation text-sm"></i>
                        </div>
                        <h2 class="text-[15px] font-bold text-slate-800">{{ __('students_at_risk') }}</h2>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-rose-50 text-rose-500">
                        {{ $atRiskStudents->count() }}
                    </span>
                </div>

                <div class="p-4 space-y-2 max-h-72 overflow-y-auto custom-scrollbar">
                    @forelse($atRiskStudents as $risk)
                        @php
                            $pic = $risk['student']->profile?->profile_picture_url
                                ?? $risk['student']->userProfile?->profile_picture_url
                                ?? $risk['student']->avatar;
                        @endphp
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 hover:bg-rose-50/50 transition-colors border border-slate-100/70">
                            <div class="w-9 h-9 rounded-lg overflow-hidden flex-shrink-0 {{ $pic ? '' : 'bg-rose-100 text-rose-500 flex items-center justify-center' }}">
                                @if($pic)
                                    <img src="{{ $pic }}" alt="{{ $risk['student']->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="font-bold text-xs">{{ Str::substr($risk['student']->name ?? '?', 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-xs text-slate-800 truncate">{{ $risk['student']->name ?? 'N/A' }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $risk['course'] }}</p>
                            </div>
                            <span class="flex-shrink-0 text-[10px] font-semibold px-2 py-1 rounded-md bg-rose-100 text-rose-600">
                                {{ $risk['reason'] }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-check-circle text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-500">{{ __('everything_is_good') }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ __('no_problem_students') }}</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- ANNOUNCEMENTS --}}
            <section class="bg-white rounded-2xl border border-slate-200/70 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                            <i class="fas fa-bullhorn text-sm"></i>
                        </div>
                        <h2 class="text-[15px] font-bold text-slate-800">{{ __('announcements') }}</h2>
                    </div>
                    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-600 text-[10px] font-bold px-2.5 py-1 rounded-lg">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Live
                    </span>
                </div>

                <div class="divide-y divide-slate-100 max-h-[460px] overflow-y-auto custom-scrollbar">
                    @forelse($announcements as $announcement)
                        @php
                            $announcementDate = \Carbon\Carbon::parse($announcement->created_at);
                            $isUnread = is_null($announcement->read_at ?? null);
                        @endphp
                        <div class="px-6 py-4 {{ $isUnread ? 'bg-amber-50/40' : '' }} hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-2 mb-1.5">
                                @if($isUnread)
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>
                                @endif
                                <span class="text-[11px] text-slate-500 font-medium">
                                    {{ $announcementDate->isToday()
                                        ? $announcementDate->format('H:i') . ' · ' . __('today')
                                        : $announcementDate->diffForHumans() }}
                                </span>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-800 leading-snug mb-1">
                                {{ $announcement->title_km ?? ($announcement->title_en ?? __('no_title')) }}
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                {{ $announcement->content_km ?? ($announcement->content_en ?? '') }}
                            </p>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-inbox text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-500">{{ __('nothing_new_yet') }}</p>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>{{-- /right column --}}
    </div>{{-- /main grid --}}
</div>
</div>

{{-- ============================================================ --}}
{{-- TELEGRAM MODAL                                               --}}
{{-- ============================================================ --}}
<div id="telegramEntryModal"
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[9999] p-4"
    style="display:none">
    <div class="bg-white rounded-3xl p-8 w-full max-w-md border border-slate-100 shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#0088cc] flex items-center justify-center">
                    <i class="fab fa-telegram-plane text-xl"></i>
                </div>
                {{ __('connect_telegram') }}
            </h3>
            <button onclick="document.getElementById('telegramEntryModal').style.display='none'"
                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-500 transition-all">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form action="{{ route('professor.update_telegram') }}" method="POST">
            @csrf
            <div class="mb-5 text-xs text-slate-500 leading-relaxed bg-slate-50 p-4 rounded-2xl space-y-2">
                <p class="flex gap-2">
                    <span class="font-bold text-slate-500">{{ __('step_1') }}.</span>
                    {{ __('send_a_message_to') }}
                    <a href="https://t.me/userinfobot" target="_blank" class="text-emerald-600 font-semibold">@userinfobot</a>
                    {{ __('then_copy_your_id') }}
                </p>
                <p class="flex gap-2">
                    <span class="font-bold text-slate-500">{{ __('step_2') }}.</span>
                    {{ __('click') }}
                    <a href="https://t.me/Nmu1_schedule_bot" target="_blank" class="text-amber-600 font-semibold">@Nmu1_schedule_bot</a>
                    {{ __('then_press') }} <span class="text-amber-600 italic font-semibold">START</span>{{ __('period') }}
                </p>
            </div>
            <input type="number" name="telegram_chat_id" required placeholder="{{ __('e_g_584930211') }}"
                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl mb-4 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none text-center text-lg font-mono tracking-widest">
            <div class="flex gap-3">
                <button type="button"
                    onclick="document.getElementById('telegramEntryModal').style.display='none'"
                    class="flex-1 py-3.5 bg-slate-100 rounded-2xl font-semibold text-slate-500 text-sm">
                    {{ __('cancel_2') }}
                </button>
                <button type="submit"
                    class="flex-[2] py-3.5 bg-[#0088cc] text-white rounded-2xl font-semibold text-sm hover:bg-[#0077b5] transition-colors">
                    {{ __('save_2') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Attendance Modal (Alpine.js) --}}
@include('professor.attendance.attendance-modal-alpine')

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --------- Modal toggle helpers ---------
    document.querySelectorAll('[onclick*="telegramEntryModal"]').forEach(el => {
        el.addEventListener('click', () => {
            const modal = document.getElementById('telegramEntryModal');
            modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
        });
    });

    // --------- Helpers ---------
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    }

    async function openAttendanceListOnly(courseOfferingId, scheduleId) {
        window.dispatchEvent(new CustomEvent('open-attendance', { detail: { courseOfferingId, scheduleId, readOnly: true } }));
    }

    async function openAttendanceList(courseOfferingId, scheduleId) {
        window.dispatchEvent(new CustomEvent('open-attendance', { detail: { courseOfferingId, scheduleId, readOnly: false } }));
    }

    async function postJson(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
            body: JSON.stringify(payload)
        });
        const text = await res.text();
        let data = null;
        try { data = JSON.parse(text); } catch (e) {}
        if (!res.ok) { const err = new Error(data?.message || `Server error (${res.status}).`); err.status = res.status; err.data = data; throw err; }
        return data;
    }

    async function checkAttendanceAvailability(courseOfferingId) {
        return await postJson("{{ route('professor.attendance.api.check-availability') }}", { course_offering_id: courseOfferingId });
    }

    async function precheckAttendance(courseOfferingId, sessionId) {
        return await postJson("{{ route('professor.attendance.precheck') }}", { course_offering_id: courseOfferingId, session_id: sessionId });
    }

    async function verifyLocation(courseOfferingId, sessionId, lat, lng) {
        return await postJson("{{ route('professor.verify-location') }}", { course_offering_id: courseOfferingId, session_id: sessionId, lat, lng });
    }

    function getBestLocation(attempts = 3, waitMs = 1500) {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) { reject(new Error('{{ __("your_device_does_not_support_gps") }}')); return; }
            let best = null, count = 0;
            const tryOnce = () => {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const reading = { lat: pos.coords.latitude, lng: pos.coords.longitude, accuracy: pos.coords.accuracy };
                        if (!best || reading.accuracy < best.accuracy) best = reading;
                        count++;
                        if (count >= attempts) resolve(best); else setTimeout(tryOnce, waitMs);
                    },
                    () => reject(new Error('{{ __("please_enable_gps_and_allow_location_access") }}')),
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            };
            tryOnce();
        });
    }

    async function verifyTeacherLocationBeforeScan(courseOfferingId, sessionId) {
        const scanBtn = document.getElementById(`btn-scan-${courseOfferingId}`);
        if (scanBtn) scanBtn.disabled = true;

        Swal.fire({
            title: '{{ __("checking") }}',
            text: '{{ __("please_wait_a_moment") }}',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const availability = await checkAttendanceAvailability(courseOfferingId);
            if (!availability?.available) throw new Error(availability?.message || 'Attendance is not available at this time.');

            const pre = await precheckAttendance(courseOfferingId, sessionId);
            if (pre?.checked_in) { Swal.close(); await openAttendanceList(courseOfferingId, sessionId); if (scanBtn) scanBtn.disabled = false; return; }

            const loc = await getBestLocation(3, 1500);
            const data = await verifyLocation(courseOfferingId, sessionId, loc.lat, loc.lng);
            Swal.close();

            if (data?.success) {
                await Swal.fire({ icon: 'success', title: '{{ __("success_3") }}', text: data.message || '{{ __("attendance_checked_in") }}', confirmButtonColor: '#059669' });
                await openAttendanceList(courseOfferingId, sessionId);
            } else {
                Swal.fire({ icon: 'error', title: '{{ __("failed_2") }}', text: data?.message || '{{ __("invalid_location") }}' });
            }
        } catch (err) {
            Swal.close();
            Swal.fire('{{ __("error_2") }}', err.message || '{{ __("there_is_a_problem") }}', 'error');
        } finally {
            if (scanBtn) scanBtn.disabled = false;
        }
    }

    async function startOnlineCheckin(courseOfferingId, scheduleId) {
        const button = document.getElementById(`btn-online-${courseOfferingId}`);
        if (button) button.disabled = true;

        Swal.fire({
            title: 'Starting online check-in',
            text: 'Please wait a moment...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const data = await postJson("{{ route('professor.attendance.api.online-checkin') }}", {
                course_offering_id: courseOfferingId,
                schedule_id: scheduleId
            });

            await Swal.fire({
                icon: 'success',
                title: 'Online check-in recorded',
                html: `Your attendance was recorded online.<br><small>Session token expires in 30 minutes.</small>`,
                confirmButtonColor: '#0284c7'
            });
        } catch (err) {
            Swal.fire('{{ __("unable_to_check_in") }}', err.message || '{{ __("problem_starting_online_attendance") }}', 'error');
        } finally {
            if (button) button.disabled = false;
        }
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar       { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
</x-app-layout>
