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
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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

            @if ($courseOfferings->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 bg-white rounded-3xl border border-dashed border-slate-300 print:hidden">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">{{ __('no_teaching_schedule_yet') }}</h3>
                    <p class="text-slate-500 mt-2">{{ __('no_teaching_schedule_yet') }}</p>
                </div>
            @else
                @php
                    $docData = [
                        'rows' => $courseOfferings->flatMap(fn ($offering) => $offering->scheduleDocumentRows()),
                        'semesterNum' => $semesterNum,
                        'generation' => $courseOfferings->first()?->generation,
                        'academicYear' => $academicYear,
                        'facultyName' => $courseOfferings->first()?->department?->faculty?->name_km,
                        'deptName' => $courseOfferings->map(fn ($offering) => $offering->department?->name_km)->filter()->unique()->implode(' / '),
                    ];
                @endphp

                {{-- SCREEN: the official document inside a card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-10 print:hidden">
                    @include('components.schedule-document', $docData)
                </div>

                {{-- PRINT: same document, laid out for one A4 landscape page --}}
                <div class="hidden print:block">
                    @include('components.schedule-document', $docData + ['forPrint' => true])
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
