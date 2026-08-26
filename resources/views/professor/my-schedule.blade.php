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
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight leading-none">{{ __('កាលវិភាគបង្រៀន') }}</h2>
                            <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">My Teaching Schedule</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="hidden md:flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 mr-2">
                            <span class="text-xs font-bold text-slate-400 uppercase">{{ $semester }}</span>
                            <div class="h-4 w-px bg-slate-300"></div>
                            <span class="text-sm font-bold text-emerald-600">{{ __('ឆ្នាំសិក្សា') }} {{ $academicYear }}</span>
                        </div>
                        <button onclick="window.print()" class="group flex items-center justify-center gap-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl font-bold shadow-sm transition-all text-sm">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"></path></svg>
                            <span>{{ __('បោះពុម្ព') }}</span>
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
                        <h3 class="text-xl font-bold text-slate-800">{{ __('មិនទាន់មានកាលវិភាគបង្រៀន') }}</h3>
                        <p class="text-slate-500 mt-2">{{ __('សូមទាក់ទង{{ __('ការិយាល័យសិក្សា') }}សម្រាប់ព័ត៌មានបន្ថែម។') }}</p>
                    </div>
                @else
                    @php
                        $weekdayMap = ['Monday' => __('ចន្ទ/Mon'), 'Tuesday' => __('អង្គារ/Tue'), 'Wednesday' => __('ពុធ/Wed'), 'Thursday' => __('ព្រហស្បតិ៍/Thu'), 'Friday' => __('សុក្រ/Fri')];
                        $weekendMap = ['Saturday' => __('សៅរ៍/Sat'), 'Sunday' => __('អាទិត្យ/Sun')];

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
                                ]);
                            }
                        }

                        $weekdaySchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekdayMap));
                        $weekendSchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekendMap));

                        $weekdayRows = $weekdaySchedules->groupBy(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->sortKeys();
                        $weekendTimeSlots = $weekendSchedules->map(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->unique()->sort();
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-6">
                            {{-- Header --}}
                            <div class="grid grid-cols-3 items-start gap-4 border-b-2 border-black pb-4 mb-4 text-center">
                                <div class="flex flex-col items-center">
                                    <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" class="w-20 h-20 object-contain">
                                    <h3 class="text-sm font-bold text-blue-700 mt-1" style="font-family: 'Moul', serif;">{{ __('សាកលវិទ្យាល័យជាតិមានជ័យ') }}</h3>
                                    <h3 class="text-sm font-bold text-blue-700" style="font-family: 'Moul', serif;">{{ __('ការិយាល័យសិក្សា') }}</h3>
                                </div>
                                <div class="flex flex-col items-center">
                                    <h2 class="text-base font-bold" style="font-family: 'Moul', serif;">{{ __('ព្រះរាជាណាចក្រកម្ពុជា') }}</h2>
                                    <h2 class="text-base font-bold" style="font-family: 'Moul', serif;">{{ __('ជាតិ សាសនា ព្រះមហាក្សត្រ') }}</h2>
                                    <img src="{{ asset('assets/image/2.png') }}" alt="motto" class="h-7 mx-auto mt-1">
                                </div>
                                <div></div>
                            </div>

                            <div class="text-center mb-4">
                                <h1 class="text-lg font-bold" style="font-family: 'Moul', serif;">{{ __('តារាងវិភាគប្រចាំ') }}{{ $semester }}</h1>
                                <p class="text-sm font-bold mt-1">{{ __('ឆ្នាំសិក្សា') }} {{ $academicYear }}</p>
                            </div>

                            {{-- Weekday Table --}}
                            @if($weekdayRows->isNotEmpty())
                                <div class="mb-5">
                                    <div class="text-left font-bold underline text-sm mb-1">{{ __('វេនសិក្សា៖ ចន្ទ-សុក្រ') }} (Mon-Fri)</div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full border-collapse border border-black text-sm">
                                            <thead>
                                                <tr>
                                                    <th class="border border-black px-2 py-1 bg-slate-100" style="font-family: 'Moul', serif; width: 12%;">{{ __('ម៉ោងសិក្សា') }}</th>
                                                    @foreach($weekdayMap as $label)
                                                        <th class="border border-black px-2 py-1 bg-slate-100" style="font-family: 'Moul', serif;">{{ $label }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($weekdayRows as $slot => $slots)
                                                <tr>
                                                    <td class="border border-black px-2 py-1 text-center font-bold bg-slate-50">{{ $slot }}</td>
                                                    @foreach($weekdayMap as $dayKey => $label)
                                                        <td class="border border-black px-2 py-1 text-center">
                                                            @php $class = $slots->where('day_of_week', $dayKey)->first(); @endphp
                                                            @if($class)
                                                                <div class="flex flex-col gap-0.5">
                                                                    <span class="font-bold">{{ $class->course_title_km }}</span>
                                                                    <span class="text-xs">{{ __('បន្ទប់') }} {{ $class->room_number }}</span>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            {{-- Weekend Table --}}
                            @if($weekendSchedules->isNotEmpty())
                                <div class="mb-5">
                                    <div class="text-left font-bold underline text-sm mb-1">{{ __('វេនសិក្សា៖ សៅរ៍-អាទិត្យ') }} (Sat-Sun)</div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full border-collapse border border-black text-sm">
                                            <thead>
                                                <tr>
                                                    <th class="border border-black px-2 py-1 bg-slate-100" style="font-family: 'Moul', serif; width: 12%;">{{ __('ថ្ងៃសិក្សា') }}</th>
                                                    @foreach($weekendTimeSlots as $time)
                                                        <th class="border border-black px-2 py-1 bg-slate-100" style="font-family: 'Moul', serif;">{{ $time }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($weekendMap as $dayKey => $label)
                                                <tr>
                                                    <td class="border border-black px-2 py-1 text-center font-bold bg-slate-50">{{ $label }}</td>
                                                    @foreach($weekendTimeSlots as $time)
                                                        <td class="border border-black px-2 py-1 text-center">
                                                            @php
                                                                $class = $weekendSchedules->filter(function($s) use ($dayKey, $time) {
                                                                    $slot = \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i');
                                                                    return $s->day_of_week === $dayKey && $slot === $time;
                                                                })->first();
                                                            @endphp
                                                            @if($class)
                                                                <div class="flex flex-col gap-0.5">
                                                                    <span class="font-bold">{{ $class->course_title_km }}</span>
                                                                    <span class="text-xs">{{ __('បន្ទប់') }} {{ $class->room_number }}</span>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            {{-- Footer Signature --}}
                            <div class="flex justify-between mt-6">
                                <div class="text-left w-1/2 pl-2">
                                    <div class="text-sm font-bold">{{ __('បានឃើញ និងឯកភាព') }}</div>
                                    <div class="text-sm" style="font-family: 'Moul', serif;">{{ __('ជ. សាកលវិទ្យាធិការ') }}</div>
                                    <div class="text-sm" style="font-family: 'Moul', serif;">{{ __('សាកលវិទ្យាធិការរង') }}</div>
                                    <div class="h-16"></div>
                                </div>
                                @php
                                    $now = now();
                                    $khmerMonths = [1=>__('មករា'),2=>__('កុម្ភៈ'),3=>__('មីនា'),4=>__('មេសា'),5=>__('ឧសភា'),6=>__('មិថុនា'),7=>__('កក្កដា'),8=>__('សីហា'),9=>__('កញ្ញា'),10=>__('តុលា'),11=>__('វិច្ឆិកា'),12=>__('ធ្នូ')];
                                    function toKhmerNumsScr($n) { return str_replace(range(0,9), ['០','១','២','៣','៤','៥','៦','៧','៨','៩'], $n); }
                                @endphp
                                <div class="text-right w-1/2 pr-2">
                                    <div class="text-xs">{{ __('ថ្ងៃទី') }}{{ toKhmerNumsScr($now->format('d')) }} {{ __('ខែ') }}{{ $khmerMonths[$now->month] }} {{ __('ឆ្នាំ') }}{{ toKhmerNumsScr((string)$now->year) }}</div>
                                    <div class="text-sm mt-1" style="font-family: 'Moul', serif;">ប្រធាន{{ __('ការិយាល័យសិក្សា') }}</div>
                                    <div class="h-16"></div>
                                </div>
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
                    $weekdayMap = ['Monday' => __('ចន្ទ/Mon'), 'Tuesday' => __('អង្គារ/Tue'), 'Wednesday' => __('ពុធ/Wed'), 'Thursday' => __('ព្រហស្បតិ៍/Thu'), 'Friday' => __('សុក្រ/Fri')];
                    $weekendMap = ['Saturday' => __('សៅរ៍/Sat'), 'Sunday' => __('អាទិត្យ/Sun')];

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
                    $khmerMonths = [1=>__('មករា'),2=>__('កុម្ភៈ'),3=>__('មីនា'),4=>__('មេសា'),5=>__('ឧសភា'),6=>__('មិថុនា'),7=>__('កក្កដា'),8=>__('សីហា'),9=>__('កញ្ញា'),10=>__('តុលា'),11=>__('វិច្ឆិកា'),12=>__('ធ្នូ')];
                    $dayKh = toKhmerNumsPrint($now->format('d'));
                    $monthKh = $khmerMonths[$now->month];
                    $yearKh = toKhmerNumsPrint((string)$now->year);
                @endphp

                {{-- HEADER --}}
                <div style="display: grid; grid-template-columns: 30% 40% 30%; text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px;">
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" style="width: 70px; height: auto; margin-bottom: 5px;">
                        <h3 style="font-family: 'Moul', serif; font-size: 10pt; color: #2a58ad; line-height: 1.4; margin: 0;">{{ __('សាកលវិទ្យាល័យជាតិមានជ័យ') }}</h3>
                        <h3 style="font-family: 'Moul', serif; font-size: 10pt; color: #2a58ad; line-height: 1.4; margin: 0;">{{ __('ការិយាល័យសិក្សា') }}</h3>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <h2 style="font-family: 'Moul', serif; font-size: 11pt; margin: 0;">{{ __('ព្រះរាជាណាចក្រកម្ពុជា') }}</h2>
                        <h2 style="font-family: 'Moul', serif; font-size: 11pt; margin: 0;">{{ __('ជាតិ សាសនា ព្រះមហាក្សត្រ') }}</h2>
                        <img src="{{ asset('assets/image/2.png') }}" alt="motto" style="height: 30px; margin-top: 5px;">
                    </div>
                    <div></div>
                </div>

                <div style="text-align: center; margin-bottom: 15px;">
                    <h1 style="font-family: 'Moul', serif; font-size: 13pt; margin: 5px 0;">{{ __('តារាងវិភាគប្រចាំ') }}{{ $semester }}</h1>
                    <p style="font-size: 9pt; font-weight: bold; margin: 2px 0;">
                        {{ __('ឆ្នាំសិក្សា') }} {{ $academicYear }}
                    </p>
                </div>

                {{-- WEEKDAY TABLE --}}
                @if($weekdayRows->isNotEmpty())
                    <div style="margin-bottom: 15px;">
                        <div style="text-align: left; font-weight: bold; text-decoration: underline; font-size: 10pt; margin-bottom: 5px;">{{ __('វេនសិក្សា៖ ចន្ទ-សុក្រ') }} (Mon-Fri)</div>
                        <table style="width: 100%; border-collapse: collapse; border: 1.5pt solid black;">
                            <thead>
                                <tr>
                                    <th style="border: 1pt solid black; padding: 4px; background-color: #f1f5f9; font-family: 'Moul', serif; width: 12%;">{{ __('ម៉ោងសិក្សា') }}</th>
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
                                                    <span style="color: #334155;">{{ __('បន្ទប់') }} {{ $class->room_number }}</span>
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
                        <div style="text-align: left; font-weight: bold; text-decoration: underline; font-size: 10pt; margin-bottom: 5px;">{{ __('វេនសិក្សា៖ សៅរ៍-អាទិត្យ') }} (Sat-Sun)</div>
                        <table style="width: 100%; border-collapse: collapse; border: 1.5pt solid black;">
                            <thead>
                                <tr>
                                    <th style="border: 1pt solid black; padding: 4px; background-color: #f1f5f9; font-family: 'Moul', serif; width: 12%;">{{ __('ថ្ងៃសិក្សា') }}</th>
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
                                                    <span style="color: #334155;">{{ __('បន្ទប់') }} {{ $class->room_number }}</span>
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
                        <div style="font-size: 9pt; font-weight: bold;">{{ __('បានឃើញ និងឯកភាព') }}</div>
                        <div style="font-size: 9pt; font-family: 'Moul', serif;">{{ __('ជ. សាកលវិទ្យាធិការ') }}</div>
                        <div style="font-size: 9pt; font-family: 'Moul', serif;">{{ __('សាកលវិទ្យាធិការរង') }}</div>
                        <div style="height: 70px;"></div>
                    </div>
                    <div style="text-align: right; padding-right: 10px; width: 48%;">
                        <div style="font-size: 8.5pt;">{{ __('ថ្ងៃទី') }}{{ $dayKh }} {{ __('ខែ') }}{{ $monthKh }} {{ __('ឆ្នាំ') }}{{ $yearKh }}</div>
                        <div style="font-size: 9pt; font-family: 'Moul', serif; margin-top: 5px;">ប្រធាន{{ __('ការិយាល័យសិក្សា') }}</div>
                        <div style="height: 70px;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>