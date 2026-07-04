<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <a href="{{ route('professor.manage-grades', ['offering_id' => $courseOffering->id]) }}" 
                   class="group p-2.5 bg-white border border-slate-200 rounded-xl sm:rounded-2xl hover:bg-emerald-50 hover:border-emerald-100 transition-all duration-200 text-slate-500 hover:text-emerald-600 shadow-sm">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-xl sm:text-2xl text-slate-800 leading-tight tracking-tight">
                        {{ __('កែសម្រួលការវាយតម្លៃ') }}
                    </h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="inline-flex w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <p class="text-[10px] sm:text-[11px] font-black text-emerald-500 uppercase tracking-[0.15em] sm:tracking-[0.2em]">{{ $type }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 bg-[#f8fafc] min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-slate-200 rounded-[1.5rem] sm:rounded-[3rem] overflow-hidden transition-all duration-300">
                
                <div class="p-6 sm:p-14">
                    <form method="POST" action="{{ route('professor.assessments.update', ['id' => $assessment->id, 'type' => $type]) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6 sm:space-y-9">
                            {{-- Section: ព័ត៌មានទូទៅ --}}
                            <div class="grid grid-cols-1 gap-5 sm:gap-7">
                                {{-- ចំណងជើង (Khmer) --}}
                                <div class="group">
                                    <label class="flex items-center gap-2 text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-emerald-600 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        {{ __('ចំណងជើង (ភាសាខ្មែរ)') }}
                                    </label>
                                    <input type="text" name="title_km" value="{{ old('title_km', $assessment->title_km) }}" 
                                           class="w-full rounded-xl sm:rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 sm:py-4 px-5 sm:px-6 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all font-bold text-slate-700 placeholder-slate-300">
                                    @error('title_km') <p class="text-rose-500 text-[11px] mt-2 font-bold ml-1 italic">{{ $message }}</p> @enderror
                                </div>

                                {{-- ចំណងជើង (English) --}}
                                <div class="group">
                                    <label class="flex items-center gap-2 text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-emerald-600 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>
                                        {{ __('ចំណងជើង (English)') }}
                                    </label>
                                    <input type="text" name="title_en" value="{{ old('title_en', $assessment->title_en) }}" 
                                           class="w-full rounded-xl sm:rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 sm:py-4 px-5 sm:px-6 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all font-bold text-slate-700 placeholder-slate-300">
                                </div>
                            </div>

                            {{-- Section: ពិន្ទុ និង កាលបរិច្ឆេទ --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-7">
                                <div class="group">
                                    <label class="flex items-center gap-2 text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-emerald-600 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                        {{ __('ពិន្ទុអតិបរមា') }}
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="max_score" value="{{ old('max_score', $assessment->max_score) }}" 
                                               class="w-full rounded-xl sm:rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 sm:py-4 px-5 sm:px-6 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all font-bold text-slate-700">
                                        <div class="absolute right-5 sm:right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">{{ __('ពិន្ទុ') }}</div>
                                    </div>
                                    @error('max_score') <p class="text-rose-500 text-[11px] mt-2 font-bold ml-1 italic">{{ $message }}</p> @enderror
                                </div>

                                <div class="group">
                                    <label class="flex items-center gap-2 text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-emerald-600 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ __('កាលបរិច្ឆេទ') }}
                                    </label>
                                    <input type="date" name="assessment_date" 
                                           {{-- value="{{ old('assessment_date', $assessment->assessment_date ? \Illuminate\Support\Carbon::parse($assessment->assessment_date)->format('Y-m-d') : '') }}"    --}}
                                           value="{{ old('assessment_date', date('Y-m-d')) }}" required 
                                           
                                           class="w-full rounded-xl sm:rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 sm:py-4 px-5 sm:px-6 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all font-bold text-slate-700 cursor-pointer">
                                           
                                    @error('assessment_date') <p class="text-rose-500 text-[11px] mt-2 font-bold ml-1 italic">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Section: ប្រភេទពិន្ទុ --}}
                            <div class="group">
                                <label class="flex items-center gap-2 text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-emerald-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    {{ __('ប្រភេទពិន្ទុ (Category)') }}
                                </label>
                                <div class="relative">
                                    <select name="grading_category_id" 
                                            class="w-full rounded-xl sm:rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 sm:py-4 px-5 sm:px-6 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all font-bold text-slate-700 appearance-none cursor-pointer">
                                        <option value="">{{ __('មិនកំណត់') }}</option>
                                        @foreach($gradingCategories as $category)
                                            <option value="{{ $category->id }}" {{ ($assessment->grading_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name_km }} ({{ $category->weight_percentage }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($gradingCategories->isEmpty())
                                        <p class="text-xs text-slate-400 mt-2 italic">{{ __('មិនមានប្រភេទពិន្ទុសម្រាប់មុខវិជ្ជានេះទេ។') }}</p>
                                    @endif
                                    <div class="pointer-events-none absolute inset-y-0 right-5 sm:right-6 flex items-center text-slate-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                                @error('grading_category_id') <p class="text-rose-500 text-[11px] mt-2 font-bold ml-1 italic">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="mt-8 sm:mt-14 pt-6 sm:pt-10 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4">
                            <a href="{{ route('professor.manage-grades', ['offering_id' => $courseOffering->id]) }}" 
                               class="inline-flex justify-center items-center px-8 py-3.5 sm:py-4 bg-white text-slate-500 rounded-xl sm:rounded-2xl font-bold border border-slate-200 hover:bg-slate-50 hover:text-slate-700 transition-all active:scale-95">
                                {{ __('បោះបង់') }}
                            </a>
                            <button type="submit" 
                                    class="inline-flex justify-center items-center px-8 py-3.5 sm:py-4 bg-emerald-600 text-white rounded-xl sm:rounded-2xl font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-700 hover:-translate-y-0.5 transition-all active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                {{ __('រក្សាទុក') }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>