<x-app-layout>
    <div class="py-6 md:py-12 bg-[#f8fafc] min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl md:text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        <div class="p-1.5 md:p-2 bg-blue-600 rounded-lg text-white">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <span class="leading-tight">{{ __('តំបន់ប្រធានថ្នាក់ - ស្រង់វត្តមាន') }}</span>
                    </h2>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <p class="text-xs md:text-sm font-bold text-slate-500 uppercase tracking-wider">{{ $courseOffering->course->name_km }}</p>
                    </div>
                </div>
                
                <a href="{{ route('student.leader.report', $courseOffering->id) }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-slate-200 text-blue-600 rounded-xl md:rounded-2xl font-bold text-sm hover:bg-blue-50 transition-all shadow-sm group">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('មើលរបាយការណ៍សរុប') }}
                </a>
            </div>

            {{-- Alerts --}}
         @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 md:p-5 rounded-xl mb-6 shadow-sm flex items-center animate-bounce" role="alert">
                        <i class="fas fa-check-circle mr-3 text-green-500 text-xl"></i>
                        <span class="font-bold text-sm md:text-lg">{{ session('success') }}</span>
                    </div>
                @endif

            <form action="{{ route('student.leader.attendance.store', $courseOffering->id) }}" method="POST">
                @csrf
                
                {{-- Date Picker & Submit Button --}}
                <div class="bg-white rounded-3xl p-4 md:p-6 mb-6 border border-slate-200 shadow-sm flex flex-col md:flex-row items-end md:items-center justify-between gap-4 md:gap-6">
                    <div class="w-full md:w-72 group">
                        <label class="block text-[10px] md:text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">{{ __('កាលបរិច្ឆេទស្រង់វត្តមាន') }}</label>
                        <input type="date" name="attendance_date" value="{{ $today }}" 
                               class="w-full rounded-xl md:rounded-2xl border-slate-200 bg-slate-50/50 py-2.5 md:py-3 px-4 md:px-5 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all font-bold text-slate-700 text-sm md:text-base cursor-pointer">
                    </div>
                    
                    <button type="submit" class="w-full md:w-auto px-8 py-3.5 md:py-4 bg-blue-600 text-white rounded-xl md:rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center gap-2 text-sm md:text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('បញ្ជូនវត្តមាន') }}
                    </button>
                </div>

                {{-- Student List --}}
                <div class="bg-white rounded-[2rem] md:rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-4 md:px-8 py-4 text-left text-[10px] md:text-[11px] font-black text-slate-400 uppercase tracking-widest">{{ __('ព័ត៌មាននិស្សិត') }}</th>
                                    <th class="px-4 md:px-8 py-4 text-center text-[10px] md:text-[11px] font-black text-slate-400 uppercase tracking-widest">{{ __('ស្ថានភាពវត្តមាន') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($students as $student)
                                    @if ($student->id == $leaderId)
                                        @continue
                                    @endif
                                    <tr class="group hover:bg-blue-50/30 transition-colors">
                                        <td class="px-4 md:px-8 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3 md:gap-4">
                                                <div class="relative flex-shrink-0">
                                                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl md:rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                        {{ Str::substr($student->name, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-bold text-slate-700 truncate max-w-[120px] md:max-w-full">
                                                        {{ $student->studentProfile->full_name_km ?? $student->name }}
                                                    </div>
                                                    <div class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase mt-0.5">ID: #{{ $student->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 md:px-8 py-4">
                                            <div class="flex justify-center">
                                                {{-- Radio Group - បង្រួមទំហំនៅលើ Mobile --}}
                                                <div class="inline-flex bg-slate-100 p-1 md:p-1.5 rounded-xl md:rounded-[1.2rem] gap-0.5 md:gap-1">
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="attendance[{{ $student->id }}]" value="present" checked class="hidden peer">
                                                        <span class="inline-flex items-center px-3 py-1.5 md:px-5 md:py-2 rounded-lg md:rounded-xl text-[9px] md:text-[11px] font-black uppercase text-slate-500 peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all whitespace-nowrap">
                                                            {{ __('មក') }}
                                                        </span>
                                                    </label>
                                                    
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="attendance[{{ $student->id }}]" value="permission" class="hidden peer">
                                                        <span class="inline-flex items-center px-3 py-1.5 md:px-5 md:py-2 rounded-lg md:rounded-xl text-[9px] md:text-[11px] font-black uppercase text-slate-500 peer-checked:bg-white peer-checked:text-amber-500 peer-checked:shadow-sm transition-all whitespace-nowrap">
                                                            {{ __('ច្បាប់') }}
                                                        </span>
                                                    </label>
                                                    
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="attendance[{{ $student->id }}]" value="absent" class="hidden peer">
                                                        <span class="inline-flex items-center px-3 py-1.5 md:px-5 md:py-2 rounded-lg md:rounded-xl text-[9px] md:text-[11px] font-black uppercase text-slate-500 peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm transition-all whitespace-nowrap">
                                                            {{ __('អវត្តមាន') }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>