<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="font-extrabold text-xl md:text-2xl text-slate-800 leading-tight tracking-tight">
                        {{ __('បញ្ចូលពិន្ទុ') }}
                    </h2>
                    <div class="flex items-center mt-1 text-slate-500 space-x-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $assessment->courseOffering->course->title_km }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 md:gap-3">
                    @php
                        $type = 'exam';
                        if ($assessment instanceof \App\Models\Assignment) $type = 'assignment';
                        if ($assessment instanceof \App\Models\Quiz) $type = 'quiz';
                    @endphp

                    <a href="{{ route('grades.export', ['id' => $assessment->id, 'type' => $type]) }}"
                       class="flex-1 md:flex-none inline-flex justify-center items-center px-3 py-2 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl text-[11px] font-bold hover:bg-emerald-100 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('ទាញយក CSV') }}
                    </a>
                    <form id="importForm" action="{{ route('grades.import', ['id' => $assessment->id]) }}" method="POST" enctype="multipart/form-data" class="flex-1 md:flex-none">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="offering_id" value="{{ $assessment->course_offering_id }}">
                        <input type="file" id="csvFileInput" name="excel_file" class="hidden" accept=".csv" onchange="document.getElementById('importForm').submit();">
                        <button type="button" onclick="document.getElementById('csvFileInput').click();"
                                class="w-full inline-flex justify-center items-center px-3 py-2 bg-amber-50 text-amber-700 border border-amber-100 rounded-xl text-[11px] font-bold hover:bg-amber-100 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            {{ __('បញ្ចូលតាម Excel') }}
                        </button>
                    </form>

                    <div class="w-full lg:w-auto flex items-center gap-4 bg-slate-50 lg:bg-white p-2 md:p-2.5 rounded-2xl border border-slate-100 shadow-sm mt-2 lg:mt-0">
                        <div class="flex-1 lg:text-right lg:pr-4 lg:border-r border-slate-200">
                            <p class="text-[9px] text-slate-400 uppercase font-black tracking-widest leading-none">{{ __('ការវាយតម្លៃ') }}</p>
                            <p class="text-xs md:text-sm font-bold text-slate-700 mt-1">{{ $assessment->title_km }}</p>
                        </div>
                        <div class="text-center px-4">
                            <p class="text-[9px] text-slate-400 uppercase font-black tracking-widest leading-none">{{ __('អតិបរមា') }}</p>
                            <p class="text-xs md:text-sm font-black text-emerald-600 mt-1">{{ $assessment->max_score }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 bg-[#f8fafc] min-h-screen pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6" id="statsPanel">
                <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-sm">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ __('សរុប') }}</p>
                    <p class="text-xl font-black text-slate-700 mt-1" id="statTotal">{{ count($students) }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-sm">
                    <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">{{ __('បំពេញរួច') }}</p>
                    <p class="text-xl font-black text-emerald-600 mt-1" id="statGraded">0</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-sm">
                    <p class="text-[9px] font-black text-amber-400 uppercase tracking-widest">{{ __('នៅសល់') }}</p>
                    <p class="text-xl font-black text-amber-600 mt-1" id="statPending">0</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-sm">
                    <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">{{ __('មធ្យម') }}</p>
                    <p class="text-xl font-black text-emerald-600 mt-1" id="statAvg">0.0</p>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('ដំណើរការបញ្ចូលពិន្ទុ') }}</span>
                    <span class="text-[10px] font-black text-emerald-500" id="progressText">0/0</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div id="progressBar" class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
            </div>

            {{-- Toolbar: Search + Filters + Batch --}}
            <div class="flex flex-col md:flex-row gap-3 mb-6">
                {{-- Search --}}
                <form action="{{ url()->current() }}" method="GET" class="relative flex-1 md:max-w-sm">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="{{ __('ស្វែងរកឈ្មោះ ឬ អត្តលេខ...') }}"
                           class="block w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-sm">
                </form>

                {{-- Filter Tabs --}}
                <div class="flex bg-white rounded-xl border border-slate-200 p-1 shadow-sm">
                    <button type="button" onclick="setFilter('all')" data-filter="all"
                            class="filter-btn flex-1 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all bg-emerald-50 text-emerald-600">
                        {{ __('ទាំងអស់') }}
                    </button>
                    <button type="button" onclick="setFilter('ungraded')" data-filter="ungraded"
                            class="filter-btn flex-1 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all text-slate-400 hover:text-slate-600">
                        {{ __('មិនទាន់បំពេញ') }}
                    </button>
                    <button type="button" onclick="setFilter('graded')" data-filter="graded"
                            class="filter-btn flex-1 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all text-slate-400 hover:text-slate-600">
                        {{ __('បំពេញរួច') }}
                    </button>
                </div>

                {{-- Batch Fill --}}
                <div class="flex gap-2">
                    <button type="button" onclick="openBatchFillModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black text-slate-500 uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        {{ __('បំពេញទាំងអស់') }}
                    </button>
                </div>
            </div>

            {{-- Main Form --}}
            <form id="grade-form" action="{{ route('professor.grades.store', ['assessment_id' => $assessment->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="assessment_type" value="{{ $type }}">

                <div class="bg-white shadow-sm border border-slate-200 rounded-[2rem] overflow-hidden mb-12">
                    <table class="w-full text-left border-collapse">
                        <thead class="hidden md:table-header-group">
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest w-16">#</th>
                                <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">{{ __('ព័ត៌មាននិស្សិត') }}</th>
                                <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest w-48 text-center">{{ __('ពិន្ទុទទួលបាន') }}</th>
                                <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">{{ __('កំណត់ចំណាំ') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="studentRows">
                            @forelse ($students as $index => $student)
                                @php
                                    $existingScore = $scores[$student->id]['score'] ?? null;
                                    $isGraded = $existingScore !== null && $existingScore !== '';
                                @endphp
                                <tr class="flex flex-col md:table-row hover:bg-slate-50/50 transition-colors p-5 md:p-0 student-row"
                                    data-student-id="{{ $student->id }}"
                                    data-graded="{{ $isGraded ? '1' : '0' }}">

                                    <td class="hidden md:table-cell px-6 py-4 text-xs font-bold text-slate-400">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="px-6 py-2 md:py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-black text-xs">
                                                {{ mb_substr($student->studentProfile?->full_name_km ?? $student->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-bold text-slate-800">{{ $student->studentProfile?->full_name_km ?? $student->name }}</p>
                                                <p class="text-[10px] text-slate-400 font-bold tracking-widest uppercase">{{ $student->student_id_code }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-2 md:py-4">
                                        <label class="md:hidden text-[10px] font-black uppercase text-slate-400 mb-1 block">{{ __('ពិន្ទុទទួលបាន') }}</label>
                                        <div class="relative">
                                            <input type="number"
                                                name="grades[{{ $student->id }}][score]"
                                                value="{{ old('grades.'.$student->id.'.score', $existingScore) }}"
                                                min="0" max="{{ $assessment->max_score }}" step="0.01"
                                                data-max="{{ $assessment->max_score }}"
                                                oninput="validateScore(this); updateStats();"
                                                class="score-input block w-full text-center py-2 bg-slate-50 border-2 border-transparent rounded-xl text-sm font-black text-emerald-700 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none"
                                                placeholder="0.00">
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-300 pointer-events-none">
                                                /{{ $assessment->max_score }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-2 md:py-4">
                                        <label class="md:hidden text-[10px] font-black uppercase text-slate-400 mb-1 block">{{ __('មតិយោបល់') }}</label>
                                        <div class="relative">
                                            <input type="text"
                                                name="grades[{{ $student->id }}][notes]"
                                                value="{{ old('grades.'.$student->id.'.notes', $scores[$student->id]['notes'] ?? '') }}"
                                                class="notes-input block w-full px-4 py-2 bg-slate-50 border-transparent rounded-xl text-xs font-medium text-slate-600 focus:bg-white focus:ring-2 focus:ring-emerald-500/10 transition-all outline-none"
                                                placeholder="...">
                                            <div class="absolute right-2 top-1/2 -translate-y-1/2 flex gap-1 opacity-0 hover:opacity-100 focus-within:opacity-100 transition-opacity">
                                                <button type="button" onclick="setQuickNote(this, 'ល្អ')" class="quick-note text-[8px] px-1.5 py-0.5 bg-emerald-50 text-emerald-600 rounded-md border border-emerald-100 font-bold hover:bg-emerald-100" title="ល្អ">ល្អ</button>
                                                <button type="button" onclick="setQuickNote(this, 'ត្រូវកែ')" class="quick-note text-[8px] px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded-md border border-amber-100 font-bold hover:bg-amber-100" title="ត្រូវកែ">កែ</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-20 text-center text-slate-400 font-bold">{{ __('រកមិនឃើញនិស្សិត') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            @if(session('success'))
                <div class="fixed top-4 right-4 z-50 animate-bounce">
                    <div class="bg-emerald-500 text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3 font-bold text-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Sticky Actions Bar --}}
    <div class="fixed bottom-0 inset-x-0 bg-white/90 backdrop-blur-xl border-t border-slate-200 py-4 z-40 shadow-[0_-10px_25px_rgba(0,0,0,0.05)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
            <a href="{{ route('professor.manage-grades', ['offering_id' => $assessment->course_offering_id]) }}"
               class="p-3 md:px-6 md:py-3 text-slate-500 hover:text-emerald-600 transition-colors">
                <svg class="w-6 h-6 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span class="hidden md:inline-flex items-center text-xs font-bold uppercase tracking-wider">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    {{ __('ត្រឡប់ក្រោយ') }}
                </span>
            </a>

            <div class="flex-1 md:flex-none flex items-center justify-end gap-4">
                <span class="hidden md:inline text-[11px] font-black text-slate-400 uppercase tracking-widest" id="unsavedIndicator" style="display:none">
                    <span class="inline-block w-2 h-2 bg-amber-400 rounded-full animate-pulse mr-1"></span>
                    {{ __('មានការផ្លាស់ប្តូរ') }}
                </span>
                <span class="hidden md:inline text-[11px] font-black text-slate-400 uppercase tracking-widest">
                    {{ count($students) }} {{ __('និស្សិតសរុប') }}
                </span>
                <button type="submit" form="grade-form" class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-sm shadow-xl shadow-emerald-200 transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    {{ __('រក្សាទុកទាំងអស់') }}
                    <span class="hidden md:inline ml-2 text-[10px] opacity-60">(Ctrl+S)</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Batch Fill Modal --}}
    <div id="batchFillModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeBatchFillModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-3xl shadow-2xl p-8 w-[90%] max-w-md">
            <h3 class="text-lg font-black text-slate-800 mb-1">{{ __('បំពេញពិន្ទុទាំងអស់') }}</h3>
            <p class="text-xs text-slate-500 mb-6">{{ __('បញ្ចូលពិន្ទុដែលចង់បំពេញសម្រាប់និស្សិតដែលមិនទាន់មានពិន្ទុ') }}</p>
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('ពិន្ទុ') }}</label>
                    <input type="number" id="batchScore" min="0" max="{{ $assessment->max_score }}" step="0.01"
                           class="w-full mt-1 px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-lg font-black text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all"
                           placeholder="0">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('កំណត់ចំណាំ (ជាជម្រើស)') }}</label>
                    <input type="text" id="batchNote"
                           class="w-full mt-1 px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all"
                           placeholder="ឧ. ល្អ, ត្រូវកែ...">
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeBatchFillModal()" class="flex-1 px-4 py-3 border border-slate-200 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">{{ __('បោះបង់') }}</button>
                <button type="button" onclick="applyBatchFill()" class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 rounded-xl text-sm font-black text-white transition-all">{{ __('អនុវត្ត') }}</button>
            </div>
        </div>
    </div>

    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
        .score-input.score-error { border-color: #ef4444 !important; background-color: #fef2f2 !important; }
        .score-input.score-valid { border-color: #22c55e !important; background-color: #f0fdf4 !important; }
        .student-row.row-hidden { display: none !important; }
    </style>

    <script>
        const maxScore = {{ $assessment->max_score }};
        const totalStudents = {{ count($students) }};
        let hasUnsavedChanges = false;
        let currentFilter = 'all';

        // --- Score Validation ---
        function validateScore(input) {
            const val = parseFloat(input.value);
            input.classList.remove('score-error', 'score-valid');
            if (input.value === '' || input.value === null) return;
            if (isNaN(val) || val < 0 || val > maxScore) {
                input.classList.add('score-error');
            } else {
                input.classList.add('score-valid');
            }
        }

        // --- Stats ---
        function updateStats() {
            const inputs = document.querySelectorAll('.score-input');
            let graded = 0, total = 0, sum = 0;
            inputs.forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val) && input.value !== '') {
                    graded++;
                    sum += val;
                }
            });
            const pending = totalStudents - graded;
            const avg = graded > 0 ? (sum / graded).toFixed(1) : '0.0';
            const pct = totalStudents > 0 ? Math.round((graded / totalStudents) * 100) : 0;

            document.getElementById('statGraded').textContent = graded;
            document.getElementById('statPending').textContent = pending;
            document.getElementById('statAvg').textContent = avg;
            document.getElementById('progressText').textContent = `${graded}/${totalStudents}`;
            document.getElementById('progressBar').style.width = `${pct}%`;

            if (pct === 100) {
                document.getElementById('progressBar').classList.remove('bg-emerald-500');
                document.getElementById('progressBar').classList.add('bg-emerald-500');
            } else {
                document.getElementById('progressBar').classList.remove('bg-emerald-500');
                document.getElementById('progressBar').classList.add('bg-emerald-500');
            }

            hasUnsavedChanges = true;
            document.getElementById('unsavedIndicator').style.display = 'inline';
        }

        // --- Filter ---
        function setFilter(filter) {
            currentFilter = filter;
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-emerald-50', 'text-emerald-600');
                btn.classList.add('text-slate-400');
            });
            const activeBtn = document.querySelector(`[data-filter="${filter}"]`);
            activeBtn.classList.add('bg-emerald-50', 'text-emerald-600');
            activeBtn.classList.remove('text-slate-400');

            document.querySelectorAll('.student-row').forEach(row => {
                const graded = row.dataset.graded;
                row.classList.remove('row-hidden');
                if (filter === 'graded' && graded !== '1') row.classList.add('row-hidden');
                if (filter === 'ungraded' && graded === '1') row.classList.add('row-hidden');
            });
        }

        // --- Quick Note ---
        function setQuickNote(btn, text) {
            const input = btn.closest('td').querySelector('.notes-input');
            input.value = text;
            input.focus();
            hasUnsavedChanges = true;
            document.getElementById('unsavedIndicator').style.display = 'inline';
        }

        // --- Batch Fill Modal ---
        function openBatchFillModal() {
            document.getElementById('batchFillModal').classList.remove('hidden');
            document.getElementById('batchScore').value = '';
            document.getElementById('batchNote').value = '';
            document.getElementById('batchScore').focus();
        }
        function closeBatchFillModal() {
            document.getElementById('batchFillModal').classList.add('hidden');
        }
        function applyBatchFill() {
            const score = document.getElementById('batchScore').value;
            const note = document.getElementById('batchNote').value;
            document.querySelectorAll('.student-row:not(.row-hidden)').forEach(row => {
                const scoreInput = row.querySelector('.score-input');
                const noteInput = row.querySelector('.notes-input');
                if (score !== '' && (scoreInput.value === '' || scoreInput.value === null)) {
                    scoreInput.value = score;
                    validateScore(scoreInput);
                }
                if (note !== '' && (noteInput.value === '' || noteInput.value === '...')) {
                    noteInput.value = note;
                }
            });
            closeBatchFillModal();
            updateStats();
        }

        // --- Unsaved Changes Warning ---
        window.addEventListener('beforeunload', function(e) {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Mark clean on form submit
        document.getElementById('grade-form').addEventListener('submit', function() {
            hasUnsavedChanges = false;
        });

        // --- Ctrl+S Shortcut ---
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.getElementById('grade-form').submit();
            }
        });

        // --- Init ---
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.score-input').forEach(input => {
                validateScore(input);
            });
            updateStats();
        });
    </script>
</x-app-layout>
