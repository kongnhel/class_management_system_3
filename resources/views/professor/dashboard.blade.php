<x-app-layout>
    <div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-6">

            {{-- =========================================================== --}}
            {{-- HERO BANNER --}}
            {{-- =========================================================== --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-purple-700 text-white shadow-xl shadow-emerald-200/50">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-20 -left-10 w-72 h-72 bg-purple-400/10 rounded-full blur-3xl"></div>

                <div class="relative p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        @php
                            $profilePic = $user->profile?->profile_picture_url
                                ?? $user->userProfile?->profile_picture_url
                                ?? $user->avatar;
                        @endphp
                        <div class="flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 overflow-hidden flex items-center justify-center text-2xl sm:text-3xl font-black">
                            @if($profilePic)
                                <img src="{{ $profilePic }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                {{ mb_substr($user->name, 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <p class="text-emerald-200 text-xs sm:text-sm font-semibold">{{ now()->translatedFormat('l, d F Y') }}</p>
                            <h2 class="text-2xl sm:text-3xl font-black leading-tight mt-0.5">
                                {{ __('សួស្តី') }}, {{ $user->name }}! 👋
                            </h2>
                            <p class="text-emerald-200 text-sm mt-1">{{ __('ផ្ទាំងគ្រប់គ្រងការបង្រៀនរបស់លោកគ្រូ') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                        @if(!auth()->user()->telegram_chat_id)
                            <button type="button" onclick="document.getElementById('telegramEntryModal').classList.remove('hidden')"
                                class="inline-flex items-center gap-2 bg-[#0088cc] hover:bg-[#0077b5] text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-lg transition-all">
                                <i class="fab fa-telegram-plane text-sm"></i>
                                <span>{{ __('ភ្ជាប់ Telegram') }}</span>
                            </button>
                        @else
                            <div class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 text-emerald-100 px-4 py-2.5 rounded-xl font-bold text-xs">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ __('Telegram រួចរាល់') }}</span>
                            </div>
                        @endif

                        @if(!auth()->user()->google_id)
                            <button onclick="linkWithGoogle()" id="btn-link-google" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition-all">
                                <i class="fa-brands fa-google text-sm"></i>
                                <span>{{ __('ភ្ជាប់ Google') }}</span>
                            </button>
                        @else
                            <div class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 text-emerald-100 px-4 py-2.5 rounded-xl font-bold text-xs">
                                <i class="fa-solid fa-circle-check text-sm"></i>
                                <span>{{ __('Google រួចរាល់') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- =========================================================== --}}
            {{-- KEY METRICS GRID --}}
            {{-- =========================================================== --}}
            @php
                $todayClassCount = $todaySchedules->pluck('course_offering_id')->unique()->count();
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 lg:gap-4">
                <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ __('ថ្នាក់ថ្ងៃនេះ') }}</p>
                        <h4 class="text-xl font-black text-gray-800">{{ $todayClassCount }}</h4>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-users"></i></div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ __('និស្សិតសរុប') }}</p>
                        <h4 class="text-xl font-black text-gray-800">{{ $totalStudents }}</h4>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-violet-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-clipboard-check"></i></div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ __('វត្តមានថ្ងៃនេះ') }}</p>
                        <h4 class="text-xl font-black text-gray-800">{{ $todayAttendanceCount ?? 0 }}</h4>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-amber-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-file-signature"></i></div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ __('ការងារដាក់ឱ្យ') }}</p>
                        <h4 class="text-xl font-black text-gray-800">{{ $upcomingAssignments->count() }}</h4>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-rose-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-award"></i></div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ __('ប្រឡង/ឃ្វីស') }}</p>
                        <h4 class="text-xl font-black text-gray-800">{{ $upcomingExams->count() + $upcomingQuizzes->count() }}</h4>
                    </div>
                </div>
            </div>

            {{-- =========================================================== --}}
            {{-- ALERTS / WORK-TO-DO STRIP --}}
            {{-- =========================================================== --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-2xl flex items-center gap-3 text-sm font-bold">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(($ungradedSubmissionsCount ?? 0) > 0 || ($pendingAssessments ?? 0) > 0)
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fas fa-circle-exclamation"></i></div>
                        <div>
                            <p class="text-sm font-bold text-amber-800">{{ __('មានការងាររងសង់') }}</p>
                            <p class="text-xs text-amber-600">
                                @if($ungradedSubmissionsCount > 0){{ $ungradedSubmissionsCount }} {{ __('ការបញ្ជូនមិនទាន់ដាក់ពិន្ទុ') }}@endif
                                @if($ungradedSubmissionsCount > 0 && $pendingAssessments > 0) · @endif
                                @if($pendingAssessments > 0){{ $pendingAssessments }} {{ __('កិច្ចការមិនទាន់វាយតម្លៃ') }}@endif
                            </p>
                        </div>
                    </div>
                    {{-- <a href="{{ route('professor.grades.all') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-xl font-bold text-xs transition-all self-start sm:self-auto">
                        {{ __('វាយតម្លៃឥឡូវ') }} <i class="fas fa-arrow-right text-[10px]"></i>
                    </a> --}}
                </div>
            @endif

            {{-- =========================================================== --}}
            {{-- MAIN TWO-COLUMN LAYOUT --}}
            {{-- =========================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ============ LEFT COLUMN ============ --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Today's Teaching Schedule --}}
                    <section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm"><i class="fas fa-calendar-day"></i></div>
                                <h4 class="text-sm font-bold text-gray-800">{{ __('កាលវិភាគបង្រៀនថ្ងៃនេះ') }}</h4>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-400">{{ now()->translatedFormat('l') }}</span>
                                <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600">{{ $todayClassCount }} {{ __('មុខវិជ្ជា') }}</span>
                            </div>
                        </div>

                        <div class="p-5">
                            @php $groupedSchedules = $todaySchedules->groupBy('course_offering_id'); @endphp

                            @if($groupedSchedules->isEmpty())
                                <div class="text-center py-10">
                                    <div class="w-14 h-14 bg-gray-50 text-gray-300 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-mug-hot text-xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-400">{{ __('មិនមានម៉ោងបង្រៀនថ្ងៃនេះទេ') }}</p>
                                    <p class="text-xs text-gray-300 mt-1">{{ __('សូមសម្រាក ឬរៀបចំផែនការសម្រាប់ថ្ងៃស្អែក!') }}</p>
                                </div>
                            @endif

                            <div class="space-y-4">
                                @foreach($groupedSchedules as $offeringId => $schedules)
                                    @php
                                        $firstSchedule = $schedules->first();
                                        $courseOffering = $firstSchedule->courseOffering;
                                        $startTime = \Carbon\Carbon::parse($schedules->min('start_time'));
                                        $endTime = \Carbon\Carbon::parse($schedules->max('end_time'));
                                        $isCompletedToday = $schedules->contains('is_completed_today', true);
                                        $enrolledCount = $courseOffering->studentCourseEnrollments->count() ?? 0;
                                        $programName = $courseOffering->targetPrograms->first()?->name_km
                                            ?? $courseOffering->course->programs->first()?->name_km
                                            ?? '...';
                                        $now = \Carbon\Carbon::now('Asia/Phnom_Penh');
                                        $scanWindowStart = $startTime->copy()->subMinutes(5);
                                        $scanWindowEnd = $endTime->copy()->addMinutes(10);
                                        $isScanActive = $now->gte($scanWindowStart) && $now->lte($scanWindowEnd);
                                        $isScanNotStarted = $now->lt($scanWindowStart);
                                        $isScanEnded = $now->gt($scanWindowEnd);
                                    @endphp

                                    <div class="rounded-2xl border {{ $isCompletedToday ? 'border-emerald-100 bg-emerald-50/30' : 'border-slate-100 bg-slate-50/30' }} overflow-hidden transition-all hover:shadow-md">

                                        {{-- header row --}}
                                        <div class="p-4 flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3 min-w-0 flex-1">
                                                <div class="text-center bg-{{ $isCompletedToday ? 'emerald' : 'indigo' }}-50 border border-{{ $isCompletedToday ? 'emerald' : 'indigo' }}-100 px-3 py-2 rounded-xl min-w-[70px] flex-shrink-0">
                                                    <p class="text-xs font-black text-{{ $isCompletedToday ? 'emerald' : 'indigo' }}-600">{{ $startTime->format('H:i') }}</p>
                                                    <p class="text-[9px] font-bold text-{{ $isCompletedToday ? 'emerald' : 'indigo' }}-400">{{ __('ដល់') }} {{ $endTime->format('H:i') }}</p>
                                                </div>
                                                <div class="min-w-0">
                                                    <h3 class="font-bold text-gray-800 text-sm leading-tight">{{ $courseOffering->course->title_km ?? ($courseOffering->course->title_en ?? $courseOffering->course->name) }}</h3>
                                                    <div class="flex flex-wrap gap-2 mt-1.5 text-[11px] text-gray-500 font-semibold">
                                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-md">{{ $programName }}</span>
                                                        <span class="inline-flex items-center gap-1"><i class="fas fa-user-graduate text-gray-300"></i> {{ $enrolledCount }} {{ __('នាក់') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($isCompletedToday)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 flex-shrink-0">
                                                    <i class="fas fa-check-double"></i> {{ __('បានស្កែន') }}
                                                </span>
                                            @elseif($isScanActive)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 flex-shrink-0">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> {{ __('កំពុងស្កែន') }}
                                                </span>
                                            @elseif($isScanNotStarted)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200 flex-shrink-0">
                                                    <i class="fas fa-clock"></i> {{ __('រង់ចាំម៉ោង') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 flex-shrink-0">
                                                    <i class="fas fa-times-circle"></i> {{ __('បានបញ្ចប់') }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- room chips --}}
                                        <div class="px-4 pb-3 flex flex-wrap gap-2">
                                            @foreach($schedules as $sched)
                                                <span class="inline-flex items-center gap-1.5 bg-white border border-slate-100 px-2.5 py-1 rounded-lg text-[11px] font-semibold text-gray-600">
                                                    <span class="text-gray-400">{{ __('វេនទី') }} {{ $loop->iteration }}</span>
                                                    <span class="text-gray-300">·</span>
                                                    <i class="fas fa-door-open text-gray-400 text-[10px]"></i>
                                                    <span class="text-{{ $isCompletedToday ? 'emerald' : 'indigo' }}-600 font-bold">{{ $sched->room->room_number ?? 'Online' }}</span>
                                                </span>
                                            @endforeach
                                        </div>

                                        {{-- action --}}
                                        <div class="px-4 pb-4">
                                            @if($isCompletedToday)
                                                <button type="button"
                                                    onclick="openAttendanceListOnly({{ $courseOffering->id }})"
                                                    id="btn-scan-{{ $courseOffering->id }}"
                                                    class="w-full py-2.5 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 bg-white border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50">
                                                    <i class="fas fa-clipboard-list"></i> {{ __('ពិនិត្យវត្តមានឡើងវិញ') }}
                                                </button>
                                            @elseif($isScanActive)
                                                <button type="button"
                                                    onclick="verifyTeacherLocationBeforeScan({{ $courseOffering->id }}, {{ $firstSchedule->id }})"
                                                    id="btn-scan-{{ $courseOffering->id }}"
                                                    class="w-full py-2.5 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm shadow-emerald-100">
                                                    <i class="fas fa-qrcode"></i> {{ __('ចាប់ផ្ដើមស្រង់វត្តមាន') }}
                                                </button>
                                            @elseif($isScanNotStarted)
                                                <div class="w-full py-2.5 rounded-xl font-bold text-xs flex items-center justify-center gap-2 bg-yellow-50 text-yellow-600 border border-yellow-200 cursor-not-allowed">
                                                    <i class="fas fa-clock"></i> {{ __('សូមរង់ចាំដល់ម៉ោង') }} {{ $startTime->format('H:i') }}
                                                </div>
                                            @else
                                                <div class="w-full py-2.5 rounded-xl font-bold text-xs flex items-center justify-center gap-2 bg-slate-50 text-slate-400 border border-slate-200 cursor-not-allowed">
                                                    <i class="fas fa-times-circle"></i> {{ __('ម៉ោងស្កែនបានបញ្ចប់') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    {{-- Quick Actions --}}
                    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <a href="{{ route('professor.my-course-offerings') }}" class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:border-emerald-200 hover:shadow-md transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-book"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-800">{{ __('មុខវិជ្ជារបស់ខ្ញុំ') }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $myCourseOfferings->count() }} {{ __('មុខវិជ្ជា') }}</p>
                        </a>
                        <a href="{{ route('professor.grades.all') }}" class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:border-emerald-200 hover:shadow-md transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-800">{{ __('ពិន្ទុសរុប') }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ __('មើលពិន្ទុទាំងអស់') }}</p>
                        </a>
                        <a href="{{ route('professor.all-attendance') }}" class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:border-violet-200 hover:shadow-md transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-800">{{ __('វត្តមានសរុប') }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ __('មើលវត្តមានទាំងអស់') }}</p>
                        </a>
                    </section>
                </div>

                {{-- ============ RIGHT COLUMN ============ --}}
                <div class="space-y-6">

                    {{-- At-Risk Students --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm"><i class="fas fa-triangle-exclamation"></i></div>
                                <h4 class="text-sm font-bold text-gray-800">{{ __('សិស្សប្រឈមមុខ') }}</h4>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600">{{ $atRiskStudents->count() }}</span>
                        </div>
                        <div class="p-4 space-y-2 max-h-[320px] overflow-y-auto custom-scrollbar">
                            @forelse($atRiskStudents as $risk)
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-rose-50/50 border border-rose-100/60 hover:bg-rose-50 transition-all">
                                    @php
                                        $pic = $risk['student']->profile?->profile_picture_url
                                            ?? $risk['student']->userProfile?->profile_picture_url
                                            ?? $risk['student']->avatar;
                                    @endphp
                                    <div class="w-9 h-9 rounded-lg overflow-hidden flex items-center justify-center flex-shrink-0 {{ $pic ? '' : 'bg-rose-100 text-rose-500' }}">
                                        @if($pic)
                                            <img src="{{ $pic }}" alt="{{ $risk['student']->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="font-black text-xs">{{ Str::substr($risk['student']->name ?? '?', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-sm text-gray-800 truncate">{{ $risk['student']->name ?? 'N/A' }}</p>
                                        <p class="text-[11px] text-gray-400 truncate">{{ $risk['course'] }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-md bg-rose-100 text-rose-600 flex-shrink-0">{{ $risk['reason'] }}</span>
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-check-circle text-lg"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-400">{{ __('ទាំងអស់គ្នាល្អ') }}</p>
                                    <p class="text-xs text-gray-300 mt-0.5">{{ __('មិនមានសិស្សបញ្ហាទេ') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Announcements --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm"><i class="fas fa-bullhorn"></i></div>
                                <h4 class="text-sm font-bold text-gray-800">{{ __('សេចក្តីប្រកាស') }}</h4>
                            </div>
                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-600 text-[10px] font-black px-2 py-1 rounded-md uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Live
                            </span>
                        </div>
                        <div class="p-4 space-y-3 max-h-[500px] overflow-y-auto custom-scrollbar">
                            @forelse($announcements as $announcement)
                                @php
                                    $announcementDate = \Carbon\Carbon::parse($announcement->created_at);
                                    $isUnread = is_null($announcement->read_at ?? null);
                                @endphp
                                <div class="rounded-xl border transition-all {{ $isUnread ? 'bg-amber-50/40 border-amber-100' : 'bg-white border-slate-100' }} hover:shadow-sm">
                                    <div class="p-4">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded {{ $isUnread ? 'bg-amber-100 text-amber-700' : 'bg-slate-50 text-slate-500' }}">
                                                @if($isUnread){{ __('ថ្មី') }}@else{{ __('ចាស់') }}@endif
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-bold whitespace-nowrap">
                                                {{ $announcementDate->isToday() ? $announcementDate->format('H:i') . ' · ' . __('ថ្ងៃនេះ') : $announcementDate->diffForHumans() }}
                                            </span>
                                        </div>
                                        <h5 class="text-xs font-bold text-gray-800 leading-snug mb-1">{{ $announcement->title_km ?? ($announcement->title_en ?? __('គ្មានចំណងជើង')) }}</h5>
                                        <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed">{{ $announcement->content_km ?? ($announcement->content_en ?? '') }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <div class="w-12 h-12 bg-gray-50 text-gray-300 rounded-xl flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-inbox text-lg"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-400">{{ __('មិនទាន់មានអ្វីថ្មីនៅឡើយទេ') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Telegram Modal --}}
    <div id="telegramEntryModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-[9999] p-4">
        <div class="bg-white rounded-3xl p-8 w-full max-w-md border border-slate-100 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-slate-800 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#0088cc] flex items-center justify-center"><i class="fab fa-telegram-plane text-xl"></i></div>
                    {{ __('ភ្ជាប់ Telegram') }}
                </h3>
                <button onclick="document.getElementById('telegramEntryModal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 transition-all">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form action="{{ route('professor.update_telegram') }}" method="POST">
                @csrf
                <div class="mb-5 text-xs text-slate-500 leading-relaxed bg-slate-50 p-4 rounded-2xl space-y-2">
                    <p class="flex gap-2"><span class="font-black text-slate-400">១.</span> ផ្ញើសារទៅ <a href="https://t.me/userinfobot" target="_blank" class="text-emerald-600 font-bold">@userinfobot</a> រួចចម្លងលេខ ID។</p>
                    <p class="flex gap-2"><span class="font-black text-slate-400">២.</span> ចុច <a href="https://t.me/Nmu1_schedule_bot" target="_blank" class="text-amber-600 font-bold">@Nmu1_schedule_bot</a> រួចចុច <span class="text-amber-600 italic font-bold">START</span>។</p>
                </div>
                <input type="number" name="telegram_chat_id" required placeholder="ឧ. 584930211"
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl mb-4 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none text-center text-lg font-mono tracking-widest">
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('telegramEntryModal').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 rounded-2xl font-bold text-slate-500 text-sm">{{ __('បោះបង់') }}</button>
                    <button type="submit" class="flex-[2] py-4 bg-[#0088cc] text-white rounded-2xl font-bold text-sm hover:bg-[#0077b5] transition-all">{{ __('រក្សាទុក') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Attendance Modal (Alpine.js) --}}
    @include('professor.attendance.attendance-modal-alpine')

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Firebase Integration --}}
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        window.linkWithGoogle = () => {
            const btn = document.getElementById('btn-link-google');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> កំពុងដំណើរការ...';
            btn.disabled = true;

            signInWithPopup(auth, provider)
                .then(async (result) => {
                    const user = result.user;
                    const idToken = await user.getIdToken();
                    return fetch('{{ route("user.link-google") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            id_token: idToken
                        })
                    });
                })
                .then(async (res) => {
                    const data = await res.json().catch(() => ({ status: 'error', message: 'មានបញ្ហាក្នុងការទាក់ទងនឹង server។' }));
                    if (data.status === 'linked') {
                        Swal.fire({
                            icon: 'success',
                            title: 'ជោគជ័យ',
                            text: 'គណនី Google ត្រូវបានភ្ជាប់!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                        Swal.fire('បរាជ័យ', data.message || 'មិនអាចភ្ជាប់ Google បានទេ', 'error');
                    }
                })
                .catch((error) => {
                    console.error("Firebase Error:", error.code);
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    let msg = 'មិនអាចភ្ជាប់ Google បានទេ';
                    if (error.code === 'auth/popup-closed-by-user') msg = 'បង្អួចបានបិទមុនពេលភ្ជាប់បានសម្រេច។';
                    else if (error.code === 'auth/popup-blocked') msg = 'Popup ត្រូវបានបិទ។ សូមអនុញ្ញាត popup សម្រាប់ទំព័រនេះ។';
                    else if (error.code === 'auth/unauthorized-domain') msg = 'Domain នេះមិនទាន់បានអនុញ្ញាតនៅក្នុង Firebase Console ទេ។';
                    else if (error.message) msg = error.message;
                    Swal.fire('បរាជ័យ', msg, 'error');
                });
        };
    </script>

    <script>
      // ========= Helpers =========
      function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
      }

      async function openAttendanceListOnly(courseOfferingId) {
        window.dispatchEvent(new CustomEvent('open-attendance', { detail: { courseOfferingId, readOnly: true } }));
      }

      async function openAttendanceList(courseOfferingId) {
        window.dispatchEvent(new CustomEvent('open-attendance', { detail: { courseOfferingId, readOnly: false } }));
      }

      async function postJson(url, payload) {
        const csrf = getCsrfToken();
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify(payload)
        });

        const text = await res.text();
        let data = null;
        try { data = JSON.parse(text); } catch (e) {}

        if (!res.ok) {
          const msg = data?.message || `Server error (${res.status}).`;
          const err = new Error(msg);
          err.status = res.status;
          err.data = data;
          throw err;
        }
        return data;
      }

      async function precheckAttendance(courseOfferingId, sessionId) {
        return await postJson("{{ route('professor.attendance.precheck') }}", {
          course_offering_id: courseOfferingId,
          session_id: sessionId
        });
      }

      async function verifyLocation(courseOfferingId, sessionId, lat, lng) {
        return await postJson("{{ route('professor.verify-location') }}", {
          course_offering_id: courseOfferingId,
          session_id: sessionId,
          lat,
          lng
        });
      }

      function getBestLocation(attempts = 3, waitMs = 1500) {
        return new Promise((resolve, reject) => {
          if (!navigator.geolocation) {
            reject(new Error('ឧបករណ៍លោកគ្រូមិនគាំទ្រ GPS ទេ!'));
            return;
          }
          let best = null;
          let count = 0;
          const tryOnce = () => {
            navigator.geolocation.getCurrentPosition(
              (pos) => {
                const reading = { lat: pos.coords.latitude, lng: pos.coords.longitude, accuracy: pos.coords.accuracy };
                if (!best || reading.accuracy < best.accuracy) best = reading;
                count++;
                if (count >= attempts) resolve(best);
                else setTimeout(tryOnce, waitMs);
              },
              () => reject(new Error('សូមបើក GPS និងអនុញ្ញាត (Allow Location)!')),
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
            title: 'កំពុងពិនិត្យ...',
            text: 'សូមរង់ចាំបន្តិច...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const pre = await precheckAttendance(courseOfferingId, sessionId);

            if (pre?.checked_in) {
                Swal.close();
                await openAttendanceList(courseOfferingId);
                if (scanBtn) scanBtn.disabled = false;
                return;
            }

            const loc = await getBestLocation(3, 1500);
            const data = await verifyLocation(courseOfferingId, sessionId, loc.lat, loc.lng);

            Swal.close();

            if (data?.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'ជោគជ័យ',
                    text: data.message || 'ចុះវត្តមានបានសម្រេច!',
                    confirmButtonColor: '#4f46e5'
                });
                await openAttendanceList(courseOfferingId);
            } else {
                Swal.fire({ icon: 'error', title: 'បរាជ័យ', text: data?.message || 'ទីតាំងមិនត្រឹមត្រូវ' });
            }
        } catch (err) {
            Swal.close();
            Swal.fire('កំហុស', err.message || 'មានបញ្ហា!', 'error');
        } finally {
            if (scanBtn) scanBtn.disabled = false;
        }
      }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</x-app-layout>