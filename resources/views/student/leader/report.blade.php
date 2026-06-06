<x-app-layout>
    <div class="py-6 md:py-12 bg-[#f8fafc] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
                <div class="flex items-center gap-3 md:gap-4">
                    <div class="p-2.5 md:p-3 bg-white border border-slate-200 rounded-xl md:rounded-2xl shadow-sm">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-extrabold text-slate-800 tracking-tight leading-tight">{{ __('របាយការណ៍វត្តមានរួម') }}</h2>
                        <p class="text-xs md:text-sm font-medium text-slate-500 mt-1 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            {{ $courseOffering->course->title_km ?? $courseOffering->course->name_km }}
                        </p>
                    </div>
                </div>
                
                <button onclick="window.print()" 
                        class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-xl md:rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95 group text-sm">
                    <svg class="w-4 h-4 mr-2 transform group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    {{ __('បោះពុម្ពរបាយការណ៍') }}
                </button>
            </div>

            <div class="bg-white shadow-sm border border-slate-200 rounded-3xl md:rounded-[2.5rem] overflow-hidden">
                <div class="p-1 w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 no-print"></div>
                
                {{-- Table Container --}}
                <div class="overflow-x-auto selection:bg-blue-100">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-4 md:px-6 py-4 md:py-5 text-left text-[10px] md:text-[11px] font-black text-slate-400 uppercase tracking-wider md:tracking-[0.2em]">{{ __('អត្តលេខ') }}</th>
                                <th class="px-4 md:px-6 py-4 md:py-5 text-left text-[10px] md:text-[11px] font-black text-slate-400 uppercase tracking-wider md:tracking-[0.2em]">{{ __('ព័ត៌មាននិស្សិត') }}</th>
                                {{-- លាក់ Column មុខវិជ្ជាលើ Mobile ដើម្បីកុំឱ្យចង្អៀត --}}
                                <th class="hidden md:table-cell px-6 py-5 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ __('មុខវិជ្ជា') }}</th>
                                <th class="px-2 md:px-4 py-4 md:py-5 text-center text-[10px] md:text-[11px] font-black text-emerald-600 uppercase">{{ __('មក') }}</th>
                                <th class="px-2 md:px-4 py-4 md:py-5 text-center text-[10px] md:text-[11px] font-black text-amber-500 uppercase">{{ __('ច្បាប់') }}</th>
                                <th class="px-2 md:px-4 py-4 md:py-5 text-center text-[10px] md:text-[11px] font-black text-rose-500 uppercase">{{ __('អវត្តមាន') }}</th>
                                <th class="px-4 md:px-6 py-4 md:py-5 text-right text-[10px] md:text-[11px] font-black text-slate-400 uppercase tracking-wider">{{ __('ភាគរយ (%)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($students as $data)
                                @php
                                    $total = $data->present_count + $data->permission_count + $data->absent_count + ($data->late_count ?? 0);
                                    $percentage = $total > 0 ? (($data->present_count + $data->permission_count) / $total) * 100 : 0;
                                @endphp
                                <tr class="group hover:bg-slate-50/80 transition-all duration-200">
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-[11px] md:text-sm font-bold text-slate-400">
                                        #{{ $data->id }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2 md:gap-3">
                                            <div class="w-8 h-8 md:w-9 md:h-9 rounded-lg md:rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-black text-[10px] md:text-xs">
                                                {{ Str::substr($data->name, 0, 1) }}
                                            </div>
                                            <span class="text-xs md:text-sm font-bold text-slate-700 truncate max-w-[100px] md:max-w-full">
                                                {{ $data->studentProfile->full_name_km ?? $data->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-tight">
                                            {{ $courseOffering->course->title_km ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-2 md:px-4 py-4 text-center">
                                        <span class="text-xs md:text-sm font-black text-emerald-600 bg-emerald-50 w-7 h-7 md:w-8 md:h-8 inline-flex items-center justify-center rounded-lg italic">
                                            {{ $data->present_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 md:px-4 py-4 text-center">
                                        <span class="text-xs md:text-sm font-black text-amber-500 bg-amber-50 w-7 h-7 md:w-8 md:h-8 inline-flex items-center justify-center rounded-lg italic">
                                            {{ $data->permission_count }}
                                        </span>
                                    </td>
                                    <td class="px-2 md:px-4 py-4 text-center">
                                        <span class="text-xs md:text-sm font-black text-rose-500 bg-rose-50 w-7 h-7 md:w-8 md:h-8 inline-flex items-center justify-center rounded-lg italic">
                                            {{ $data->absent_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="text-[10px] md:text-xs font-black {{ $percentage < 75 ? 'text-rose-600' : 'text-emerald-600' }}">
                                                {{ number_format($percentage, 0) }}%
                                            </span>
                                            <div class="w-12 md:w-20 h-1 md:h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full {{ $percentage < 75 ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Footer Info --}}
            <div class="mt-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-4 md:px-6 no-print">
                <p class="text-[9px] md:text-[11px] font-bold text-slate-400 uppercase tracking-widest italic">
                    * ចំណុចសម្គាល់៖ វត្តមាន < ៧៥% បង្ហាញជាពណ៌ក្រហម។
                </p>
                <p class="text-[9px] md:text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ __('កាលបរិច្ឆេទបញ្ចេញ៖') }} {{ date('d-M-Y') }}
                </p>
            </div>
        </div>
    </div>

    <style>
        /* Smooth Horizontal Scroll for Mobile */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }

        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; }
            .py-12, .py-6 { padding: 0 !important; }
            .rounded-3xl, .rounded-[2.5rem] { border-radius: 0 !important; border: 1px solid #e2e8f0 !important; }
            .overflow-x-auto { overflow: visible !important; }
            table { width: 100% !important; border-collapse: collapse !important; }
        }
    </style>
</x-app-layout>