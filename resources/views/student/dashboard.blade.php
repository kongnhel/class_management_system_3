<x-app-layout>
    <div class="bg-[#f8fafc] min-h-screen font-['Battambang'] antialiased">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            {{-- Header Section --}}
            <div class="mb-8 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="text-center lg:text-left">
                    <h3 class="text-3xl sm:text-4xl font-black text-gray-900 leading-tight">
                        {{ __('stu_dashboard_greeting') }} <span class="text-indigo-600">{{ auth()->user()->name }}</span>! 👋
                    </h3>
                    <p class="text-gray-500 font-medium mt-1 mb-6">{{ __('stu_dashboard_subtitle') }}</p>
                    
                    {{-- 🛡️ Google Link Status Card --}}
                    <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4 transition-all hover:shadow-md">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner">
                            <i class="fa-brands fa-google text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm leading-none mb-1">{{ __('stu_account_security') }}</h3>
                            @if(!auth()->user()->google_id)
                                <button onclick="linkWithGoogle()" id="btn-link-google" class="flex items-center gap-2 text-blue-600 hover:text-blue-700 text-[11px] font-black transition-all group">
                                    <i class="fa-solid fa-link group-hover:rotate-45 transition-transform"></i> {{ __('stu_connect_google') }}
                                </button>
                            @else
                                <span class="text-emerald-500 font-bold flex items-center gap-1.5 text-[11px]">
                                    <i class="fa-solid fa-circle-check"></i> {{ __('stu_google_connected') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
                    @if(!auth()->user()->telegram_chat_id)
                        <button type="button" onclick="document.getElementById('telegramEntryModal').classList.remove('hidden')" 
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-[#0088cc] hover:bg-[#0077b5] text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 transition-all transform hover:scale-105 text-sm">
                            <i class="fab fa-telegram-plane text-lg"></i>
                            <span>{{ __('stu_connect_telegram') }}</span>
                        </button>
                    @else
                        <div class="w-full sm:w-auto bg-green-50 text-green-600 border border-green-100 px-5 py-3 rounded-2xl font-bold flex items-center justify-center gap-2 text-sm shadow-sm">
                            <i class="fas fa-check-circle text-lg"></i>
                            <span>{{ __('stu_telegram_connected') }}</span>
                        </div>
                    @endif

                    <a href="{{ route('qr.scanner') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg text-sm transition-all">
                        <i class="fa-solid fa-camera"></i>
                        <span>{{ __('stu_scan_qr') }}</span>
                    </a>

                    <div class="w-full sm:w-auto bg-white text-gray-700 border border-gray-100 px-5 py-3 rounded-2xl font-bold shadow-sm flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-calendar-day text-indigo-500"></i>
                        {{ now()->format('d M, Y') }}
                    </div>
                </div>
            </div>

            {{-- ១. ស្ថិតិវត្តមាន --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-4 rounded-2xl border border-green-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl"><i class="fas fa-user-check"></i></div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase">{{ __('stu_present') }}</p>
                        <h4 class="text-2xl font-black text-gray-800">{{ $totalPresent ?? 0 }}</h4>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-red-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl"><i class="fas fa-user-times"></i></div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase">{{ __('stu_absent') }}</p>
                        <h4 class="text-2xl font-black text-gray-800">{{ $totalAbsent ?? 0 }}</h4>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl"><i class="fas fa-file-contract"></i></div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase">{{ __('stu_permission') }}</p>
                        <h4 class="text-2xl font-black text-gray-800">{{ $totalPermission ?? 0 }}</h4>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-yellow-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl"><i class="fas fa-clock"></i></div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase">{{ __('stu_late') }}</p>
                        <h4 class="text-2xl font-black text-gray-800">{{ $totalLate ?? 0 }}</h4>
                    </div>
                </div>
            </div>

            {{-- ២. ស្ថិតិទូទៅ --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-12">
                @php
                    $stats = [
                        ['label' => __('stu_quizzes'), 'count' => $upcomingQuizzes->count(), 'icon' => 'fa-stopwatch', 'color' => 'indigo'],
                        ['label' => __('stu_assignments'), 'count' => $upcomingAssignments->count(), 'icon' => 'fa-file-signature', 'color' => 'emerald'],
                        ['label' => __('stu_exams'), 'count' => $upcomingExams->count(), 'icon' => 'fa-graduation-cap', 'color' => 'rose'],
                        ['label' => __('stu_study_hours'), 'count' => $upcomingSchedules->count(), 'icon' => 'fa-book-open', 'color' => 'purple'],
                        ['label' => __('stu_courses'), 'count' => $enrolledCourses->count(), 'icon' => 'fa-layer-group', 'color' => 'blue'],
                    ];
                @endphp
                @foreach($stats as $stat)
                    <div class="bg-white p-5 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
                        <div class="w-10 h-10 bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform relative z-10">
                            <i class="fas {{ $stat['icon'] }}"></i>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1 relative z-10">{{ __($stat['label']) }}</p>
                        <h2 class="text-2xl font-black text-gray-900 relative z-10">{{ $stat['count'] }}</h2>
                        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity">
                             <i class="fas {{ $stat['icon'] }} text-7xl"></i>
                        </div>
                    </div>
                @endforeach
            </div>

    

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- ផ្នែកខាងឆ្វេង --}}
                <div class="lg:col-span-2 space-y-12">
                    {{-- កាលវិភាគថ្ងៃនេះ --}}
                    <section>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="h-8 w-1.5 bg-purple-600 rounded-full"></div>
                            <h4 class="text-2xl font-black text-gray-800">{{ __('stu_today_schedule') }}</h4>
                        </div>
                        <div class="grid gap-4">
                            @forelse($upcomingSchedules as $schedule)
                                <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between group hover:border-purple-200 transition-all gap-4">
                                    <div class="flex items-center gap-5">
                                        <div class="text-center bg-purple-50 px-4 py-2 rounded-2xl border border-purple-100 min-w-[80px]">
                                            <p class="text-sm font-black text-purple-600">{{ $schedule->start_time->format('H:i') }}</p>
                                            <p class="text-[10px] font-bold text-purple-400">{{ __('stu_to') }} {{ $schedule->end_time->format('H:i') }}</p>
                                        </div>
                                        <div>
                                            <h5 class="font-black text-gray-800 group-hover:text-purple-600 transition-colors">
                                                {{ $schedule->courseOffering->course->name_km ?? ($schedule->courseOffering->course->title_en ?? '') }}
                                            </h5>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs text-gray-500">
                                                <span><i class="fas fa-door-open text-gray-300"></i> {{ $schedule->room->room_number ?? 'N/A' }}</span>
                                                <span><i class="fas fa-user-tie text-gray-300"></i> {{ $schedule->courseOffering->lecturer->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-gray-50 rounded-[2.5rem] p-10 text-center border-2 border-dashed border-gray-200">
                                    <div class="w-16 h-16 bg-gray-100 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-calendar-times text-3xl"></i>
                                    </div>
                                    <p class="text-gray-400 font-bold">{{ __('stu_no_schedule') }}</p>
                                    <p class="text-xs text-gray-300 mt-1">{{ __('មិនមានកាលវិភាគសម្រាប់ថ្ងៃនេះទេ។') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                                {{-- មុខវិជ្ជាដែលកំពុងសិក្សា (Enrolled Courses) --}}
                    <section>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="h-8 w-1.5 bg-blue-600 rounded-full"></div>
                            <h4 class="text-2xl font-black text-gray-800">{{ __('stu_enrolled_courses') }}</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($enrolledCourses as $course)
                                @php
                                    // រកមើលថាសិស្សជាប្រធានថ្នាក់ឬអត់
                                    // ដោយសារយើង load relation 'studentCourseEnrollments' ដែល filter តែសិស្សម្នាក់នេះ
                                    // ដូច្នេះយកធាតុទី 1 មកឆែក
                                    $myEnrollment = $course->studentCourseEnrollments->first(); 
                                    $isLeader = $myEnrollment ? $myEnrollment->is_class_leader : false;
                                @endphp

                                <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm hover:border-blue-200 transition-all relative group overflow-hidden">
                                    
                                    {{-- 🔥 STATUS BADGE (បង្ហាញនៅជ្រុង) 🔥 --}}
                                    <div class="absolute top-6 right-6 z-10">
                                        @if($course->today_status == 'present')
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200 flex items-center gap-1 shadow-sm">
                                                <i class="fas fa-check-circle"></i> {{ __('stu_present') }}
                                            </span>
                                        @elseif($course->today_status == 'absent')
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                                {{ __('stu_absent') }}
                                            </span>
                                        @elseif($course->today_status == 'late')
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200 shadow-sm">
                                                {{ __('stu_late') }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-50 text-gray-400 border border-gray-100">
                                                {{ __('stu_not_recorded') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex flex-col justify-between h-full">
                                        <div class="mb-6 max-w-[70%]">
                                            <h3 class="font-black text-gray-800 leading-tight text-lg mb-1">
                                                {{ $course->course->title_en ?? ($course->course->name ?? '') }}
                                            </h3>
                                            <p class="text-[10px] text-blue-500 uppercase font-black tracking-widest">
                                                {{ $course->academic_year }} • ឆមាស {{ $course->semester }}
                                            </p>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 mb-6">
                                            <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 border border-gray-100">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ __('សាស្ត្រាចារ្យ') }}</p>
                                                <p class="text-sm font-bold text-gray-700">{{ $course->lecturer->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>

                                        {{-- ប៊ូតុងចូលស្កែន ឬ មើលវត្តមាន --}}
                                        <div class="flex flex-col gap-2">
                                            @if($course->today_status == 'present')
                                                <button disabled class="w-full py-3 rounded-xl font-bold bg-green-50 text-green-600 cursor-default flex items-center justify-center gap-2">
                                                    <i class="fas fa-check"></i> {{ __('បានស្កែនរួចរាល់') }}
                                                </button>
                                            @else
                                                <a href="{{ route('student.scan') }}" class="w-full py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2">
                                                    <i class="fas fa-qrcode"></i> {{ __('ស្កែនវត្តមាន') }}
                                                </a>
                                            @endif

                                            @if($isLeader)
                                                <a href="{{ route('student.leader.attendance', $course->id) }}" 
                                                   class="w-full bg-slate-800 text-white px-4 py-3 rounded-xl text-xs font-bold hover:bg-slate-700 transition-all flex items-center justify-center gap-2">
                                                    <i class="fas fa-clipboard-check"></i> {{ __('គ្រប់គ្រងវត្តមាន (ប្រធានថ្នាក់)') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>


                                    {{-- កម្មវិធីសិក្សា (Curriculum) --}}
                    <section>
                         <div class="flex items-center gap-3 mb-6">
                            <div class="h-8 w-1.5 bg-emerald-600 rounded-full"></div>
                            <h4 class="text-2xl font-black text-gray-800">{{ __('កម្មវិធីសិក្សា') }}</h4>
                        </div>
                         @if ($studentProgram)
                            <div class="bg-gradient-to-r from-emerald-600 to-teal-500 rounded-[2.5rem] p-8 text-white shadow-xl shadow-emerald-100 flex flex-col md:flex-row justify-between items-center gap-6">
                                <div>
                                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-[0.2em] mb-2">{{ __('កម្មវិធីសិក្សាបច្ចុប្បន្ន') }}</p>
                                    <h5 class="text-2xl font-black">{{ $studentProgram->name_km }}</h5>
                                </div>
                                <div class="bg-white/20 px-6 py-3 rounded-2xl backdrop-blur-md border border-white/30 text-center">
                                    <p class="text-xs opacity-90">{{ __('ជំនាន់') }}</p>
                                    <p class="text-xl font-black">{{ $user->generation }}</p>
                                </div>
                            </div>
                        @endif
                    </section>
                                            @if ($studentProgram)
    

                            @if ($availableCoursesInProgram->isNotEmpty())
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach ($availableCoursesInProgram as $courseOffering)
                                        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                                            <div class="mb-6">
                                                <h6 class="font-black text-gray-800 mb-2 text-lg leading-tight">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</h6>
                                                <div class="flex items-center gap-2">
                                                    <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-md">{{ $courseOffering->course->code }}</span>
                                                    <span class="text-xs text-gray-400">|</span>
                                                    <span class="text-xs text-gray-500 italic">{{ $courseOffering->lecturer->name }}</span>
                                                </div>
                                            </div>
                                            <form action="{{ route('student.enroll_self') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="course_offering_id" value="{{ $courseOffering->id }}">
                                                <button class="w-full bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white py-3 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 group">
                                                    <i class="fas fa-plus-circle transition-transform group-hover:rotate-90"></i> {{ __('ចុះឈ្មោះចូលរៀន') }}
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="bg-white p-12 rounded-[2.5rem] border border-dashed border-gray-300 text-center">
                                <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-graduation-cap text-3xl"></i>
                                </div>
                                <p class="text-gray-500 font-bold">{{ __('មិនទាន់មានកម្មវិធីសិក្សា? សូមទាក់ទងរដ្ឋបាល។') }}</p>
                            </div>
                        @endif
                </div>

                {{-- ៤. ផ្នែកខាងស្តាំ (Feed & Notifications) --}}
<div class="space-y-6">
    {{-- Attendance Score Card --}}
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-6 w-1 bg-emerald-600 rounded-full"></div>
            <h4 class="text-xl font-black text-gray-800">{{ __('stu_attendance_score') }}</h4>
        </div>
        <div class="text-center mb-4">
            @php
                $scoreColor = $attendanceScore >= 12 ? 'emerald' : ($attendanceScore >= 8 ? 'yellow' : 'red');
            @endphp
            <div class="w-24 h-24 mx-auto bg-{{ $scoreColor }}-50 rounded-full flex items-center justify-center border-4 border-{{ $scoreColor }}-100 mb-3">
                <span class="text-3xl font-black text-{{ $scoreColor }}-600">{{ $attendanceScore }}</span>
            </div>
            <p class="text-sm font-bold text-gray-400">/ 15</p>
        </div>
        <div class="bg-gray-50 rounded-2xl p-4 space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500 font-bold"><i class="fas fa-user-check text-green-500 mr-1"></i> {{ __('stu_present') }}</span>
                <span class="font-black text-gray-700">{{ $totalPresent ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500 font-bold"><i class="fas fa-user-times text-red-500 mr-1"></i> {{ __('stu_absent') }}</span>
                <span class="font-black text-gray-700">{{ $totalAbsent ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500 font-bold"><i class="fas fa-file-contract text-blue-500 mr-1"></i> {{ __('stu_permission') }}</span>
                <span class="font-black text-gray-700">{{ $totalPermission ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- Feed & Notifications --}}
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm sticky top-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="h-6 w-1 bg-blue-600 rounded-full"></div>
                <h4 class="text-xl font-black text-gray-800">{{ __('ព័ត៌មានថ្មីៗ') }}</h4>
            </div>
            <span class="bg-blue-50 text-blue-600 text-[10px] font-black px-2 py-1 rounded-lg">LIVE</span>
        </div>

        {{-- បន្ថែម max-h និង overflow-y-auto នៅទីនេះ ដើម្បីឱ្យវា Scroll បាន --}}
        <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
            @forelse ($combinedFeed as $item)
                <div id="{{ $item->type }}-{{ $item->id }}" 
                     class="group relative p-5 rounded-[2rem] border transition-all duration-300 {{ $item->is_read ? 'bg-gray-50/50 border-gray-100 opacity-75' : 'bg-white border-blue-100 shadow-md shadow-blue-50/50' }}">
                    
                    <div class="flex gap-5">
                        {{-- Icon --}}
                        <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center {{ $item->type === 'announcement' ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600' }}">
                            <i class="fas {{ $item->type === 'announcement' ? 'fa-bullhorn' : 'fa-bell' }} text-lg"></i>
                        </div>

                        <div class="flex-grow min-w-0">
                            <div class="flex flex-col mb-2">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md {{ $item->type === 'announcement' ? 'bg-emerald-50 text-emerald-700' : 'bg-indigo-50 text-indigo-700' }}">
                                        {{ $item->type === 'announcement' ? __('សេចក្តីជូនដំណឹង') : __('ការជូនដំណឹង') }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-bold">• {{ $item->created_at->diffForHumans() }}</span>
                                </div>
                                <h5 class="text-sm font-black text-gray-800 leading-snug">{{ $item->title }}</h5>
                            </div>

                            <p class="text-[12px] text-gray-500 line-clamp-2 leading-relaxed mb-3">{{ $item->content }}</p>

                            <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center">
                                        <i class="fas fa-user-tie text-[10px] text-slate-400"></i>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-slate-600">{{ $item->sender_name }}</span>
                                </div>

                                @if(!$item->is_read)
                                    <button onclick="markAsRead('{{ $item->type }}', '{{ $item->id }}')" 
                                            class="px-3 py-1 rounded-xl bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-wider hover:bg-blue-600 hover:text-white transition-all">
                                        {{ __('អានរួច') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-500">
                    <div class="w-16 h-16 bg-gray-100 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bell-slash text-3xl"></i>
                    </div>
                    <p class="font-bold text-gray-400">{{ __('មិនមានសេចក្តីប្រកាសថ្មីនៅឡើយទេ។') }}</p>
                    <p class="text-xs text-gray-300 mt-1">{{ __('សូមពិនិត្យមើលនៅពេលក្រោយ។') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    /* បន្ថែម Style បន្តិចដើម្បីឱ្យ Scrollbar មើលទៅស្អាត (លាក់ Scrollbar របៀបទាន់សម័យ) */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>


            </div>
        </div>
    </div>

    {{-- Telegram Modal --}}
    <div id="telegramEntryModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-[9999] p-4">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md border border-slate-100 shadow-2xl">
            <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                <i class="fab fa-telegram-plane text-blue-500"></i> ភ្ជាប់ Telegram
            </h3>
            <form action="{{ route('student.update_telegram') }}" method="POST">
                @csrf
                <div class="mb-6 text-xs text-slate-500 leading-relaxed bg-slate-50 p-4 rounded-2xl">
                    <p>១. ផ្ញើសារទៅ <a href="https://t.me/userinfobot" target="_blank" class="text-blue-600 font-bold">@userinfobot</a></p>
                    <p class="mt-1">២. ចុច START លើ <a href="https://t.me/kong_grade_bot" target="_blank" class="text-blue-600 font-bold">@kong_grade_bot</a></p>
                </div>
                <input type="number" name="telegram_chat_id" required placeholder="បញ្ចូលលេខ Chat ID" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl mb-4 focus:ring-4 focus:ring-blue-500/10 outline-none">
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('telegramEntryModal').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 rounded-2xl font-bold text-slate-500">{{ __('បោះបង់') }}</button>
                    <button type="submit" class="flex-[2] py-4 bg-blue-600 text-white rounded-2xl font-bold">{{ __('រក្សាទុក') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Notification Script --}}
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
                .then((result) => {
                    const user = result.user;
                    fetch('{{ route("user.link-google") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            uid: user.uid,
                            photoURL: user.photoURL
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status === 'linked') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ជោគជ័យ',
                                text: 'គណនី Google ត្រូវបានភ្ជាប់!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => window.location.reload());
                        }
                    });
                })
                .catch((error) => {
                    console.error("Firebase Error:", error.code);
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    Swal.fire('បរាជ័យ', 'មិនអាចភ្ជាប់ Google បានទេ៖ ' + error.message, 'error');
                });
        };
    </script>
</x-app-layout>