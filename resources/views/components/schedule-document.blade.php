@props([
    'rows' => [],
    'semesterNum' => '1',
    'generation' => '',
    'academicYear' => '',
    'yearLabel' => '៤',
    'facultyName' => '',
    'deptName' => '',
    'showLecturer' => true,
    'forPrint' => false,
])

@php
    if (! function_exists('schedKhmerNumber')) {
        function schedKhmerNumber($number)
        {
            if (! $number) {
                return '';
            }

            return str_replace(range(0, 9), ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'], (string) $number);
        }
    }

    $slotOf = fn ($r) => \Carbon\Carbon::parse($r->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($r->end_time)->format('H:i');

    $weekdayMap = [
        'Monday' => 'ចន្ទ/Monday',
        'Tuesday' => 'អង្គារ/Tuesday',
        'Wednesday' => 'ពុធ/Wednesday',
        'Thursday' => 'ព្រហស្បតិ៍/Thursday',
        'Friday' => 'សុក្រ/Friday',
    ];
    $weekendMap = ['Saturday' => 'សៅរ៍/Saturday', 'Sunday' => 'អាទិត្យ/Sunday'];

    $all = collect($rows);
    $weekdaySchedules = $all->filter(fn ($r) => array_key_exists($r->day_of_week, $weekdayMap));
    $weekendSchedules = $all->filter(fn ($r) => array_key_exists($r->day_of_week, $weekendMap));

    $weekdayRows = $weekdaySchedules->groupBy($slotOf)->sortKeys();
    $weekendTimeSlots = $weekendSchedules->map($slotOf)->unique()->sort();
@endphp

@once
    <style>
        /* Official NMU timetable document.
           Layout copied from admin/course-offerings/index.blade.php so the
           professor and student schedules print identically. Scoped under
           .sched-doc so it can sit inside a card on screen and bare on paper. */
        .sched-doc { color: black; font-family: 'Battambang', system-ui, sans-serif; }

        .sched-doc .header-print-layout {
            display: flex;
            align-items: flex-start;
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }
        .sched-doc .uni-logo-text {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-left: 10px;
        }
        .sched-doc .uni-logo-text img { width: 95px; height: auto; margin: 0 auto 5px auto; }
        .sched-doc .uni-logo-text h3 {
            font-family: 'Moul', serif !important;
            font-size: 11pt;
            color: black;
            margin: 2px 0;
            line-height: 1.4;
            font-weight: normal;
            white-space: nowrap;
        }
        .sched-doc .kingdom-header {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            top: 0;
        }
        .sched-doc .kingdom-header h2 {
            font-family: 'Moul', serif !important;
            font-size: 13pt;
            margin: 2px 0;
            color: black;
            line-height: 1.4;
            font-weight: normal;
        }
        .sched-doc .kingdom-header img { width: 130px; height: auto; margin: 4px auto 0 auto; display: block; }

        .sched-doc .schedule-title-block { text-align: center; margin-top: 15px; margin-bottom: 20px; }
        .sched-doc .schedule-title-block h1 { font-family: 'Moul', serif !important; font-size: 12pt; margin: 5px 0; color: black; font-weight: normal; }
        .sched-doc .schedule-title-block p { font-size: 10.5pt; margin: 3px 0; color: black; line-height: 1.5; }

        .sched-doc .table-wrapper { flex-grow: 1; }
        .sched-doc .specialty-title {
            text-align: left;
            font-weight: bold;
            font-family: 'Battambang', sans-serif;
            font-size: 11pt;
            margin-bottom: 6px;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        .sched-doc .matrix-table { width: 100%; border-collapse: collapse; border: 1.5pt solid black; margin-bottom: 20px; }
        .sched-doc .matrix-table th,
        .sched-doc .matrix-table td {
            border: 1pt solid black;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            color: black;
        }
        .sched-doc .matrix-table th {
            font-size: 10pt;
            font-family: 'Battambang', sans-serif;
            font-weight: bold;
            background-color: transparent !important;
        }
        .sched-doc .matrix-table td { font-size: 9.5pt; line-height: 1.4; height: 45px; }

        .sched-doc .cell-subject { font-weight: bold; display: block; margin-bottom: 2px; }
        .sched-doc .cell-lecturer { display: block; margin-bottom: 2px; }
        .sched-doc .cell-room { display: block; font-weight: bold; }
        .sched-doc .cell-entry + .cell-entry { margin-top: 4px; padding-top: 4px; border-top: 1px dotted #999; }

        .sched-doc .f-sigs {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            page-break-inside: avoid;
            padding: 0 10px;
            padding-bottom: 15px;
        }
        .sched-doc .sig-block-left { text-align: center; width: 40%; }
        .sched-doc .sig-block-right { text-align: center; width: 45%; }
        .sched-doc .sig-title-moul { font-family: 'Moul', serif !important; font-size: 11pt; margin-bottom: 4px; font-weight: normal; }
        .sched-doc .sig-date-kh { font-size: 11pt; font-family: 'Battambang', sans-serif; margin-bottom: 4px; }
        .sched-doc .sig-spacer { height: 80px; }

        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body {
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0;
                padding: 0;
                font-family: 'Battambang', system-ui !important;
                zoom: 95%;
            }
            /* the paper copy behaves like the admin print container: flex column pinned to one page */
            .sched-doc-print {
                display: flex !important;
                flex-direction: column;
                width: 100% !important;
                height: 95vh;
                color: black;
                position: relative;
            }
        }
    </style>
@endonce

<div class="sched-doc @if($forPrint) sched-doc-print @endif">
    {{-- Header Layout --}}
    <div class="header-print-layout">
        <div class="kingdom-header">
            <h2>ព្រះរាជាណាចក្រកម្ពុជា</h2>
            <h2>ជាតិ សាសនា ព្រះមហាក្សត្រ</h2>
            <img src="{{ asset('assets/image/2.png') }}" alt="">
        </div>

        <div class="uni-logo-text">
            <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="">
            <h3>សាកលវិទ្យាល័យជាតិមានជ័យ</h3>
            <h3>ការិយាល័យសិក្សា</h3>
        </div>
    </div>

    {{-- Schedule Title --}}
    <div class="schedule-title-block">
        <h1>កាលវិភាគប្រចាំឆមាសទី{{ $semesterNum }} / Timetable Semester {{ $semesterNum }}</h1>
        <p>ជំនាន់ទី{{ schedKhmerNumber($generation) }} ឆ្នាំទី{{ $yearLabel }} {{ $facultyName }} ឆ្នាំសិក្សា {{ schedKhmerNumber($academicYear) }}</p>
        <p>ចាប់ផ្តើមពីថ្ងៃ................................................................................. វេនសិក្សា ចន្ទ-សុក្រ</p>
    </div>

    {{-- Timetable matrices --}}
    <div class="table-wrapper">
        @if ($weekdayRows->isNotEmpty())
            <div class="specialty-title">ជំនាញ៖ {{ $deptName }}</div>
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th style="width: 14%;">{{ __('class_hours') }}</th>
                        @foreach ($weekdayMap as $dayLabel)
                            <th>{{ $dayLabel }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weekdayRows as $slot => $slots)
                        <tr>
                            <td style="font-weight: bold;">{{ $slot }}</td>
                            @foreach ($weekdayMap as $dayKey => $dayLabel)
                                <td>
                                    @foreach ($slots->where('day_of_week', $dayKey) as $class)
                                        <div class="cell-entry">
                                            <span class="cell-subject">{{ $class->course_title }}</span>
                                            @if ($showLecturer)
                                                <span class="cell-lecturer">លោក {{ str_replace('Mr. ', '', $class->lecturer_name) }}</span>
                                            @endif
                                            <span class="cell-room">{{ __('room') }} {{ $class->room_number }}</span>
                                        </div>
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if ($weekendSchedules->isNotEmpty())
            <div class="specialty-title">ជំនាញ៖ {{ $deptName }} (សៅរ៍-អាទិត្យ)</div>
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th style="width: 14%;">{{ __('class_hours') }}</th>
                        @foreach ($weekendTimeSlots as $timeSlot)
                            <th>{{ schedKhmerNumber($timeSlot) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weekendMap as $dayKey => $dayLabel)
                        <tr>
                            <td style="font-weight: bold;">{{ $dayLabel }}</td>
                            @foreach ($weekendTimeSlots as $time)
                                <td>
                                    @foreach ($weekendSchedules->filter(function ($r) use ($dayKey, $time, $slotOf) {
                                        return $r->day_of_week === $dayKey && $slotOf($r) === $time;
                                    }) as $class)
                                        <div class="cell-entry">
                                            <span class="cell-subject">{{ $class->course_title }}</span>
                                            @if ($showLecturer)
                                                <span class="cell-lecturer">លោក {{ str_replace('Mr. ', '', $class->lecturer_name) }}</span>
                                            @endif
                                            <span class="cell-room">{{ __('room') }} {{ $class->room_number }}</span>
                                        </div>
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Footer Signatures --}}
    <div class="f-sigs">
        <div class="sig-block-left">
            <div class="sig-title-moul">{{ __('doc_seen_and_approved') }}</div>
            <div class="sig-title-moul">{{ __('doc_rector') }}</div>
            <div class="sig-title-moul" style="margin-top: 0;">{{ __('doc_rector_office') }}</div>
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
