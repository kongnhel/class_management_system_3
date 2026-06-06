<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 no-print px-2 font-['Battambang']">
            <div>
                <h2 class="font-black text-2xl md:text-3xl text-slate-900 leading-tight text-center md:text-left">
                    {{ __('កាលវិភាគសិក្សា') }}
                </h2>
                <p class="text-sm text-slate-500 font-medium mt-1 text-center md:text-left">{{ __('ពិនិត្យ និងគ្រប់គ្រងម៉ោងសិក្សារបស់អ្នក') }}</p>
            </div>
            
            <div class="flex gap-2 w-full md:w-auto">
                <button onclick="exportToWord()" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all text-xs">
                    <i class="fas fa-file-word mr-2"></i> Word
                </button>
                <button onclick="window.print()" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all text-xs">
                    <i class="fas fa-print mr-2"></i> {{ __('បោះពុម្ព') }}
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
        .text-blue-custom { color: #2a58ad; }
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
            display: flex; flex-direction: column; gap: 20px; width: 100%; font-family: var(--font-body);
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
    </style>

    <div class="bg-gray-100 min-h-screen py-4 md:py-10 no-print-bg">
        <div id="printable-area" class="a4-paper">
            
            {{-- 1. HEADER --}}
            <div class="header-layout">
                <div class="header-col header-logo">
                    <img id="logoImg" src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo">
                    <h3 class="font-moul text-blue-custom header-title-km">សាកលវិទ្យាល័យជាតិមានជ័យ</h3>
                    <h3 class="font-moul text-blue-custom header-title-km">{{ __('ការិយាល័យសិក្សា') }}</h3>
                </div>
                <div class="header-col">
                    <h2 class="font-moul header-kingdom">ព្រះរាជាណាចក្រកម្ពុជា</h2>
                    <h2 class="font-moul header-kingdom">ជាតិ សាសនា ព្រះមហាក្សត្រ</h2>
                    <div class="header-line"><img id="lineImg" src="{{ asset('assets/image/2.png') }}" alt="Line"></div>
                </div>
                <div class="header-col hidden md:flex"></div>
            </div>

            <div class="schedule-info">
                <h1 class="font-moul">តារាងវិភាគកម្មធម៌ឆមាសទី១ <span class="font-sans" style="font-family: var(--font-body)">/Timetable Semester 1</span></h1>
                <p>
                    ជំនាន់ទី {{ $user->generation ?? '...' }} 
                    @if($studentProgram) | {{ $studentProgram->name_km ?? $studentProgram->name_en }} @endif
                    | ឆ្នាំសិក្សា {{ date('Y') }}-{{ date('Y')+1 }}
                </p>
                <p style="font-weight: normal; margin-top: 5px;">ចាប់ផ្តើមពីថ្ងៃចន្ទ ១២ កើត ខែអស្សុជ ឆ្នាំរោង ឆស័ក ព.ស ២៥៦៨ ត្រូវនឹងថ្ងៃទី១៤ ខែតុលា ឆ្នាំ២០២៤</p>
            </div>

            {{-- 2. TABLES --}}
            <div class="table-container">
                @php
                    $weekdayMap = ['Monday' => 'ចន្ទ/Mon', 'Tuesday' => 'អង្គារ/Tue', 'Wednesday' => 'ពុធ/Wed', 'Thursday' => 'ព្រហស្បតិ៍/Thu', 'Friday' => 'សុក្រ/Fri'];
                    $weekendMap = ['Saturday' => 'សៅរ៍/Sat', 'Sunday' => 'អាទិត្យ/Sun'];

                    $weekdaySchedules = $schedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekdayMap));
                    $weekendSchedules = $schedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekendMap));

                    $weekdayRows = $weekdaySchedules->groupBy(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->sortKeys();
                    $weekendTimeSlots = $weekendSchedules->map(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->unique()->sort();
                @endphp

                @if($weekdayRows->isNotEmpty())
                    <div class="table-responsive">
                        <div style="text-align: left; font-weight: bold; text-decoration: underline; font-size: 10pt; margin-bottom: 5px;">វេនសិក្សា៖ ចន្ទ-សុក្រ (Mon-Fri)</div>
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th class="font-moul" style="width: 12%;">ម៉ោងសិក្សា</th>
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
                                                    <span class="cell-subject">{{ $class->courseOffering->course->title_km ?? $class->courseOffering->course->title_en }}</span>
                                                    <span class="cell-lecturer">លោក {{ $class->courseOffering->lecturer->name ?? 'N/A' }}</span>
                                                    <span class="cell-room">បន្ទប់ {{ $class->room->room_number ?? '-' }}</span>
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
                        <div style="text-align: left; font-weight: bold; text-decoration: underline; font-size: 10pt; margin-bottom: 5px;">វេនសិក្សា៖ សៅរ៍-អាទិត្យ (Sat-Sun)</div>
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th class="font-moul" style="width: 12%;">{{ __('ថ្ងៃសិក្សា') }}</th>
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
                                                    <span class="cell-subject">{{ $class->courseOffering->course->title_km ?? $class->courseOffering->course->title_en }}</span>
                                                    <span class="cell-lecturer">លោក {{ $class->courseOffering->lecturer->name ?? 'N/A' }}</span>
                                                    <span class="cell-room">បន្ទប់ {{ $class->room->room_number ?? '-' }}</span>
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
                    <div class="sig-title" style="font-weight: bold;">បានឃើញ និងឯកភាព</div>
                    <div class="sig-title font-moul">ជ. សាកលវិទ្យាធិការ</div>
                    <div class="sig-title font-moul">សាកលវិទ្យាធិការរង</div>
                    <div class="sig-spacer"></div>
                </div>

                @php
                    function toKhmerNumber($number) {
                        $khmerNumbers = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
                        return str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $khmerNumbers, $number);
                    }
                    $now = now();
                    $khmerMonths = [1 => 'មករា', 2 => 'កុម្ភៈ', 3 => 'មីនា', 4 => 'មេសា', 5 => 'ឧសភា', 6 => 'មិថុនា', 7 => 'កក្កដា', 8 => 'សីហា', 9 => 'កញ្ញា', 10 => 'តុលា', 11 => 'វិច្ឆិកា', 12 => 'ធ្នូ'];
                    $beYear = $now->year + 543; 
                    $day = toKhmerNumber($now->format('d'));
                    $month = $khmerMonths[$now->month];
                    $year = toKhmerNumber($now->year);
                    $beYearKh = toKhmerNumber($beYear);
                @endphp

                <div class="sig-block" style="text-align: right; padding-right: 10px;">
                    <div class="sig-date">ថ្ងៃទី{{ $day }} ខែ{{ $month }} ឆ្នាំ{{ $year }} ព.ស {{ $beYearKh }}</div>
                    <div class="sig-date">បន្ទាយមានជ័យ ថ្ងៃទី............. ខែ............. ឆ្នាំ២០......</div>
                    <div class="sig-title font-moul" style="margin-top: 5px;">ប្រធានការិយាល័យសិក្សា</div>
                    <div class="sig-spacer"></div>
                </div>
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

        function exportToWord() {
            const logo = document.getElementById('logoImg');
            const line = document.getElementById('lineImg');
            let content = document.getElementById('printable-area').cloneNode(true);
            
            if(logo && logo.src) { content.querySelector('#logoImg').src = getBase64Image(logo); }
            if(line && line.src) { content.querySelector('#lineImg').src = getBase64Image(line); }

            const htmlString = `
                <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
                <head><meta charset='utf-8'><style>
                    body { font-family: 'Battambang', Arial, sans-serif; }
                    .header-layout { width: 100%; border-bottom: 2px solid black; margin-bottom: 20px; }
                    .custom-table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1pt solid black; padding: 5px; text-align: center; }
                    th { background-color: #f1f5f9; font-family: 'Moul', serif; font-size: 10pt; }
                    @page { size: A4 landscape; margin: 1cm; }
                </style></head>
                <body>${content.innerHTML}</body></html>`;

            const blob = new Blob(['\ufeff', htmlString], { type: 'application/msword' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url; link.download = 'My-Study-Schedule.doc';
            document.body.appendChild(link); link.click(); document.body.removeChild(link);
        }
    </script>
</x-app-layout>