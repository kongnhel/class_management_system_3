<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                {{-- Title --}}
                <div class="text-center lg:text-left">
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight tracking-tight">
                        {{ __('កែសម្រួលពិន្ទុវត្តមាន') }}
                    </h2>
                    <div class="flex items-center justify-center lg:justify-start mt-1 text-slate-500 space-x-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            {{ $student->profile->full_name_km ?? $student->name }} | {{ $student->student_id_code }}
                        </p>
                    </div>
                </div>
                
                {{-- Back Action --}}
                <div class="flex items-center justify-center lg:justify-end gap-3">
                    <a href="{{ route('professor.manage-grades', ['offering_id' => $courseOffering->id]) }}"
                        class="group inline-flex items-center justify-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold text-xs transition-all duration-200 shadow-sm hover:bg-slate-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        {{ __('ត្រឡប់ក្រោយ') }}
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f8fafc] min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <form action="{{ route('grades.update-attendance') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <input type="hidden" name="course_id" value="{{ $courseOffering->id }}">

                <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden transition-all hover:shadow-md">
                    <div class="p-8 sm:p-10">
                        {{-- Top Stats Section --}}
                        <div class="grid grid-cols-2 gap-4 mb-10">
                            <div class="p-5 bg-slate-50 rounded-[2rem] border border-slate-100 text-center">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">{{ __('ពិន្ទុស្វ័យប្រវត្តិ') }}</span>
                                <span class="text-2xl font-black text-slate-700">{{ number_format($autoScore, 1) }}</span>
                            </div>
                            <div class="p-5 bg-indigo-50 rounded-[2rem] border border-indigo-100 text-center">
                                <span class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-2">{{ __('ពិន្ទុអតិបរមា') }}</span>
                                <span class="text-2xl font-black text-indigo-700">15.0</span>
                            </div>
                        </div>

                        {{-- Input Field --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between px-2">
                                <label for="score" class="text-sm font-black text-slate-700 uppercase tracking-tight">
                                    {{ __('បញ្ចូលពិន្ទុថ្មី (Manual Override)') }}
                                </label>
                                @if($enrollment->attendance_score_manual !== null)
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200 animate-pulse">
                                        {{ __('កំពុងប្រើពិន្ទុដៃ') }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="relative group">
                                <input type="number" 
                                       name="score" 
                                       id="score"
                                       step="0.1" 
                                       max="15" 
                                       min="0"
                                       value="{{ old('score', $enrollment->attendance_score_manual) }}"
                                       placeholder="ឧទាហរណ៍៖ 14.5"
                                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-[2rem] p-6 text-2xl font-black text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 font-bold group-focus-within:text-indigo-500">
                                    / 15
                                </div>
                            </div>

                            <div class="flex gap-3 p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                                <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-[11px] font-bold text-blue-600 leading-relaxed">
                                    {{ __('ប្រសិនបើលោកគ្រូបញ្ចូលពិន្ទុនៅទីនេះ ប្រព័ន្ធនឹងឈប់គណនាពិន្ទុតាមវត្តមាន (Auto) ហើយយកលេខនេះជាពិន្ទុចុងក្រោយភ្លាមៗ។ ទុកឱ្យនៅទំនេរវិញ ប្រសិនបើចង់ឱ្យប្រព័ន្ធគណនាដោយស្វ័យប្រវត្តិ។') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Action --}}
                    <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[1.5rem] font-black text-sm transition-all shadow-lg shadow-indigo-200 active:scale-95">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('រក្សាទុកពិន្ទុ') }}
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>