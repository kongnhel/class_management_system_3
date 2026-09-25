<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@300;400;700&family=Moul:wght@400&display=swap" rel="stylesheet">

<x-app-layout>
    {{-- Main Container --}}
    <div class="min-h-screen bg-slate-50/80 font-['Battambang'] pb-12 print:bg-white print:pb-0">
        
        {{-- HEADER SECTION --}}
        <div class="bg-white border-b border-slate-200 sticky top-0 z-10 print:hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between py-4 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight leading-none">{{ __('teaching_schedule') }}</h2>
                            <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">{{ __('My Teaching Schedule') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="hidden md:flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 mr-2">
                            <span class="text-xs font-bold text-slate-400 uppercase">{{ $semester }}</span>
                            <div class="h-4 w-px bg-slate-300"></div>
                            <span class="text-sm font-bold text-emerald-600">{{ __('academic_year') }} {{ $academicYear }}</span>
                        </div>
                        <button onclick="window.print()" class="group flex items-center justify-center gap-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl font-bold shadow-sm transition-all text-sm">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"></path></svg>
                            <span>{{ __('print_2') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENT SECTION --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 print:mt-0 print:px-0 print:max-w-none">
            
            {{-- SCREEN VIEW --}}
            <div class="print:hidden">
                @if ($courseOfferings->isEmpty())
                    <div class="flex flex-col items-center justify-center py-24 bg-white rounded-3xl border border-dashed border-slate-300">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800">{{ __('no_teaching_schedule_yet') }}</h3>
                        <p class="text-slate-500 mt-2">{{ __('no_teaching_schedule_yet') }}</p>
                    </div>
                @else
                    @php
                        function toKhmerNumsScr($n) {
                            $khmer = ['០','១','២','៣','៤','៥','៦','៧','៨','៩'];
                            return str_replace(range(0,9), $khmer, $n);
                        }

                        $weekdayMap = ['Monday' => 'ចន្ទ/Monday', 'Tuesday' => 'អង្គារ/Tuesday', 'Wednesday' => 'ពុធ/Wednesday', 'Thursday' => 'ព្រហស្បតិ៍/Thursday', 'Friday' => 'សុក្រ/Friday'];
                        $weekendMap = ['Saturday' => 'សៅរ៍/Saturday', 'Sunday' => 'អាទិត្យ/Sunday'];

                        $allSchedules = collect();
                        foreach ($courseOfferings as $offering) {
                            foreach ($offering->schedules as $schedule) {
                                $allSchedules->push((object)[
                                    'day_of_week' => $schedule->day_of_week,
                                    'start_time' => $schedule->start_time,
                                    'end_time' => $schedule->end_time,
                                    'course_title' => $offering->course?->title_km ?? $offering->course?->title_en ?? 'N/A',
                                    'lecturer_name' => $offering->lecturer?->name ?? '',
                                    'room_number' => $schedule->room?->room_number ?? '-',
                                ]);
                            }
                        }

                        $weekdaySchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekdayMap));
                        $weekendSchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekendMap));

                        $weekdayRows = $weekdaySchedules->groupBy(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->sortKeys();
                        $weekendTimeSlots = $weekendSchedules->map(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->unique()->sort();

                        $firstOffering = $courseOfferings->first();
                        $deptName = $firstOffering?->department?->name_km;
                        $facultyName = $firstOffering?->department?->faculty?->name_km;
                        $generationKh = toKhmerNumsScr($firstOffering?->generation ?? '');
                        $yearKh = toKhmerNumsScr($academicYear ?? '');
                    @endphp

                    {{-- screen document styles (admin print mirror) --}}
                    <style>
                        .schedule-doc .header-print-layout { display: flex; align-items: flex-start; position: relative; width: 100%; margin-bottom: 15px; }
                        .schedule-doc .uni-logo-text { text-align: center; display: flex; flex-direction: column; align-items: center; padding-left: 10px; }
                        .schedule-doc .uni-logo-text img { width: 95px; height: auto; margin: 0 auto 5px auto; }
                        .schedule-doc .uni-logo-text h3 { font-family: 'Moul', serif; font-size: 11pt; color: black; margin: 2px 0; line-height: 1.4; font-weight: normal; white-space: nowrap; }
                        .schedule-doc .kingdom-header { position: absolute; left: 50%; transform: translateX(-50%); text-align: center; top: 0; }
                        .schedule-doc .kingdom-header h2 { font-family: 'Moul', serif; font-size: 13pt; margin: 2px 0; color: black; line-height: 1.4; font-weight: normal; }
                        .schedule-doc .kingdom-header img { width: 130px; height: auto; margin: 4px auto 0 auto; display: block; }
                        .schedule-doc .schedule-title-block { text-align: center; margin-top: 15px; margin-bottom: 20px; }
                        .schedule-doc .schedule-title-block h1 { font-family: 'Moul', serif; font-size: 12pt; margin: 5px 0; color: black; font-weight: normal; }
                        .schedule-doc .schedule-title-block p { font-size: 10.5pt; margin: 3px 0; color: black; line-height: 1.5; }
                        .schedule-doc .specialty-title { text-align: left; font-weight: bold; font-family: 'Battambang', sans-serif; font-size: 11pt; margin-bottom: 6px; text-decoration: underline; text-underline-offset: 3px; }
                        .schedule-doc .matrix-table { width: 100%; border-collapse: collapse; border: 1.5pt solid black; margin-bottom: 20px; }
                        .schedule-doc .matrix-table th, .schedule-doc .matrix-table td { border: 1pt solid black; padding: 6px 4px; text-align: center; vertical-align: middle; color: black; }
                        .schedule-doc .matrix-table th { font-size: 10pt; font-family: 'Battambang', sans-serif; font-weight: bold; background-color: transparent; }
                        .schedule-doc .matrix-table td { font-size: 9.5pt; line-height: 1.4; height: 45px; }
                        .schedule-doc .cell-subject { font-weight: bold; display: block; margin-bottom: 2px; }
                        .schedule-doc .cell-lecturer { display: block; margin-bottom: 2px; }
                        .schedule-doc .cell-room { display: block; font-weight: bold; }
                        .schedule-doc .f-sigs { display: flex; justify-content: space-between; margin-top: 20px; padding: 0 10px 15px; }
                        .schedule-doc .sig-block-left { text-align: center; width: 40%; }
                        .schedule-doc .sig-block-right { text-align: center; width: 45%; }
                        .schedule-doc .sig-title-moul { font-family: 'Moul', serif; font-size: 11pt; margin-bottom: 4px; font-weight: normal; }
                        .schedule-doc .sig-date-kh { font-size: 11pt; font-family: 'Battambang', sans-serif; margin-bottom: 4px; }
                        .schedule-doc .sig-spacer { height: 80px; }
                    </style>

                    {{-- SCREEN DOCUMENT (admin print mirror) --}}
                    <div class="schedule-doc bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-10">

                        {{-- Header Layout --}}
                        <div class="header-print-layout">
                            <div class="kingdom-header">
                                <h2>{{ __('kingdom_of_cambodia') }}</h2>
                                <h2>{{ __('nation_religion_king') }}</h2>
                                <img src="{{ asset('assets/image/2.png') }}" alt="Line">
                            </div>

                            <div class="uni-logo-text">
                                <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo">
                                <h3>{{ __('national_meanchey_university') }}</h3>
                                <h3>{{ __('academic_office') }}</h3>
                            </div>
                        </div>

                        {{-- Schedule Title --}}
                        <div class="schedule-title-block">
                            <h1>កាលវិភាគប្រចាំឆមាសទី{{ $semesterNum }} / Timetable Semester {{ $semesterNum }}</h1>
                            <p>ជំនាន់ទី{{ $generationKh }} {{ $facultyName }} ឆ្នាំសិក្សា {{ $yearKh }}</p>
                            <p>ចាប់ផ្តើមពីថ្ងៃ................................................................................. វេនសិក្សា ចន្ទ-សុក្រ</p>
                        </div>

                        {{-- Weekday Matrix --}}
                        @if($weekdayRows->isNotEmpty())
                            <div class="specialty-title">ជំនាញ៖ {{ $deptName }}</div>
                            <table class="matrix-table">
                                <thead>
                                    <tr>
                                        <th style="width: 14%;">{{ __('class_hours') }}</th>
                                        @foreach($weekdayMap as $dayLabel)
                                            <th>{{ $dayLabel }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($weekdayRows as $slot => $slots)
                                        <tr>
                                            <td style="font-weight: bold;">{{ $slot }}</td>
                                            @foreach($weekdayMap as $dayKey => $dayLabel)
                                                <td>
                                                    @php $class = $slots->where('day_of_week', $dayKey)->first(); @endphp
                                                    @if($class)
                                                        <span class="cell-subject">{{ $class->course_title }}</span>
                                                        <span class="cell-lecturer">លោក {{ str_replace('Mr. ', '', $class->lecturer_name) }}</span>
                                                        <span class="cell-room">បន្ទប់ {{ $class->room_number }}</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        {{-- Weekend Matrix --}}
                        @if($weekendSchedules->isNotEmpty())
                            <div class="specialty-title">ជំនាញ៖ {{ $deptName }} (សៅរ៍-អាទិត្យ)</div>
                            <table class="matrix-table">
                                <thead>
                                    <tr>
                                        <th style="width: 14%;">{{ __('class_hours') }}</th>
                                        @foreach($weekendTimeSlots as $timeSlot)
                                            <th>{{ toKhmerNumsScr($timeSlot) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($weekendMap as $dayKey => $dayLabel)
                                        <tr>
                                            <td style="font-weight: bold;">{{ $dayLabel }}</td>
                                            @foreach($weekendTimeSlots as $time)
                                                <td>
                                                    @php
                                                        $class = $weekendSchedules->filter(function($s) use ($dayKey, $time) {
                                                            $slot = \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i');
                                                            return $s->day_of_week === $dayKey && $slot === $time;
                                                        })->first();
                                                    @endphp
                                                    @if($class)
                                                        <span class="cell-subject">{{ $class->course_title }}</span>
                                                        <span class="cell-lecturer">លោក {{ str_replace('Mr. ', '', $class->lecturer_name) }}</span>
                                                        <span class="cell-room">បន្ទប់ {{ $class->room_number }}</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        {{-- Footer Signatures --}}
                        <div class="f-sigs">
                            <div class="sig-block-left">
                                <div class="sig-title-moul">{{ __('seen_and_approved') }}</div>
                                <div class="sig-title-moul">{{ __('vice_rector') }}</div>
                                <div class="sig-title-moul" style="margin-top: 0;">{{ __('deputy_vice_rector') }}</div>
                                <div class="sig-spacer"></div>
                            </div>
                            <div class="sig-block-right">
                                <div class="sig-date-kh">ថ្ងៃ........................... ខែ...................... ឆ្នាំ...................... ព.ស ២៥៦...</div>
                                <div class="sig-date-kh">បន្ទាយមានជ័យ ថ្ងៃទី........... ខែ........... ឆ្នាំ២០២...</div>
                                <div class="sig-title-moul" style="margin-top: 8px;">ប្រធាន{{ __('academic_office') }}</div>
                                <div class="sig-spacer"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <style>
                @media print {
                    body { font-family: 'Battambang', 'Khmer OS Battambang', sans-serif !important; }
                    * { font-family: 'Battambang', 'Khmer OS Battambang', sans-serif !important; }
                    .font-moul, [style*="font-family: 'Moul'"] { font-family: 'Moul', serif !important; }
                }
            </style>

            {{-- PRINT VIEW (Matching Student Schedule Print Layout) --}}
            <div class="hidden print:block font-['Battambang']">
                @php
                    $weekdayMap = ['Monday' => __('mon'), 'Tuesday' => __('tue'), 'Wednesday' => __('wed'), 'Thursday' => __('thu'), 'Friday' => __('fri')];
                    $weekendMap = ['Saturday' => __('sat'), 'Sunday' => __('sun')];

                    $allSchedules = collect();
                    foreach ($courseOfferings as $offering) {
                        foreach ($offering->schedules as $schedule) {
                            $allSchedules->push((object)[
                                'day_of_week' => $schedule->day_of_week,
                                'start_time' => $schedule->start_time,
                                'end_time' => $schedule->end_time,
                                'course_title_km' => $offering->course?->title_km ?? '',
                                'course_title_en' => $offering->course?->title_en ?? '',
                                'room_number' => $schedule->room?->room_number ?? '-',
                                'offering_id' => $offering->id,
                                'academic_year' => $offering->academic_year,
                                'semester' => $offering->semester,
                            ]);
                        }
                    }

                    $weekdaySchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekdayMap));
                    $weekendSchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekendMap));

                    $weekdayRows = $weekdaySchedules->groupBy(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->sortKeys();
                    $weekendTimeSlots = $weekendSchedules->map(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->unique()->sort();

                    function toKhmerNumsPrint($n) {
                        $khmer = ['០','១','២','៣','៤','៥','៦','៧','៨','៩'];
                        return str_replace(range(0,9), $khmer, $n);
                    }
                    $now = now();
                    $khmerMonths = [1=>__('january'),2=>__('february'),3=>__('march'),4=>__('april'),5=>__('may'),6=>__('june'),7=>__('july'),8=>__('august'),9=>__('september'),10=>__('october'),11=>__('november'),12=>__('december')];
                    $dayKh = toKhmerNumsPrint($now->format('d'));
                    $monthKh = $khmerMonths[$now->month];
                    $yearKh = toKhmerNumsPrint((string)$now->year);
                @endphp

                {{-- HEADER --}}
                <div style="display: grid; grid-template-columns: 30% 40% 30%; text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px;">
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" style="width: 70px; height: auto; margin-bottom: 5px;">
                        <h3 style="font-family: 'Moul', serif; font-size: 10pt; color: #2a58ad; line-height: 1.4; margin: 0;">{{ __('national_meanchey_university') }}</h3>
                        <h3 style="font-family: 'Moul', serif; font-size: 10pt; color: #2a58ad; line-height: 1.4; margin: 0;">{{ __('academic_office') }}</h3>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <h2 style="font-family: 'Moul', serif; font-size: 11pt; margin: 0;">{{ __('kingdom_of_cambodia') }}</h2>
                        <h2 style="font-family: 'Moul', serif; font-size: 11pt; margin: 0;">{{ __('nation_religion_king') }}</h2>
                        <img src="{{ asset('assets/image/2.png') }}" alt="motto" style="height: 30px; margin-top: 5px;">
                    </div>
                    <div></div>
                </div>

                <div style="text-align: center; margin-bottom: 15px;">
                    <h1 style="font-family: 'Moul', serif; font-size: 13pt; margin: 5px 0;">{{ __('weekly_analysis_timetable') }}{{ $semester }}</h1>
                    <p style="font-size: 9pt; font-weight: bold; margin: 2px 0;">
                        {{ __('academic_year') }} {{ $academicYear }}
                    </p>
                </div>

                {{-- WEEKDAY TABLE --}}
                @if($weekdayRows->isNotEmpty())
                    <div style="margin-bottom: 15px;">
                        <div style="text-align: left; font-weight: bold; text-decoration: underline; font-size: 10pt; margin-bottom: 5px;">{{ __('shift_mon_fri') }} (Mon-Fri)</div>
                        <table style="width: 100%; border-collapse: collapse; border: 1.5pt solid black;">
                            <thead>
                                <tr>
                                    <th style="border: 1pt solid black; padding: 4px; background-color: #f1f5f9; font-family: 'Moul', serif; width: 12%;">{{ __('class_hours') }}</th>
                                    @foreach($weekdayMap as $label)
                                        <th style="border: 1pt solid black; padding: 4px; background-color: #f1f5f9; font-family: 'Moul', serif; font-size: 8.5pt;">{{ $label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weekdayRows as $slot => $slots)
                                <tr>
                                    <td style="border: 1pt solid black; padding: 4px; text-align: center; font-weight: bold; background-color: #f8fafc; font-size: 8.5pt;">{{ $slot }}</td>
                                    @foreach($weekdayMap as $dayKey => $label)
                                        <td style="border: 1pt solid black; padding: 4px; text-align: center; vertical-align: middle; font-size: 8.5pt;">
                                            @php $class = $slots->where('day_of_week', $dayKey)->first(); @endphp
                                            @if($class)
                                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                                    <span style="font-weight: bold; color: #1e293b;">{{ $class->course_title_km }}</span>
                                                    <span style="color: #334155;">{{ __('room') }} {{ $class->room_number }}</span>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- WEEKEND TABLE --}}
                @if($weekendSchedules->isNotEmpty())
                    <div style="margin-bottom: 15px;">
                        <div style="text-align: left; font-weight: bold; text-decoration: underline; font-size: 10pt; margin-bottom: 5px;">{{ __('shift_sat_sun') }} (Sat-Sun)</div>
                        <table style="width: 100%; border-collapse: collapse; border: 1.5pt solid black;">
                            <thead>
                                <tr>
                                    <th style="border: 1pt solid black; padding: 4px; background-color: #f1f5f9; font-family: 'Moul', serif; width: 12%;">{{ __('class_day') }}</th>
                                    @foreach($weekendTimeSlots as $time)
                                        <th style="border: 1pt solid black; padding: 4px; background-color: #f1f5f9; font-family: 'Moul', serif; font-size: 8.5pt;">{{ $time }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weekendMap as $dayKey => $label)
                                <tr>
                                    <td style="border: 1pt solid black; padding: 4px; text-align: center; font-weight: bold; background-color: #f8fafc; font-size: 8.5pt;">{{ $label }}</td>
                                    @foreach($weekendTimeSlots as $time)
                                        <td style="border: 1pt solid black; padding: 4px; text-align: center; vertical-align: middle; font-size: 8.5pt;">
                                            @php
                                                $class = $weekendSchedules->filter(function($s) use ($dayKey, $time) {
                                                    $slot = \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i');
                                                    return $s->day_of_week === $dayKey && $slot === $time;
                                                })->first();
                                            @endphp
                                            @if($class)
                                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                                    <span style="font-weight: bold; color: #1e293b;">{{ $class->course_title_km }}</span>
                                                    <span style="color: #334155;">{{ __('room') }} {{ $class->room_number }}</span>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- FOOTER SIGNATURE --}}
                <div style="display: flex; justify-content: space-between; margin-top: 20px; gap: 10px;">
                    <div style="text-align: left; padding-left: 10px; width: 48%;">
                        <div style="font-size: 9pt; font-weight: bold;">{{ __('seen_and_approved') }}</div>
                        <div style="font-size: 9pt; font-family: 'Moul', serif;">{{ __('vice_rector') }}</div>
                        <div style="font-size: 9pt; font-family: 'Moul', serif;">{{ __('deputy_vice_rector') }}</div>
                        <div style="height: 70px;"></div>
                    </div>
                    <div style="text-align: right; padding-right: 10px; width: 48%;">
                        <div style="font-size: 8.5pt;">{{ __('day') }}{{ $dayKh }} {{ __('month') }}{{ $monthKh }} {{ __('years') }}{{ $yearKh }}</div>
                        <div style="font-size: 9pt; font-family: 'Moul', serif; margin-top: 5px;">ប្រធាន{{ __('academic_office') }}</div>
                        <div style="height: 70px;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
