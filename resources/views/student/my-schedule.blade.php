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

    @php
        $docData = [
            'rows' => $schedules->map(fn ($s) => (object) [
                'day_of_week' => $s->day_of_week,
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'course_title' => $s->courseOffering?->course?->title_km ?? $s->courseOffering?->course?->title_en ?? 'N/A',
                'lecturer_name' => $s->courseOffering?->lecturer?->name ?? '',
                'room_number' => $s->room?->room_number ?? '-',
            ]),
            'semesterNum' => $semesterNum,
            'generation' => $generation,
            'academicYear' => date('Y') . '-' . (date('Y') + 1),
            'facultyName' => $studentDepartment?->faculty?->name_km,
            'deptName' => $studentDepartment?->name_km,
        ];
    @endphp

    <div class="bg-gray-100 min-h-screen py-4 md:py-10 print:hidden">
        <div class="max-w-6xl mx-auto px-2 sm:px-4">
            @if ($schedules->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-book-open text-gray-300 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-600 mb-2">{{ __('no_schedule_yet') }}</h3>
                </div>
            @else
                {{-- SCREEN: the official document inside a card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-10">
                    @include('components.schedule-document', $docData)
                </div>
            @endif
        </div>
    </div>

    {{-- PRINT: same document, laid out for one A4 landscape page --}}
    @if ($schedules->isNotEmpty())
        <div class="hidden print:block">
            @include('components.schedule-document', $docData + ['forPrint' => true])
        </div>
    @endif
</x-app-layout>
