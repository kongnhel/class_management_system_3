<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto px-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('professor.manage-grades', ['offering_id' => $courseOffering->id]) }}"
                   class="p-2 bg-white border border-slate-200 rounded-xl text-slate-500 hover:bg-emerald-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-lg sm:text-xl text-gray-800 leading-tight tracking-tight">
                        {{ __('បង្កើតការវាយតម្លៃថ្មី') }}
                    </h2>
                    <p class="text-[11px] sm:text-sm text-gray-500 mt-0.5">
                        {{ __('មុខវិជ្ជា:') }} <span class="font-bold text-emerald-600">{{ $courseOffering->course->title_km }}</span>
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Messages --}}
    <div class="max-w-7xl mx-auto px-4 mt-6">
        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm flex items-center gap-3" role="alert">
                <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                <p class="font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-xl shadow-sm flex items-center gap-3" role="alert">
                <svg class="h-5 w-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                <p class="font-bold text-sm">{{ session('error') }}</p>
            </div>
        @endif
    </div>

    <div class="py-6 sm:py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT: Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-xl shadow-gray-200/50 rounded-2xl sm:rounded-3xl border border-gray-100">
                        <div class="p-6 sm:p-10">
                            <form action="{{ route('professor.assessments.store', ['offering_id' => $courseOffering->id]) }}" method="POST" id="assessmentForm">
                                @csrf
                                <input type="hidden" name="assessment_type" id="assessment_type" value="{{ old('assessment_type', '') }}">
                                <input type="hidden" name="title_km" id="title_km" value="">
                                <input type="hidden" name="title_en" id="title_en" value="">

                                <div class="space-y-5 sm:space-y-6">

                                    {{-- Assessment Type Selection --}}
                                    <div class="group">
                                        <label class="flex items-center gap-2 text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            ប្រភេទការវាយតម្លៃ <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="typeGrid">
                                            {{-- Midterm Exam --}}
                                            <button type="button" onclick="selectType('midterm')"
                                                    class="type-card group p-4 rounded-xl border-2 border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-all text-center"
                                                    data-type="midterm" data-title-km="ប្រឡងពាក់កណ្ដាល់ឆមាស" data-title-en="Midterm Exam"
                                                    data-grading="Midterm Exam" data-icon="📝" data-color="amber" data-default-score="100">
                                                <span class="text-2xl block mb-1">📝</span>
                                                <span class="text-[10px] font-bold text-gray-600 group-hover:text-amber-700 uppercase leading-tight block">ប្រឡងពាក់កណ្ដាល់ឆមាស</span>
                                                <span class="text-[9px] text-gray-400 block mt-1">Midterm Exam</span>
                                                <span class="text-[9px] font-black text-amber-500 block mt-1">15%</span>
                                            </button>
                                            {{-- Group Assignment --}}
                                            <button type="button" onclick="selectType('assignment')"
                                                    class="type-card group p-4 rounded-xl border-2 border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all text-center"
                                                    data-type="assignment" data-title-km="កិច្ចការស្រាវជ្រាវ" data-title-en="Group Assignment"
                                                    data-grading="Group Assignment" data-icon="📋" data-color="blue" data-default-score="100">
                                                <span class="text-2xl block mb-1">📋</span>
                                                <span class="text-[10px] font-bold text-gray-600 group-hover:text-emerald-700 uppercase leading-tight block">កិច្ចការស្រាវជ្រាវ</span>
                                                <span class="text-[9px] text-gray-400 block mt-1">Group Assignment</span>
                                                <span class="text-[9px] font-black text-emerald-500 block mt-1">20%</span>
                                            </button>
                                            {{-- Final Exam --}}
                                            <button type="button" onclick="selectType('final')"
                                                    class="type-card group p-4 rounded-xl border-2 border-gray-200 hover:border-rose-300 hover:bg-rose-50 transition-all text-center"
                                                    data-type="final" data-title-km="ប្រឡងប្រចាំឆមាស" data-title-en="Final Exam"
                                                    data-grading="Final Exam" data-icon="🎓" data-color="rose" data-default-score="100">
                                                <span class="text-2xl block mb-1">🎓</span>
                                                <span class="text-[10px] font-bold text-gray-600 group-hover:text-rose-700 uppercase leading-tight block">ប្រឡងប្រចាំឆមាស</span>
                                                <span class="text-[9px] text-gray-400 block mt-1">Final Exam</span>
                                                <span class="text-[9px] font-black text-rose-500 block mt-1">50%</span>
                                            </button>
                                            {{-- Quiz (Optional) --}}
                                            <button type="button" onclick="selectType('quiz')"
                                                    class="type-card group p-4 rounded-xl border-2 border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all text-center"
                                                    data-type="quiz" data-title-km="កម្រងសំណួរ" data-title-en="Quiz"
                                                    data-grading="" data-icon="⚡" data-color="purple" data-default-score="20">
                                                <span class="text-2xl block mb-1">⚡</span>
                                                <span class="text-[10px] font-bold text-gray-600 group-hover:text-purple-700 uppercase leading-tight block">កម្រងសំណួរ</span>
                                                <span class="text-[9px] text-gray-400 block mt-1">Quiz</span>
                                                <span class="text-[9px] font-black text-purple-500 block mt-1">Optional</span>
                                            </button>
                                        </div>
                                        @error('assessment_type')<p class="text-rose-500 text-xs mt-2 font-medium ml-1">{{ $message }}</p>@enderror
                                    </div>

                                    {{-- Auto-filled Title Display --}}
                                    <div id="titleDisplay" class="hidden">
                                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                            <div class="flex items-center gap-3">
                                                <span id="titleIcon" class="text-2xl">📝</span>
                                                <div>
                                                    <p id="titleKm" class="font-bold text-gray-800 text-sm"></p>
                                                    <p id="titleEn" class="text-xs text-gray-500"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Date --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="group">
                                            <label for="assessment_date" class="flex items-center gap-2 text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span id="dateLabel">{{ __('កាលបរិច្ឆេទ') }}</span> <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="date" name="assessment_date" id="assessment_date" value="{{ old('assessment_date', date('Y-m-d')) }}" required
                                                   class="w-full bg-gray-50 border-gray-200 focus:bg-white focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 rounded-xl py-3.5 px-5 transition-all font-bold text-gray-700">
                                            @error('assessment_date')<p class="text-rose-500 text-xs mt-2 font-medium ml-1">{{ $message }}</p>@enderror
                                        </div>

                                        <div class="group">
                                            <label for="max_score" class="flex items-center gap-2 text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                                ពិន្ទុអតិបរមា <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="number" name="max_score" id="max_score" value="{{ old('max_score', 100) }}" required min="1"
                                                   class="w-full bg-gray-50 border-gray-200 focus:bg-white focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 rounded-xl py-3.5 px-5 transition-all font-bold text-gray-700">
                                            @error('max_score')<p class="text-rose-500 text-xs mt-2 font-medium ml-1">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    {{-- Duplicate Warning --}}
                                    <div id="duplicateWarning" class="hidden">
                                        <div class="bg-amber-50 border border-amber-200 text-amber-700 p-4 rounded-xl flex items-center gap-3">
                                            <svg class="h-5 w-5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            <p class="text-sm font-bold" id="duplicateMessage"></p>
                                        </div>
                                    </div>

                                </div>

                                {{-- Footer --}}
                                <div class="mt-10 pt-8 border-t border-gray-100 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                                    <a href="{{ route('professor.manage-grades', ['offering_id' => $courseOffering->id]) }}"
                                       class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3.5 border border-gray-200 text-sm font-bold rounded-xl text-gray-500 bg-white hover:bg-gray-50 transition-all active:scale-95">
                                        បោះបង់
                                    </a>
                                    <button type="submit" id="submitBtn" disabled
                                            class="w-full sm:w-auto inline-flex justify-center items-center px-10 py-3.5 border border-transparent text-sm font-bold rounded-xl shadow-xl shadow-emerald-100 text-white bg-emerald-400 cursor-not-allowed transition-all transform disabled:active:scale-100">
                                        រក្សាទុកការវាយតម្លៃ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Sidebar --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Live Preview --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/50 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                មើលជាមុន
                            </h3>
                        </div>
                        <div class="p-5">
                            <div id="previewCard" class="space-y-3">
                                <div id="previewEmpty" class="text-center py-6">
                                    <p class="text-sm text-gray-400 italic">សូមជ្រើសរើសប្រភេទការវាយតម្លៃ</p>
                                </div>
                                <div id="previewContent" class="hidden space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span id="previewTypeIcon" class="text-lg">📝</span>
                                        <span id="previewType" class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-amber-50 text-amber-600">---</span>
                                    </div>
                                    <p id="previewTitle" class="font-bold text-gray-800 text-sm leading-tight">---</p>
                                    <div class="flex items-center gap-3 text-[11px] text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span id="previewDate">{{ date('d/m/Y') }}</span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span id="previewScore">100 ពិន្ទុ</span>
                                        </span>
                                    </div>
                                    <div id="previewDurationRow" class="hidden flex items-center gap-1 text-[11px] text-gray-500">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span id="previewDuration">120 នាទី</span>
                                    </div>
                                    <div class="pt-2 border-t border-gray-100">
                                        <div class="flex items-center gap-1 text-[11px]">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            <span class="text-gray-400">ប្រភេទពិន្ទុ:</span>
                                            <span id="previewCategory" class="font-bold text-gray-600">---</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Category Budget --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/50 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                ថវិកាពិន្ទុ
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">
                            @forelse($gradingCategories as $category)
                                @php
                                    $catAssignments = $category->assignments()->where('course_offering_id', $courseOffering->id)->sum('max_score');
                                    $catQuizzes = \App\Models\Quiz::where('grading_category_id', $category->id)->where('course_offering_id', $courseOffering->id)->sum('max_score');
                                    $usedScore = $catAssignments + $catQuizzes;
                                @endphp
                                <div class="category-budget-item" data-category-id="{{ $category->id }}">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[11px] font-bold text-gray-600">{{ $category->name_km }}</span>
                                        <span class="text-[10px] font-black text-gray-400">{{ $category->weight_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $usedScore > 0 ? 'bg-emerald-500' : 'bg-gray-200' }}"
                                             style="width: {{ min($usedScore > 0 ? ($usedScore / 100) * 100 : 0, 100) }}%"></div>
                                    </div>
                                    <p class="text-[9px] text-gray-400 mt-1">{{ $usedScore }} ពិន្ទុប្រើប្រាស់រួចហើយ</p>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 text-center py-2">មិនមានប្រភេទពិន្ទុ</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Course Evaluation Structure --}}
                    <div class="bg-emerald-50 rounded-2xl border border-emerald-100 p-5">
                        <h3 class="text-[11px] font-black text-emerald-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            រចនាសម្ព័ន្ធវាយតម្លៃ
                        </h3>
                        <ul class="text-[11px] text-emerald-700 space-y-2">
                            <li class="flex items-center justify-between">
                                <span>វត្តមាន (Attendance)</span>
                                <span class="font-black">15%</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span>ប្រឡងពាក់កណ្ដាល់ឆមាស (Midterm)</span>
                                <span class="font-black">15%</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span>កិច្ចការស្រាវជ្រាវ (Assignment)</span>
                                <span class="font-black">20%</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span>ប្រឡងប្រចាំឆមាស (Final)</span>
                                <span class="font-black">50%</span>
                            </li>
                            <li class="border-t border-emerald-200 pt-2 flex items-center justify-between font-black">
                                <span>{{ __('សរុប') }}</span>
                                <span>100%</span>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script>
        const gradingCategories = @json($gradingCategories);
        const offeringId = {{ $courseOffering->id }};

        const typeConfigs = {
            midterm: {
                assessmentType: 'exam',
                titleKm: 'ប្រឡងពាក់កណ្ដាល់ឆមាស',
                titleEn: 'Midterm Exam',
                gradingName: 'Midterm Exam',
                icon: '📝', color: 'amber',
                defaultScore: 15,
                showDuration: true,
                dateLabel: 'កាលបរិច្ឆេទប្រឡង',
            },
            assignment: {
                assessmentType: 'assignment',
                titleKm: 'កិច្ចការស្រាវជ្រាវ',
                titleEn: 'Group Assignment',
                gradingName: 'Group Assignment',
                icon: '📋', color: 'blue',
                defaultScore: 20,
                showDuration: false,
                dateLabel: 'កាលបរិច្ឆេទដាក់ស្នើ',
            },
            final: {
                assessmentType: 'exam',
                titleKm: 'ប្រឡងប្រចាំឆមាស',
                titleEn: 'Final Exam',
                gradingName: 'Final Exam',
                icon: '🎓', color: 'rose',
                defaultScore: 50,
                showDuration: true,
                dateLabel: 'កាលបរិច្ឆេទប្រឡង',
            },
            quiz: {
                assessmentType: 'quiz',
                titleKm: 'កម្រងសំណួរ',
                titleEn: 'Quiz',
                gradingName: '',
                icon: '⚡', color: 'purple',
                defaultScore: 20,
                showDuration: false,
                dateLabel: 'កាលបរិច្ឆេទសំណួរ',
            },
        };

        let selectedType = null;

        function selectType(type) {
            selectedType = type;
            const config = typeConfigs[type];

            // Update hidden fields
            document.getElementById('assessment_type').value = config.assessmentType;
            document.getElementById('title_km').value = config.titleKm;
            document.getElementById('title_en').value = config.titleEn;

            // Update card styles
            document.querySelectorAll('.type-card').forEach(card => {
                card.classList.remove('border-amber-400', 'bg-amber-50', 'border-emerald-400', 'bg-emerald-50', 'border-rose-400', 'bg-rose-50', 'border-purple-400', 'bg-purple-50');
                card.classList.add('border-gray-200');
            });
            const activeCard = document.querySelector(`[data-type="${type}"]`);
            activeCard.classList.remove('border-gray-200');
            activeCard.classList.add(`border-${config.color}-400`, `bg-${config.color}-50`);

            // Update title display
            document.getElementById('titleDisplay').classList.remove('hidden');
            document.getElementById('titleIcon').textContent = config.icon;
            document.getElementById('titleKm').textContent = config.titleKm;
            document.getElementById('titleEn').textContent = config.titleEn;

            // Update date label
            document.getElementById('dateLabel').textContent = config.dateLabel;

            // Update max_score default
            document.getElementById('max_score').value = config.defaultScore;

            // Enable submit
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-emerald-400', 'cursor-not-allowed');
            submitBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');

            updatePreview();
            checkDuplicate();
        }

        function updatePreview() {
            if (!selectedType) return;
            const config = typeConfigs[selectedType];
            const maxScore = document.getElementById('max_score').value || '0';
            const date = document.getElementById('assessment_date').value;
            const duration = '';

            document.getElementById('previewEmpty').classList.add('hidden');
            document.getElementById('previewContent').classList.remove('hidden');

            document.getElementById('previewTypeIcon').textContent = config.icon;
            document.getElementById('previewType').textContent = config.titleKm;
            document.getElementById('previewType').className = `text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-${config.color}-50 text-${config.color}-600`;
            document.getElementById('previewTitle').textContent = `${config.titleKm} (${config.titleEn})`;
            document.getElementById('previewScore').textContent = `${maxScore} ពិន្ទុ`;

            if (date) {
                const d = new Date(date);
                document.getElementById('previewDate').textContent = d.toLocaleDateString('km-KH', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }

            document.getElementById('previewDurationRow').classList.toggle('hidden', !config.showDuration);
            if (config.showDuration) {
                document.getElementById('previewDuration').textContent = `${duration || 120} នាទី`;
            }

            document.getElementById('previewCategory').textContent = config.gradingName || 'កម្រងសំណួរ';
        }

        function checkDuplicate() {
            if (!selectedType) return;
            const config = typeConfigs[selectedType];
            const warning = document.getElementById('duplicateWarning');

            fetch(`/professor/api/check-duplicate?offering_id=${offeringId}&type=${config.assessmentType}&title_km=${encodeURIComponent(config.titleKm)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.duplicate) {
                        document.getElementById('duplicateMessage').textContent = data.message || 'វិញ្ញាសានេះអាចមានរួចហើយ!';
                        warning.classList.remove('hidden');
                    } else {
                        warning.classList.add('hidden');
                    }
                })
                .catch(() => warning.classList.add('hidden'));
        }

        document.getElementById('max_score').addEventListener('input', updatePreview);

        document.getElementById('assessment_date').addEventListener('change', updatePreview);

        document.addEventListener('DOMContentLoaded', function() {
            @if(old('assessment_type'))
                const savedType = '{{ old("assessment_type") }}';
                // Map assessment_type back to our type keys
                @if(old('assessment_type') === 'exam')
                    // Could be midterm or final, default to what was saved
                @endif
            @endif
        });
    </script>
</x-app-layout>
