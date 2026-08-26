<x-app-layout>
    {{-- Background: Slate ស្រាលបំផុត --}}
    <div class="min-h-screen bg-slate-50/50 py-10 font-['Battambang'] text-slate-600">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- HEADER SECTION --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8 no-print">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">
                        {{ __('កំណត់ត្រាវត្តមាន') }}
                    </h1>
                    <p class="text-sm text-slate-500 mt-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        {{ __('និស្សិត៖') }} <span class="font-bold text-slate-700">{{ Auth::user()->name }}</span>
                    </p>
                </div>
                
                <button onclick="window.print()" 
                    class="group flex items-center justify-center gap-2 bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl font-medium shadow-sm transition-all text-sm">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"></path></svg>
                    <span>{{ __('បោះពុម្ព') }}</span>
                </button>
            </div>

            {{-- MAIN CONTENT CARD --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                
                {{-- DESKTOP TABLE --}}
                <div class="hidden md:block">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead>
                            <tr class="bg-slate-50">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('កាលបរិច្ឆេទ') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/2">{{ __('មុខវិជ្ជា') }}</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('ស្ថានភាព') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($attendances as $attendance)
                                @php
                                    $statusConfig = match(strtolower($attendance->status)) {
                                        'present', 'មាន' => ['label' => __('មកសិក្សា'), 'color' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'bg_soft' => 'bg-emerald-50'],
                                        'absent', 'អវត្តមាន' => ['label' => __('អវត្តមាន'), 'color' => 'bg-rose-500', 'text' => 'text-rose-700', 'bg_soft' => 'bg-rose-50'],
                                        'permission', 'ច្បាប់' => ['label' => __('ច្បាប់'), 'color' => 'bg-amber-500', 'text' => 'text-amber-700', 'bg_soft' => 'bg-amber-50'],
                                        default => ['label' => $attendance->status, 'color' => 'bg-slate-400', 'text' => 'text-slate-600', 'bg_soft' => 'bg-slate-50']
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-600 font-mono">
                                        {{ $attendance->date ? $attendance->date->format('d M, Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-800">
                                            {{ $attendance->courseOffering->course->title_en ?? __('Unknown Course') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusConfig['bg_soft'] }} {{ $statusConfig['text'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['color'] }}"></span>
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-10 h-10 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-sm">{{ __('មិនទាន់មានទិន្នន័យ') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE LIST VIEW --}}
                <div class="md:hidden divide-y divide-slate-100">
                    @forelse ($attendances as $attendance)
                        @php
                            $statusConfig = match(strtolower($attendance->status)) {
                                'present', 'មាន' => ['label' => __('មកសិក្សា'), 'dot' => 'bg-emerald-500'],
                                'absent', 'អវត្តមាន' => ['label' => __('អវត្តមាន'), 'dot' => 'bg-rose-500'],
                                'permission', 'ច្បាប់' => ['label' => __('ច្បាប់'), 'dot' => 'bg-amber-500'],
                                default => ['label' => $attendance->status, 'dot' => 'bg-slate-400']
                            };
                        @endphp
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex-1 min-w-0 pr-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-2 h-2 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">
                                        {{ $attendance->date ? $attendance->date->format('d M, Y') : '-' }}
                                    </p>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 truncate leading-snug">
                                    {{ $attendance->courseOffering->course->title_en ?? 'Unknown Course' }}
                                </h3>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-md">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            <p>{{ __('មិនទាន់មានទិន្នន័យ') }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- PAGINATION --}}
                @if ($attendances->hasPages())
                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Print Styles */
        @media print {
            .no-print { display: none !important; }
            body, .min-h-screen { background-color: white !important; height: auto !important; padding: 0 !important; }
            .shadow-sm, .shadow-xl { box-shadow: none !important; }
            .border { border-color: #e2e8f0 !important; }
            .rounded-2xl { border-radius: 0 !important; }
            /* Hide Pagination on Print */
            .bg-slate-50.px-6.py-4 { display: none !important; } 
        }
    </style>
</x-app-layout>