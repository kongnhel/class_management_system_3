<x-app-layout>
    <div class="py-6 md:py-12 bg-[#f8fafc] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
                <div>
                    <h2 class="text-xl md:text-2xl font-extrabold text-slate-800 tracking-tight">
                        {{ __('របាយការណ៍វត្តមានសរុប') }}
                    </h2>
                    <p class="text-slate-500 text-xs md:text-sm mt-1">{{ __('គ្រប់គ្រង និងតាមដានវត្តមាននិស្សិតប្រចាំឆមាស') }}</p>
                </div>
                <div class="w-full sm:w-auto">
                    <button onclick="window.print()" 
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold hover:bg-slate-50 transition-all shadow-sm active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        {{ __('បោះពុម្ពរបាយការណ៍') }}
                    </button>
                </div>
            </div>

            <div class="bg-white shadow-sm border border-slate-200 rounded-3xl md:rounded-[2.5rem] overflow-hidden print:border-0 print:shadow-none print:rounded-0">
                
                <div class="hidden print:block text-center mb-10 pt-4">
                    <h1 class="text-xl font-bold mb-1">ព្រះរាជាណាចក្រកម្ពុជា</h1>
                    <h2 class="text-lg font-bold">ជាតិ សាសនា ព្រះមហាក្សត្រ</h2>
                    <div class="flex justify-center mt-2 mb-6">
                        <div class="w-24 h-[1px] bg-black"></div>
                    </div>
                    <div class="flex justify-between text-left text-sm mt-8">
                        <div>
                            <p class="font-bold">{{ __('គ្រឹះស្ថានសិក្សា៖') }} ............................................</p>
                            <p class="font-bold mt-2">{{ __('មុខវិជ្ជា៖') }} {{ $courseOffering->course->name_km }}</p>
                        </div>
                        <div class="text-right">
                            <p>{{ __('កាលបរិច្ឆេទ៖') }} {{ date('d-m-Y') }}</p>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mt-10 underline decoration-double uppercase tracking-wider">{{ __('របាយការណ៍វត្តមានសរុប') }}</h3>
                </div>

                <div class="p-6 md:p-12 border-b border-slate-50 text-center relative overflow-hidden no-print">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 via-teal-500 to-indigo-500"></div>
                    <span class="inline-block px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[9px] md:text-[10px] font-black uppercase tracking-[0.2em] rounded-full mb-4">
                        {{ __('Official Attendance Record') }}
                    </span>
                    <h3 class="text-xl md:text-3xl font-extrabold text-slate-800 mb-2">
                        {{ $courseOffering->course->name_km }}
                    </h3>
                    <div class="flex items-center justify-center gap-2 text-slate-400 text-xs md:text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ __('កាលបរិច្ឆេទ៖') }} {{ date('d-m-Y') }}</span>
                    </div>
                </div>

                <div class="px-4 md:px-8 pb-8 print:px-0">
                    <table class="min-w-full print:text-black">
                        <thead>
                            <tr class="border-b border-slate-100 print:border-b-2 print:border-black">
                                <th class="px-6 py-5 text-left text-[11px] font-black text-slate-400 uppercase tracking-widest print:text-black print:px-2">{{ __('អត្តលេខ') }}</th>
                                <th class="px-6 py-5 text-left text-[11px] font-black text-slate-400 uppercase tracking-widest print:text-black print:px-2">{{ __('ឈ្មោះនិស្សិត') }}</th>
                                <th class="px-4 py-5 text-center text-[11px] font-black text-emerald-600 uppercase tracking-widest print:text-black print:px-1">P</th>
                                <th class="px-4 py-5 text-center text-[11px] font-black text-amber-500 uppercase tracking-widest print:text-black print:px-1">L</th>
                                <th class="px-4 py-5 text-center text-[11px] font-black text-rose-500 uppercase tracking-widest print:text-black print:px-1">A</th>
                                <th class="px-6 py-5 text-center text-[11px] font-black text-slate-400 uppercase tracking-widest print:text-black print:px-2">{{ __('សរុប') }}</th>
                                <th class="px-6 py-5 text-right text-[11px] font-black text-slate-400 uppercase tracking-widest print:text-black print:px-2">{{ __('ភាគរយ (%)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 print:divide-y-0">
                            @foreach ($students as $data)
                                @php
                                    $total = ($data->present_count ?? 0) + ($data->permission_count ?? 0) + ($data->absent_count ?? 0) + ($data->late_count ?? 0);
                                    $percentage = $total > 0 ? (($data->present_count + ($data->late_count ?? 0)) / $total) * 100 : 0;
                                @endphp
                                <tr class="group hover:bg-slate-50/80 transition-colors print:border-b print:border-gray-300">
                                    <td class="px-6 py-4 text-sm font-bold text-slate-400 uppercase print:text-black print:px-2">#{{ $data->id }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-slate-700 print:text-black print:px-2">{{ $data->name }}</td>
                                    <td class="px-4 py-4 text-center print:px-1">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 font-bold text-sm no-print">{{ $data->present_count }}</span>
                                        <span class="hidden print:inline">{{ $data->present_count }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center print:px-1">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 font-bold text-sm no-print">{{ $data->permission_count }}</span>
                                        <span class="hidden print:inline">{{ $data->permission_count }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center print:px-1">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-rose-50 text-rose-600 font-bold text-sm no-print">{{ $data->absent_count }}</span>
                                        <span class="hidden print:inline">{{ $data->absent_count }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-bold text-slate-500 print:text-black print:px-2">{{ $total }}</td>
                                    <td class="px-6 py-4 text-right print:px-2">
                                        <div class="flex items-center justify-end gap-3">
                                            <div class="w-16 bg-slate-100 h-1.5 rounded-full overflow-hidden no-print">
                                                <div class="h-full {{ $percentage < 75 ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-sm font-black {{ $percentage < 75 ? 'text-rose-600' : 'text-emerald-600' }} print:text-black">
                                                {{ number_format($percentage, 0) }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-8 md:p-12 grid grid-cols-2 gap-8 border-t border-slate-50 bg-slate-50/30 print:bg-white print:border-0 print:mt-10">
                    <div class="text-center">
                        <p class="text-[9px] md:text-xs font-black text-slate-400 uppercase tracking-widest mb-12 md:mb-16 print:text-black print:mb-20">{{ __('ហត្ថលេខាសាស្ត្រាចារ្យ') }}</p>
                        <div class="w-full max-w-[160px] h-px bg-slate-200 mx-auto print:bg-black"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-[9px] md:text-xs font-black text-slate-400 uppercase tracking-widest mb-12 md:mb-16 print:text-black print:mb-20">{{ __('ការិយាល័យសិក្សា') }}</p>
                        <div class="w-full max-w-[160px] h-px bg-slate-200 mx-auto print:bg-black"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            /* Hide UI things like scrollbars and navs */
            .no-print { display: none !important; }
            body { background-color: white !important; -webkit-print-color-adjust: exact; }
            
            /* Remove margins from the layout for a Word-like feel */
            .py-6, .py-12 { padding: 0 !important; margin: 0 !important; }
            .max-w-7xl { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
            
            /* Make table fill page */
            table { width: 100% !important; }
            
            /* Ensure text is black for clarity */
            .print\:text-black { color: black !important; }
            
            /* Remove rounded corners and shadows for paper */
            .rounded-3xl, .rounded-[2.5rem] { border-radius: 0 !important; }
            .shadow-sm { box-shadow: none !important; }
            
            /* Custom page margins */
            @page {
                margin: 1.5cm;
            }
        }
    </style>
</x-app-layout>