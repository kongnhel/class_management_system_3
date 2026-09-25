<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 no-print px-2 font-['Battambang']">
            <div>
                <h2 class="font-black text-2xl md:text-3xl text-slate-900 leading-tight text-center md:text-left">
                    {{ __('class_schedule') }}
                </h2>
                <p class="text-sm text-slate-500 font-medium mt-1 text-center md:text-left">{{ __('review_and_manage_your_class_schedule') }}</p>
            </div>
            
            <div class="flex gap-2 w-full md:w-auto">
                <button onclick="window.print()" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all text-xs">
                    <i class="fas fa-print mr-2"></i> {{ __('print_2') }}
                </button>
            </div>
        </div>
    </x-slot>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&display=swap" rel="stylesheet">

    <style>
        :root { 
            --font-header: 'Moul', serif; 
            --font-body: 'Battambang', system-ui, sans-serif; 
        }

        .a4-paper {
            background: white;
            font-family: var(--font-body);
            color: black;
            width: 100%; /* Responsive by default */
            max-width: 297mm; /* Limit on Desktop */
            min-height: auto;
            padding: 5mm;
            margin: 10px auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
            display: block; 
        }

        @media (min-width: 1024px) {
            .a4-paper {
                width: 297mm;
                padding: 10mm 15mm;
                margin: 20px auto;
            }
        }

        @media print {
            @page { 
                size: A4 landscape; 
                margin: 5mm;
            }
            body { 
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact; 
                zoom: 85%; 
            }
            .no-print { display: none !important; } 
            .a4-paper { 
                margin: 0 !important;
                box-shadow: none !important;
                width: 100% !important;
                height: auto !important; 
                min-height: auto !important;
                padding: 0 !important;
            }
        }

        /* --- Header Layout --- */
        .header-layout {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
            text-align: center;
        }

        @media (min-width: 768px) {
            .header-layout {
                display: grid;
                grid-template-columns: 30% 40% 30%;
                text-align: center;
            }
        }
        
        .header-col { display: flex; flex-direction: column; align-items: center; }
        .font-moul { font-family: var(--font-header) !important; font-weight: normal; }
        .text-emerald-custom { color: #2a58ad; }
        .header-logo img { width: 70px; height: auto; margin-bottom: 5px; }
        .header-line img { width: 100px; height: auto; margin-top: 5px; }
        
        @media (min-width: 768px) {
            .header-logo img { width: 85px; }
            .header-line img { width: 120px; }
        }

        .header-title-km { font-size: 10pt; line-height: 1.4; }
        .header-kingdom { font-size: 11pt; line-height: 1.4; color: black; }

        .schedule-info { text-align: center; margin-bottom: 15px; }
        .schedule-info h1 { font-size: 11pt; margin: 5px 0; color: black; }
        .schedule-info p { font-size: 9pt; font-weight: bold; margin: 2px 0; }

        /* --- Table Styling --- */
        .table-container { 
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
            font-family: var(--font-body);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .custom-table { width: 100%; border-collapse: collapse; border: 1.5pt solid black; min-width: 600px; }
        .custom-table th, .custom-table td { 
            border: 1pt solid black; padding: 4px; 
            text-align: center; vertical-align: middle; 
            font-size: 8.5pt; line-height: 1.3;
        }

        @media (min-width: 768px) {
            .custom-table th, .custom-table td { font-size: 9.5pt; padding: 6px; }
            .schedule-info h1 { font-size: 13pt; }
        }

        .custom-table th { background-color: #f1f5f9 !important; font-weight: normal; }
        .bg-header { background-color: #f8fafc !important; font-weight: bold; }

        .cell-content { display: flex; flex-direction: column; gap: 2px; }
        .cell-subject { font-weight: bold; color: #1e293b; }
        .cell-lecturer { color: #334155; }
        .cell-room { font-weight: bold; color: #08110e; }

        /* --- Footer --- */
        .footer-sigs { 
            display: flex; 
            flex-direction: row; 
            justify-content: space-between;
            margin-top: 20px;
            gap: 10px;
        }
        .sig-block { text-align: center; width: 48%; }
        .sig-title { font-size: 9pt; margin-bottom: 5px; }
        .sig-spacer { height: 50px; }
        .sig-name { font-size: 10pt; font-weight: bold; color: #2a58ad; }
        .sig-date { font-size: 8.5pt; }

        @media (min-width: 768px) {
            .sig-title { font-size: 10pt; }
            .sig-spacer { height: 70px; }
            .sig-date { font-size: 10pt; }
        }

        /* --- Screen document (admin print mirror) --- */
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
        .schedule-doc .doc-cell-subject { font-weight: bold; display: block; margin-bottom: 2px; }
        .schedule-doc .doc-cell-lecturer { display: block; margin-bottom: 2px; }
        .schedule-doc .doc-cell-room { display: block; font-weight: bold; }
        .schedule-doc .f-sigs { display: flex; justify-content: space-between; margin-top: 20px; padding: 0 10px 15px; }
        .schedule-doc .sig-block-left { text-align: center; width: 40%; }
        .schedule-doc .sig-block-right { text-align: center; width: 45%; }
        .schedule-doc .sig-title-moul { font-family: 'Moul', serif; font-size: 11pt; margin-bottom: 4px; font-weight: normal; }
        .schedule-doc .sig-date-kh { font-size: 11pt; font-family: 'Battambang', sans-serif; margin-bottom: 4px; }
        .schedule-doc .sig-spacer { height: 80px; }
    </style>

    {{-- ============================================================ --}}
    {{-- SCREEN VIEW (admin print mirror)                             --}}
    {{-- ============================================================ --}}
    <div class="bg-gray-100 min-h-screen py-4 md:py-10 no-print-bg print:hidden">
        <div class="max-w-6xl mx-auto px-2 sm:px-4">

            @if ($schedules->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-book-open text-gray-300 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-600 mb-2">{{ __('no_schedule_yet') }}</h3>
                </div>
            @else
                @php
                    function toKhmerNumsScr($n) {
                        $khmer = ['០','១','២','៣','៤','៥','៦','៧','៨','៩'];
                        return str_replace(range(0,9), $khmer, $n);
                    }

                    $weekdayMap = ['Monday' => 'ចន្ទ/Monday', 'Tuesday' => 'អង្គារ/Tuesday', 'Wednesday' => 'ពុធ/Wednesday', 'Thursday' => 'ព្រហស្បតិ៍/Thursday', 'Friday' => 'សុក្រ/Friday'];
                    $weekendMap = ['Saturday' => 'សៅរ៍/Saturday', 'Sunday' => 'អាទិត្យ/Sunday'];

                    $allSchedules = $schedules->map(fn($s) => (object)[
                        'day_of_week' => $s->day_of_week,
                        'start_time' => $s->start_time,
                        'end_time' => $s->end_time,
                        'course_title' => $s->courseOffering?->course?->title_km ?? $s->courseOffering?->course?->title_en ?? 'N/A',
                        'lecturer_name' => $s->courseOffering?->lecturer?->name ?? '',
                        'room_number' => $s->room?->room_number ?? '-',
                    ]);

                    $weekdaySchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekdayMap));
                    $weekendSchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekendMap));

                    $weekdayRows = $weekdaySchedules->groupBy(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->sortKeys();
                    $weekendTimeSlots = $weekendSchedules->map(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->unique()->sort();

                    $deptName = $studentDepartment?->name_km;
                    $facultyName = $studentDepartment?->faculty?->name_km;
                    $generationKh = toKhmerNumsScr($generation ?: '');
                    $yearKh = toKhmerNumsScr(date('Y') . '-' . (date('Y') + 1));
                @endphp

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
                        <p>ជំនាន់ទី{{ $generationKh }} {{ $deptName }} {{ $facultyName }} ឆ្នាំសិក្សា {{ $yearKh }}</p>
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
                                                    <span class="doc-cell-subject">{{ $class->course_title }}</span>
                                                    <span class="doc-cell-lecturer">លោក {{ str_replace('Mr. ', '', $class->lecturer_name) }}</span>
                                                    <span class="doc-cell-room">បន្ទប់ {{ $class->room_number }}</span>
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
                                                    <span class="doc-cell-subject">{{ $class->course_title }}</span>
                                                    <span class="doc-cell-lecturer">លោក {{ str_replace('Mr. ', '', $class->lecturer_name) }}</span>
                                                    <span class="doc-cell-room">បន្ទប់ {{ $class->room_number }}</span>
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
    </div>

    {{-- ============================================================ --}}
    {{-- PRINT VIEW (A4 formal timetable - unchanged)                 --}}
    {{-- ============================================================ --}}
    <div id="printable-area" class="a4-paper hidden print:block">

        {{-- 1. HEADER --}}
        <div class="header-layout">
            <div class="header-col header-logo">
                <img id="logoImg" src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo">
                <h3 class="font-moul text-emerald-custom header-title-km">{{ __('national_meanchey_university') }}</h3>
                <h3 class="font-moul text-emerald-custom header-title-km">{{ __('academic_office') }}</h3>
            </div>
            <div class="header-col">
                <h2 class="font-moul header-kingdom">{{ __('kingdom_of_cambodia') }}</h2>
                <h2 class="font-moul header-kingdom">{{ __('nation_religion_king') }}</h2>
                <div class="header-line"><img id="lineImg" src="{{ asset('assets/image/2.png') }}" alt="Line"></div>
            </div>
            <div class="header-col hidden md:flex"></div>
        </div>

        <div class="schedule-info">
            <h1 class="font-moul">{{ __('weekly_analysis_timetable') }}{{ $semester }} <span class="font-sans" style="font-family: var(--font-body)">/Timetable Semester {{ $semesterNum }}</span></h1>
            <p>
                {{ __('generation_2') }} {{ $generation ?: '...' }} 
                @if($studentDepartment) | {{ $studentDepartment->name_km ?? $studentDepartment->name_en }} @endif
                | {{ __('academic_year') }} {{ date('Y') }}-{{ date('Y')+1 }}
            </p>
            <p style="font-weight: normal; margin-top: 5px;">{{ __('starting_from') }} {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</p>
        </div>

        {{-- 2. TABLES --}}
        <div class="table-container">
            @php
                $weekdayMap = ['Monday' => __('mon'), 'Tuesday' => __('tue'), 'Wednesday' => __('wed'), 'Thursday' => __('thu'), 'Friday' => __('fri')];
                $weekendMap = ['Saturday' => __('sat'), 'Sunday' => __('sun')];

                $weekdaySchedules = $schedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekdayMap));
                $weekendSchedules = $schedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekendMap));

                $weekdayRows = $weekdaySchedules->groupBy(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->sortKeys();
                $weekendTimeSlots = $weekendSchedules->map(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->unique()->sort();
            @endphp

            @if($weekdayRows->isNotEmpty())
                <div class="table-responsive">
                    <div style="text-align: left; font-weight: bold; text-decoration: underline; font-size: 10pt; margin-bottom: 5px;">{{ __('shift_mon_fri') }} (Mon-Fri)</div>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th class="font-moul" style="width: 12%;">{{ __('class_hours') }}</th>
                                @foreach($weekdayMap as $label) <th class="font-moul">{{ $label }}</th> @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weekdayRows as $slot => $slots)
                            <tr>
                                <td class="bg-header">{{ $slot }}</td>
                                @foreach($weekdayMap as $dayKey => $label)
                                    <td>
                                        @php $class = $slots->where('day_of_week', $dayKey)->first(); @endphp
                                        @if($class)
                                            <div class="cell-content">
                                                <span class="cell-subject">{{ $class->courseOffering?->course?->title_km ?? $class->courseOffering?->course?->title_en ?? 'N/A' }}</span>
                                                <span class="cell-lecturer">{{ __('key_mr') }} {{ $class->courseOffering?->lecturer?->name ?? 'N/A' }}</span>
                                                <span class="cell-room">{{ __('room') }} {{ $class->room?->room_number ?? '-' }}</span>
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

            @if($weekendSchedules->isNotEmpty())
                <div class="table-responsive">
                    <div style="text-align: left; font-weight: bold; text-decoration: underline; font-size: 10pt; margin-bottom: 5px;">{{ __('shift_sat_sun') }} (Sat-Sun)</div>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th class="font-moul" style="width: 12%;">{{ __('class_day') }}</th>
                                @foreach($weekendTimeSlots as $time) <th class="font-moul">{{ $time }}</th> @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weekendMap as $dayKey => $label)
                            <tr>
                                <td class="bg-header">{{ $label }}</td>
                                @foreach($weekendTimeSlots as $time)
                                    <td>
                                        @php 
                                            $class = $weekendSchedules->filter(function($s) use ($dayKey, $time) {
                                                $slot = \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i');
                                                return $s->day_of_week === $dayKey && $slot === $time;
                                            })->first();
                                        @endphp
                                        @if($class)
                                            <div class="cell-content">
                                                <span class="cell-subject">{{ $class->courseOffering?->course?->title_km ?? $class->courseOffering?->course?->title_en ?? 'N/A' }}</span>
                                                <span class="cell-lecturer">{{ __('key_mr') }} {{ $class->courseOffering?->lecturer?->name ?? 'N/A' }}</span>
                                                <span class="cell-room">{{ __('room') }} {{ $class->room?->room_number ?? '-' }}</span>
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
        </div>

        {{-- 3. FOOTER --}}
        <div class="footer-sigs">
            <div class="sig-block" style="text-align: left; padding-left: 10px;">
                <div class="sig-title" style="font-weight: bold;">{{ __('seen_and_approved') }}</div>
                <div class="sig-title font-moul">{{ __('vice_rector') }}</div>
                <div class="sig-title font-moul">{{ __('deputy_vice_rector') }}</div>
                <div class="sig-spacer"></div>
            </div>

            @php
                function toKhmerNumber($number) {
                    $khmerNumbers = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
                    return str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $khmerNumbers, $number);
                }
                $now = now();
                $khmerMonths = [1 => __('january'), 2 => __('february'), 3 => __('march'), 4 => __('april'), 5 => __('may'), 6 => __('june'), 7 => __('july'), 8 => __('august'), 9 => __('september'), 10 => __('october'), 11 => __('november'), 12 => __('december')];
                $beYear = $now->year + 543; 
                $day = toKhmerNumber($now->format('d'));
                $month = $khmerMonths[$now->month];
                $year = toKhmerNumber($now->year);
                $beYearKh = toKhmerNumber($beYear);
            @endphp

            <div class="sig-block" style="text-align: right; padding-right: 10px;">
                <div class="sig-date">{{ __('day') }}{{ $day }} {{ __('month') }}{{ $month }} {{ __('years') }}{{ $year }} ព.ស {{ $beYearKh }}</div>
                <div class="sig-date">{{ __('banteay_meanchey_day_month_year_20') }}</div>
                <div class="sig-title font-moul" style="margin-top: 5px;">{{ __('head_of_academic_office') }}</div>
                <div class="sig-spacer"></div>
            </div>
        </div>
    </div>

    <script>
        function getBase64Image(img) {
            if (!img) return '';
            var canvas = document.createElement("canvas");
            canvas.width = img.naturalWidth; canvas.height = img.naturalHeight;
            var ctx = canvas.getContext("2d"); ctx.drawImage(img, 0, 0);
            return canvas.toDataURL("image/png");
        }
    </script>
</x-app-layout>
