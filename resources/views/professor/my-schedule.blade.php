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
                            <span class="text-xs font-bold text-slate-400 uppercase">{{ __('ឆមាសទី ១') }}</span>
                            <div class="h-4 w-px bg-slate-300"></div>
                            <span class="text-sm font-bold text-emerald-600">ឆ្នាំសិក្សា ២០២៤-២០២៥</span>
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
            
            {{-- SCREEN VIEW (Cards) --}}
            <div class="print:hidden">
                @if ($courseOfferings->isEmpty())
                    <div class="flex flex-col items-center justify-center py-24 bg-white rounded-3xl border border-dashed border-slate-300">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800">{{ __('មិនទាន់មានកាលវិភាគបង្រៀន') }}</h3>
                        <p class="text-slate-500 mt-2">{{ __('សូមទាក់ទងការិយាល័យសិក្សាសម្រាប់ព័ត៌មានបន្ថែម។') }}</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach ($courseOfferings as $offering)
                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                                {{-- Subject Header --}}
                                <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/30">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg font-bold shadow-sm">
                                            {{ substr($offering->course?->title_en ?? '', 0, 1) }}
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $offering->course?->title_en ?? 'N/A' }}</h3>
                                            <p class="text-sm text-slate-500 font-medium">{{ $offering->course?->title_km ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-3 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-600 uppercase tracking-wide shadow-sm">
                                            Year {{ $offering->academic_year }}
                                        </span>
                                        <span class="px-3 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-600 uppercase tracking-wide shadow-sm">
                                            Sem {{ $offering->semester }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Schedules Grid --}}
                                <div class="p-6">
                                    @if ($offering->schedules->isEmpty())
                                        <div class="text-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                            <p class="text-sm text-slate-400 italic">{{ __('មិនទាន់កំណត់ម៉ោងបង្រៀន') }}</p>
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach ($offering->schedules as $schedule)
                                                <div class="flex items-start p-4 rounded-xl border border-slate-100 bg-white hover:border-emerald-200 hover:shadow-sm transition-all group">
                                                    <div class="mr-4 mt-1">
                                                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                                            <i class="far fa-clock"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="text-base font-black text-slate-800 mb-1">{{ __($schedule->day_of_week) }}</p>
                                                        <div class="space-y-1">
                                                            <p class="text-sm font-bold text-slate-600 flex items-center gap-2">
                                                                <span class="text-xs text-slate-400 uppercase tracking-wider w-10">Time:</span>
                                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                            </p>
                                                            <p class="text-sm font-bold text-slate-600 flex items-center gap-2">
                                                                <span class="text-xs text-slate-400 uppercase tracking-wider w-10">Room:</span>
                                                                <span class="text-emerald-600">{{ $schedule->room->room_number ?? 'N/A' }}</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- PRINT VIEW (Simple Table) --}}
            <div class="hidden print:block font-['Battambang']">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold mb-2">{{ __('កាលវិភាគបង្រៀន') }}</h1>
                    <p class="text-sm text-gray-600">Lecturer Teaching Schedule</p>
                </div>

                <table class="w-full border-collapse border border-gray-300 text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-3 py-2 text-left w-1/3">{{ __('មុខវិជ្ជា') }}</th>
                            <th class="border border-gray-300 px-3 py-2 text-center">{{ __('ឆ្នាំ/ឆមាស') }}</th>
                            <th class="border border-gray-300 px-3 py-2 text-center">{{ __('ថ្ងៃ') }}</th>
                            <th class="border border-gray-300 px-3 py-2 text-center">{{ __('ម៉ោង') }}</th>
                            <th class="border border-gray-300 px-3 py-2 text-center">{{ __('បន្ទប់') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courseOfferings as $offering)
                            @if ($offering->schedules->isEmpty())
                                <tr>
                                    <td class="border border-gray-300 px-3 py-2 font-bold">{{ $offering->course?->title_en ?? 'N/A' }}</td>
                                    <td class="border border-gray-300 px-3 py-2 text-center">Y{{ $offering->academic_year }} / S{{ $offering->semester }}</td>
                                    <td colspan="3" class="border border-gray-300 px-3 py-2 text-center italic text-gray-500">{{ __('មិនមានកាលវិភាគ') }}</td>
                                </tr>
                            @else
                                @foreach ($offering->schedules as $index => $schedule)
                                    <tr>
                                        @if ($index === 0)
                                            <td class="border border-gray-300 px-3 py-2 font-bold align-top" rowspan="{{ $offering->schedules->count() }}">
                                                {{ $offering->course?->title_en ?? 'N/A' }}
                                                <div class="text-xs font-normal text-gray-500 mt-1">{{ $offering->course?->title_km ?? '' }}</div>
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center align-top" rowspan="{{ $offering->schedules->count() }}">
                                                Y{{ $offering->academic_year }} / S{{ $offering->semester }}
                                            </td>
                                        @endif
                                        <td class="border border-gray-300 px-3 py-2 text-center">{{ __($schedule->day_of_week) }}</td>
                                        <td class="border border-gray-300 px-3 py-2 text-center">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </td>
                                        <td class="border border-gray-300 px-3 py-2 text-center font-bold">{{ $schedule->room->room_number ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
                
                <div class="mt-8 text-right text-xs text-gray-500">
                    <p>Printed on: {{ now()->format('d-M-Y H:i A') }}</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>