<x-app-layout>
    <div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-8">

            {{-- =========================================================== --}}
            {{-- HERO BANNER --}}
            {{-- =========================================================== --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-purple-700 text-white shadow-xl shadow-emerald-200/50">
                {{-- decorative blobs --}}
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-20 -left-10 w-72 h-72 bg-purple-400/10 rounded-full blur-3xl"></div>

                <div class="relative p-6 sm:p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        {{-- avatar --}}
                        @php
                            $profilePic = $user->studentProfile?->profile_picture_url
                                ?? $user->profile?->profile_picture_url
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
                                {{ __('stu_dashboard_greeting') }}, {{ auth()->user()->name }}! 👋
                            </h2>
                            <p class="text-emerald-200 text-sm mt-1">{{ __('stu_dashboard_subtitle') }}</p>
                        </div>
                    </div>

                    {{-- quick actions --}}
                    <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                        @if($studentProgram)
                            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2.5 rounded-xl text-xs font-bold">
                                <i class="fas fa-graduation-cap text-emerald-200"></i>
                                <span class="max-w-[140px] truncate">{{ $studentProgram->name_km }}</span>
                                <span class="bg-white/15 px-2 py-0.5 rounded-md text-[10px]">G{{ $user->generation }}</span>
                            </div>
                        @endif

                        @if(!auth()->user()->telegram_chat_id)
                            <button type="button" onclick="document.getElementById('telegramEntryModal').classList.remove('hidden')"
                                class="inline-flex items-center gap-2 bg-[#0088cc] hover:bg-[#0077b5] text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-lg transition-all">
                                <i class="fab fa-telegram-plane text-sm"></i>
                                <span>{{ __('stu_connect_telegram') }}</span>
                            </button>
                        @else
                            <div class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 text-emerald-100 px-4 py-2.5 rounded-xl font-bold text-xs">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ __('stu_telegram_connected') }}</span>
                            </div>
                        @endif

                        @if(!auth()->user()->google_id)
                            <button onclick="linkWithGoogle()" id="btn-link-google" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white px-4 py-2.5 rounded-xl font-bold text-xs transition-all">
                                <i class="fa-brands fa-google text-sm"></i>
                                <span>{{ __('stu_connect_google') }}</span>
                            </button>
                        @else
                            <div class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 text-emerald-100 px-4 py-2.5 rounded-xl font-bold text-xs">
                                <i class="fa-solid fa-circle-check text-sm"></i>
                                <span>{{ __('stu_google_connected') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- =========================================================== --}}
            {{-- KEY METRICS GRID --}}
            {{-- =========================================================== --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 lg:gap-4">
                {{-- Attendance score --}}
                @php
                    $scoreColor = $attendanceScore >= 12 ? 'emerald' : ($attendanceScore >= 8 ? 'amber' : 'rose');
                    $scorePercent = round(($attendanceScore / 15) * 100);
                @endphp
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center col-span-2 md:col-span-1 lg:col-span-2 row-span-2">
                    <div class="relative w-24 h-24 mb-3">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="#f1f5f9" stroke-width="8"/>
                            <circle cx="50" cy="50" r="42" fill="none" stroke-width="8" stroke-linecap="round"
                                stroke="currentColor"
                                class="text-{{ $scoreColor }}-500"
                                stroke-dasharray="{{ round(2 * pi() * 42) }}"
                                stroke-dashoffset="{{ round(2 * pi() * 42 * (1 - $scorePercent / 100)) }}"
                                style="transition: stroke-dashoffset 0.8s ease;"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-black text-{{ $scoreColor }}-600">{{ $attendanceScore }}</span>
                            <span class="text-[10px] font-bold text-gray-400">/ 15</span>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('stu_attendance_score') }}</p>
                </div>

                {{-- Present --}}
                <div class="bg-white p-4 rounded-2xl border border-green-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-user-check"></i></div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ __('stu_present') }}</p>
                        <h4 class="text-xl font-black text-gray-800">{{ $totalPresent ?? 0 }}</h4>
                    </div>
                </div>

                {{-- Absent --}}
                <div class="bg-white p-4 rounded-2xl border border-rose-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-user-times"></i></div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ __('stu_absent') }}</p>
                        <h4 class="text-xl font-black text-gray-800">{{ $totalAbsent ?? 0 }}</h4>
                    </div>
                </div>

                {{-- Permission --}}
                <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-file-contract"></i></div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ __('stu_permission') }}</p>
                        <h4 class="text-xl font-black text-gray-800">{{ $totalPermission ?? 0 }}</h4>
                    </div>
                </div>

                {{-- Late --}}
                <div class="bg-white p-4 rounded-2xl border border-amber-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-clock"></i></div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ __('stu_late') }}</p>
                        <h4 class="text-xl font-black text-gray-800">{{ $totalLate ?? 0 }}</h4>
                    </div>
                </div>

                {{-- Completed courses --}}
                @if($totalCoursesInProgram > 0)
                <div class="bg-white p-4 rounded-2xl border border-violet-100 shadow-sm flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-check-double"></i></div>
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-400 font-bold uppercase truncate">{{ __('មុខវិជ្ជាបានបញ្ចប់') }}</p>
                        <h4 class="text-xl font-black text-gray-800">{{ $completedCoursesCount ?? 0 }}<span class="text-sm font-bold text-gray-400">/{{ $totalCoursesInProgram }}</span></h4>
                    </div>
                </div>
                @endif
            </div>

            {{-- =========================================================== --}}
            {{-- ACADEMIC PERFORMANCE --}}
            {{-- =========================================================== --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="text-base font-bold text-gray-800">{{ __('សមិទ្ធផលសិក្សា') }}</h4>
                    </div>
                    <a href="{{ route('student.my-grades') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                        {{ __('មើលពិន្ទុទាំងអស់') }} <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {{-- GPA --}}
                        <div class="text-center p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-award text-lg"></i>
                            </div>
                            <p class="text-2xl font-black text-emerald-700">{{ $gpa }}</p>
                            <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-wide mt-1">GPA (4.0)</p>
                        </div>
                        {{-- Average --}}
                        <div class="text-center p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-percent text-lg"></i>
                            </div>
                            <p class="text-2xl font-black text-blue-700">{{ $averageScore }}%</p>
                            <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wide mt-1">{{ __('មធ្យមភាគ') }}</p>
                        </div>
                        {{-- Rank --}}
                        <div class="text-center p-4 bg-purple-50/50 rounded-2xl border border-purple-100">
                            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-trophy text-lg"></i>
                            </div>
                            <p class="text-2xl font-black text-purple-700">{{ $overallRank }}<span class="text-sm font-bold text-purple-400">/{{ $totalClassmates }}</span></p>
                            <p class="text-[10px] font-bold text-purple-500 uppercase tracking-wide mt-1">{{ __('ចំណាត់ថ្នាក់') }}</p>
                        </div>
                        {{-- Grade --}}
                        <div class="text-center p-4 bg-amber-50/50 rounded-2xl border border-amber-100">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-medal text-lg"></i>
                            </div>
                            <p class="text-2xl font-black text-amber-700">{{ $overallGrade }}</p>
                            <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wide mt-1">{{ __('និទ្ទេសរួម') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================== --}}
            {{-- MAIN TWO-COLUMN LAYOUT --}}
            {{-- =========================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ============ LEFT COLUMN ============ --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Today's Schedule (Timeline) --}}
                    <section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <h4 class="text-base font-bold text-gray-800">{{ __('stu_today_schedule') }}</h4>
                            </div>
                            <span class="text-xs font-bold text-gray-400">{{ $todayName }}</span>
                        </div>
                        <div class="p-6">
                            @forelse($upcomingSchedules as $index => $schedule)
                                <div class="flex gap-4 group">
                                    {{-- timeline connector --}}
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xs font-black border border-purple-100 flex-shrink-0">
                                            <span>{{ $schedule->start_time->format('H:i') }}</span>
                                        </div>
                                        @if(!$loop->last)
                                            <div class="w-px flex-1 bg-slate-100 my-1"></div>
                                        @endif
                                    </div>
                                    {{-- content --}}
                                    <div class="flex-1 pb-{{ !$loop->last ? '6' : '0' }}">
                                        <div class="bg-slate-50 group-hover:bg-purple-50/50 rounded-xl p-4 border border-slate-50 group-hover:border-purple-100 transition-all">
                                            <h5 class="font-bold text-gray-800 text-sm group-hover:text-purple-700 transition-colors">
                                                {{ $schedule->courseOffering->course->title_km ?? ($schedule->courseOffering->course->title_en ?? '') }}
                                            </h5>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500">
                                                <span class="inline-flex items-center gap-1.5"><i class="fas fa-clock text-gray-300"></i> {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</span>
                                                <span class="inline-flex items-center gap-1.5"><i class="fas fa-door-open text-gray-300"></i> {{ $schedule->room->room_number ?? 'N/A' }}</span>
                                                <span class="inline-flex items-center gap-1.5"><i class="fas fa-user-tie text-gray-300"></i> {{ $schedule->courseOffering->lecturer->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <div class="w-14 h-14 bg-gray-50 text-gray-300 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-calendar-times text-xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-400">{{ __('stu_no_schedule') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </section>

                    {{-- Enrolled Courses (Today's Classes) --}}
                    <section>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <h4 class="text-base font-bold text-gray-800">{{ __('stu_enrolled_courses') }}</h4>
                            </div>
                            <a href="{{ route('student.my-enrolled-courses') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                                {{ __('មើលទាំងអស់') }} <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        @if($enrolledCourses->isEmpty())
                            <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-10 text-center">
                                <div class="w-14 h-14 bg-gray-50 text-gray-300 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-book-open text-xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-400">{{ __('stu_no_enrolled_courses') }}</p>
                                <p class="text-xs text-gray-300 mt-1">{{ __('មិនមានមុខវិជ្ជាចូលរៀនថ្ងៃនេះទេ។') }}</p>
                            </div>
                        @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($enrolledCourses as $course)
                                @php
                                    $myEnrollment = $course->studentCourseEnrollments->first();
                                    $isLeader = $myEnrollment ? $myEnrollment->is_class_leader : false;
                                @endphp
                                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:border-emerald-200 hover:shadow-md transition-all relative group">
                                    {{-- status badge --}}
                                    <div class="absolute top-4 right-4 z-10">
                                        @if($course->today_status == 'present')
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-green-50 text-green-700 border border-green-100 flex items-center gap-1">
                                                <i class="fas fa-check-circle"></i> {{ __('stu_present') }}
                                            </span>
                                        @elseif($course->today_status == 'absent')
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">{{ __('stu_absent') }}</span>
                                        @elseif($course->today_status == 'late')
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">{{ __('stu_late') }}</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-gray-50 text-gray-400 border border-gray-100">{{ __('stu_not_recorded') }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-start gap-3 mb-4 pr-20">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-gray-800 text-sm leading-tight">{{ $course->course->title_km ?? ($course->course->title_en ?? '') }}</h3>
                                            <p class="text-[10px] text-emerald-500 font-bold uppercase tracking-wider mt-0.5">{{ $course->academic_year }} • ឆមាស {{ $course->semester }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 mb-4 pb-4 border-b border-slate-50">
                                        <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100">
                                            <i class="fas fa-user-tie text-xs"></i>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-600">{{ $course->lecturer->name ?? 'N/A' }}</span>
                                        @if($isLeader)
                                            <span class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100">
                                                <i class="fas fa-star text-[8px]"></i> ប្រធានថ្នាក់
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex gap-2">
                                        @if($course->today_status == 'present')
                                            <button disabled class="flex-1 py-2.5 rounded-xl font-bold text-xs bg-green-50 text-green-600 cursor-default flex items-center justify-center gap-1.5">
                                                <i class="fas fa-check"></i> {{ __('បានស្កែនរួចរាល់') }}
                                            </button>
                                        @else
                                            <a href="{{ route('student.scan') }}" class="flex-1 py-2.5 rounded-xl font-bold text-xs bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm shadow-emerald-100 transition-all flex items-center justify-center gap-1.5">
                                                <i class="fas fa-qrcode"></i> {{ __('ស្កែនវត្តមាន') }}
                                            </a>
                                        @endif

                                        @if($isLeader)
                                            <a href="{{ route('student.leader.attendance', $course->id) }}"
                                               class="py-2.5 px-3 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-800 hover:text-white transition-all flex items-center justify-center gap-1.5 text-xs font-bold" title="{{ __('គ្រប់គ្រងវត្តមាន (ប្រធានថ្នាក់)') }}">
                                                <i class="fas fa-clipboard-check"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </section>

                    {{-- Available Courses for Self-Enrollment --}}
                    @if($studentProgram && $availableCoursesInProgram->isNotEmpty())
                    <section>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <h4 class="text-base font-bold text-gray-800">{{ __('មុខវិជ្ជាដែលអាចចុះឈ្មោះ') }}</h4>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600">{{ $availableCoursesInProgram->count() }}</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($availableCoursesInProgram as $courseOffering)
                                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:border-emerald-200 hover:shadow-md transition-all flex flex-col">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h6 class="font-bold text-gray-800 text-sm leading-tight">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</h6>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="bg-gray-50 text-gray-500 text-[10px] font-bold px-2 py-0.5 rounded-md">{{ $courseOffering->course->code }}</span>
                                                <span class="text-[10px] text-gray-400">{{ $courseOffering->lecturer->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px] text-gray-400 mb-4">
                                        <span class="inline-flex items-center gap-1"><i class="fas fa-users"></i> {{ $courseOffering->student_course_enrollments_count ?? 0 }}/{{ $courseOffering->capacity ?? 0 }}</span>
                                        <span class="inline-flex items-center gap-1"><i class="fas fa-calendar"></i> {{ $courseOffering->academic_year }}</span>
                                    </div>
                                    <form action="{{ route('student.enroll_self') }}" method="POST" class="mt-auto">
                                        @csrf
                                        <input type="hidden" name="course_offering_id" value="{{ $courseOffering->id }}">
                                        <button class="w-full bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white py-2.5 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 group">
                                            <i class="fas fa-plus transition-transform group-hover:rotate-90"></i> {{ __('ចុះឈ្មោះចូលរៀន') }}
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </section>
                    @elseif(!$studentProgram)
                        <div class="bg-white p-8 rounded-2xl border border-dashed border-slate-200 text-center">
                            <div class="w-12 h-12 bg-gray-50 text-gray-300 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-graduation-cap text-xl"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-400">{{ __('មិនទាន់មានកម្មវិធីសិក្សា? សូមទាក់ទងរដ្ឋបាល។') }}</p>
                        </div>
                    @endif
                </div>

                {{-- ============ RIGHT COLUMN ============ --}}
                <div class="space-y-6">

                    {{-- Attendance breakdown --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <h4 class="text-sm font-bold text-gray-800">{{ __('stu_attendance_score') }}</h4>
                            </div>
                            @php
                                $totalRecords = ($totalPresent ?? 0) + ($totalAbsent ?? 0) + ($totalPermission ?? 0) + ($totalLate ?? 0);
                                $presentRate = $totalRecords > 0 ? round((($totalPresent ?? 0) / $totalRecords) * 100) : 0;
                            @endphp
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-{{ $scoreColor }}-50 text-{{ $scoreColor }}-600">{{ $presentRate }}%</span>
                        </div>

                        {{-- progress bar --}}
                        <div class="mb-5">
                            <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                <span class="text-gray-500">{{ __('វត្តមានសរុប') }}</span>
                                <span class="text-{{ $scoreColor }}-600">{{ $attendanceScore }}/15</span>
                            </div>
                            <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-{{ $scoreColor }}-500 rounded-full transition-all duration-700" style="width: {{ $scorePercent }}%"></div>
                            </div>
                        </div>

                        {{-- breakdown --}}
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between py-2 px-3 bg-green-50/50 rounded-lg">
                                <span class="flex items-center gap-2 text-xs font-semibold text-gray-600"><i class="fas fa-user-check text-green-500"></i> {{ __('stu_present') }}</span>
                                <span class="font-black text-gray-800 text-sm">{{ $totalPresent ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 px-3 bg-rose-50/50 rounded-lg">
                                <span class="flex items-center gap-2 text-xs font-semibold text-gray-600"><i class="fas fa-user-times text-rose-500"></i> {{ __('stu_absent') }}</span>
                                <span class="font-black text-gray-800 text-sm">{{ $totalAbsent ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 px-3 bg-emerald-50/50 rounded-lg">
                                <span class="flex items-center gap-2 text-xs font-semibold text-gray-600"><i class="fas fa-file-contract text-emerald-500"></i> {{ __('stu_permission') }}</span>
                                <span class="font-black text-gray-800 text-sm">{{ $totalPermission ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 px-3 bg-amber-50/50 rounded-lg">
                                <span class="flex items-center gap-2 text-xs font-semibold text-gray-600"><i class="fas fa-clock text-amber-500"></i> {{ __('stu_late') }}</span>
                                <span class="font-black text-gray-800 text-sm">{{ $totalLate ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Notifications feed --}}
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <h4 class="text-sm font-bold text-gray-800">{{ __('ព័ត៌មានថ្មីៗ') }}</h4>
                            </div>
                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 text-[10px] font-black px-2 py-1 rounded-md uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live
                            </span>
                        </div>
                        <div class="p-4 space-y-3 max-h-[500px] overflow-y-auto custom-scrollbar">
                            @forelse($combinedFeed as $item)
                                <div id="{{ $item->type }}-{{ $item->id }}"
                                     class="rounded-xl border transition-all {{ $item->is_read ? 'bg-slate-50/50 border-slate-100' : 'bg-white border-emerald-100 shadow-sm' }}">
                                    <div class="p-4">
                                        <div class="flex gap-3">
                                            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center {{ $item->type === 'announcement' ? 'bg-emerald-50 text-emerald-600' : 'bg-emerald-50 text-emerald-600' }}">
                                                <i class="fas {{ $item->type === 'announcement' ? 'fa-bullhorn' : 'fa-bell' }} text-xs"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded {{ $item->type === 'announcement' ? 'bg-emerald-50 text-emerald-700' : 'bg-emerald-50 text-emerald-700' }}">
                                                        {{ $item->type === 'announcement' ? __('សេចក្តីជូនដំណឹង') : __('ការជូនដំណឹង') }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-400 font-bold whitespace-nowrap">{{ $item->created_at->diffForHumans() }}</span>
                                                </div>
                                                <h5 class="text-xs font-bold text-gray-800 leading-snug mb-1">{{ $item->title }}</h5>
                                                <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed mb-2">{{ $item->content }}</p>
                                                <div class="flex items-center justify-between pt-2 border-t border-slate-50">
                                                    <span class="text-[10px] font-bold text-slate-500">{{ $item->sender_name }}</span>
                                                    @if(!$item->is_read)
                                                        <button onclick="markAsRead('{{ $item->type }}', '{{ $item->id }}')"
                                                                class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[10px] font-bold hover:bg-emerald-600 hover:text-white transition-all">
                                                            {{ __('អានរួច') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <div class="w-12 h-12 bg-gray-50 text-gray-300 rounded-xl flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-bell-slash text-lg"></i>
                                    </div>
                                    <p class="font-bold text-sm text-gray-400">{{ __('មិនមានសេចក្តីប្រកាសថ្មីនៅឡើយទេ។') }}</p>
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
            <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                <i class="fab fa-telegram-plane text-emerald-500"></i> ភ្ជាប់ Telegram
            </h3>
            <form action="{{ route('student.update_telegram') }}" method="POST">
                @csrf
                <div class="mb-6 text-xs text-slate-500 leading-relaxed bg-slate-50 p-4 rounded-2xl">
                    <p>១. ផ្ញើសារទៅ <a href="https://t.me/userinfobot" target="_blank" class="text-emerald-600 font-bold">@userinfobot</a></p>
                    <p class="mt-1">២. ចុច START លើ <a href="https://t.me/kong_grade_bot" target="_blank" class="text-emerald-600 font-bold">@kong_grade_bot</a></p>
                </div>
                <input type="number" name="telegram_chat_id" required placeholder="បញ្ចូលលេខ Chat ID" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl mb-4 focus:ring-4 focus:ring-emerald-500/10 outline-none">
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('telegramEntryModal').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 rounded-2xl font-bold text-slate-500">{{ __('បោះបង់') }}</button>
                    <button type="submit" class="flex-[2] py-4 bg-emerald-600 text-white rounded-2xl font-bold">{{ __('រក្សាទុក') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        function markAsRead(itemType, itemId) {
            const url = itemType === 'notification'
                ? `{{ route('student.notifications.read', ':id') }}`.replace(':id', itemId)
                : `{{ route('student.announcements.read', ':id') }}`.replace(':id', itemId);

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: itemId })
            }).then(() => location.reload());
        }
    </script>

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

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</x-app-layout>