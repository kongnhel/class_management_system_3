<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 print:hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="text-center lg:text-left">
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight tracking-tight">
                        {{ __('តារាងពិន្ទុរួម') }}
                    </h2>
                    <div class="flex items-center justify-center lg:justify-start mt-1 text-slate-500 space-x-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('ការគ្រប់គ្រង និងតាមដានលទ្ធផលសិក្សា') }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center lg:justify-end gap-3">
                    <div class="hidden lg:block text-right pr-4 border-r border-slate-200">
                        <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest leading-none">{{ __('មុខវិជ្ជា') }}</p>
                        <p class="text-sm font-bold text-emerald-600 mt-1 leading-none">{{ $courseOffering->course->title_km }}</p>
                    </div>

                    <div class="grid grid-cols-3 md:flex items-center gap-3 w-full md:w-auto">
                        <a href="{{ route('professor.assessments.create', ['offering_id' => $courseOffering->id]) }}"
                            class="group inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-emerald-200">
                            <svg class="w-4 h-4 mr-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            {{ __('បង្កើតការវាយតម្លៃ') }}
                        </a>

                        <a href="{{ route('professor.grades.export-docx', ['offering_id' => $courseOffering->id]) }}"
                            class="group inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-emerald-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
                            </svg>
                            {{ __('ទាញយក (.doc)') }}
                        </a>

                        <button onclick="window.print()"
                            class="group inline-flex items-center justify-center px-4 py-2.5 bg-slate-600 hover:bg-slate-700 text-white rounded-2xl font-bold text-xs transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            {{ __('បោះពុម្ព') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $totalStudents = count($students);
        $totalSum = 0;
        $totalMax = 0;
        $passCount = 0;
        $failCount = 0;
        $highestScore = 0;
        $lowestScore = $totalStudents > 0 ? 999999 : 0;

        foreach ($students as $student) {
            $attendanceScore = $student->getAttendanceScoreByCourse($courseOffering->id);
            $baseScore = $attendanceScore;
            $quizBonus = 0;
            foreach ($assessments as $assessment) {
                $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                $score = $gradebook[$student->id][$type . '_' . $assessment->id] ?? 0;
                if ($type === 'quiz') {
                    $quizBonus += $score;
                } else {
                    $baseScore += $score;
                }
            }
            $rowTotal = min($baseScore + $quizBonus, 100);
            $totalSum += $rowTotal;
            $totalMax += 100;
            if ($rowTotal > $highestScore) $highestScore = $rowTotal;
            if ($rowTotal < $lowestScore) $lowestScore = $rowTotal;
            $grade = \App\Services\GradingService::getLetterGrade($rowTotal);
            if (\App\Services\GradingService::isPassing($grade)) {
                $passCount++;
            } else {
                $failCount++;
            }
        }
        $classAvg = $totalStudents > 0 ? $totalSum / $totalStudents : 0;
        $passRate = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100) : 0;
        if ($lowestScore == 999999) $lowestScore = 0;
        $maleCount = 0;
        $femaleCount = 0;
        foreach ($students as $student) {
            if ($student->profile?->gender == 'male') { $maleCount++; } else { $femaleCount++; }
        }
    @endphp

    <div class="py-8 bg-[#f8fafc] min-h-screen print:bg-white">
        <div class="max-w-[98%] mx-auto px-4 sm:px-6 lg:px-8 print:px-0 print:max-w-none">

            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 md:p-5 rounded-xl mb-6 shadow-sm flex items-center animate-fade-in-down print:hidden" role="alert">
                    <i class="fas fa-check-circle mr-3 text-green-500 text-xl"></i>
                    <span class="font-bold text-sm md:text-lg">{{ session('success') }}</span>
                </div>
            @elseif(session('error'))
                <div class="mb-6 flex items-center p-4 text-rose-800 bg-rose-50 rounded-2xl border border-rose-100 shadow-sm animate-fade-in-down print:hidden">
                    <div class="p-2 bg-rose-500 rounded-lg mr-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-bold italic">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-orange-50 border border-orange-100 text-orange-700 rounded-2xl print:hidden">
                    <ul class="list-disc list-inside text-xs font-bold space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Summary Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6 print:hidden">
                <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-sm">
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">{{ __('និស្សិតសរុប') }}</p>
                    <p class="text-2xl font-black text-slate-700 mt-1">{{ $totalStudents }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-emerald-200 p-4 text-center shadow-sm">
                    <p class="text-[11px] font-black text-emerald-400 uppercase tracking-widest">{{ __('មធ្យមពិន្ទុ') }}</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($classAvg, 1) }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-emerald-200 p-4 text-center shadow-sm">
                    <p class="text-[11px] font-black text-emerald-500 uppercase tracking-widest">{{ __('អត្រាប្រឡងជាប់') }}</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">{{ $passRate }}%</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-sm">
                    <p class="text-[11px] font-black text-amber-500 uppercase tracking-widest">{{ __('ពិន្ទុខ្ពស់/ទាប') }}</p>
                    <p class="text-sm font-black text-slate-700 mt-1">{{ number_format($highestScore, 1) }} / {{ number_format($lowestScore, 1) }}</p>
                </div>
            </div>

            {{-- Search Bar + Excel Operations --}}
            <div class="mb-6 print:hidden">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="flex flex-col lg:flex-row items-stretch">
                        {{-- Search --}}
                        <div class="flex-1 p-3 border-b lg:border-b-0 lg:border-r border-slate-100">
                            <div class="relative">
                                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="studentSearch" oninput="filterStudents()"
                                    placeholder="ស្វែងរកឈ្មោះ ឬ អត្តលេខ..."
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-0 rounded-xl text-sm font-medium text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500/20 focus:bg-white transition-all">
                            </div>
                        </div>

                        {{-- Excel Actions --}}
                        <div class="flex items-center gap-2 p-3 bg-slate-50/50">
                            {{-- Export --}}
                            <a href="{{ route('professor.grades.export-excel', ['offering_id' => $courseOffering->id]) }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs transition-all shadow-sm hover:shadow-md hover:shadow-emerald-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                {{ __('ទាញយក') }}
                            </a>

                            <div class="w-px h-8 bg-slate-200"></div>

                            {{-- Import --}}
                            <form action="{{ route('professor.grades.import-excel', ['offering_id' => $courseOffering->id]) }}"
                                method="POST" enctype="multipart/form-data" class="flex items-center gap-2" id="importForm">
                                @csrf
                                <input type="file" name="excel_file" id="excelFileInput" accept=".xlsx,.xls" class="hidden"
                                    onchange="handleFileSelect(this)">
                                <button type="button" onclick="document.getElementById('excelFileInput').click()"
                                    id="importBtnSelect"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 hover:border-amber-300 hover:bg-amber-50 text-slate-700 hover:text-amber-700 rounded-xl font-bold text-xs transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    {{ __('បញ្ចូល') }}
                                </button>

                                {{-- File Selected State --}}
                                <div id="fileSelectedState" class="hidden items-center gap-2">
                                    <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-lg">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span id="importFileName" class="text-xs font-medium text-amber-800 max-w-[200px] truncate"></span>
                                        <button type="button" onclick="clearFileSelection()" class="text-amber-500 hover:text-amber-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <button type="submit" id="importBtn"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ __('អាប់ដែត') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Card View --}}
            <div class="block lg:hidden space-y-6 print:hidden">
                <div x-data="{ open: false }" class="bg-white rounded-3xl border border-emerald-100 shadow-sm overflow-hidden">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-5 bg-emerald-50/50">
                        <span class="text-sm font-black text-emerald-700 uppercase tracking-tight">{{ __('គ្រប់គ្រងការវាយតម្លៃ') }}</span>
                        <svg class="w-5 h-5 text-emerald-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="p-4 space-y-3 bg-white">
                        @foreach($assessments as $assessment)
                            @php $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam'); @endphp
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-400 uppercase">{{ $type }}</span>
                                    <span class="text-sm font-bold text-slate-700">{{ $assessment->title_km }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('professor.assessments.edit', ['id' => $assessment->id, 'type' => $type]) }}"
                                       class="p-2 bg-white text-emerald-600 border border-slate-200 rounded-xl shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form action="{{ route('professor.send_all_telegram', $courseOffering->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
                                        <input type="hidden" name="assessment_type" value="{{ $type }}">
                                        <button type="submit" title="ផ្ញើដំណឹងពិន្ទុ" class="p-2 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl shadow-sm print:hidden">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        </button>
                                    </form>
                                    <button type="button"
                                            @click="$dispatch('open-delete-modal', { url: '{{ route('professor.assessments.destroy', $assessment->id) }}', type: '{{ $type }}' })"
                                            class="p-2 bg-rose-50 text-rose-500 border border-rose-100 rounded-xl">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @forelse ($students as $student)
                    @php
                        $attendanceScore = $student->getAttendanceScoreByCourse($courseOffering->id);
                        $baseScore = $attendanceScore;
                        $quizBonus = 0;
                        foreach($assessments as $assessment) {
                            $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                            $score = $gradebook[$student->id][$type . '_' . $assessment->id] ?? 0;
                            if ($type === 'quiz') { $quizBonus += $score; } else { $baseScore += $score; }
                        }
                        $rowTotal = min($baseScore + $quizBonus, 100);
                        $grade = \App\Services\GradingService::getLetterGrade($rowTotal);
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden student-card"
                         data-name="{{ mb_strtolower($student->profile->full_name_km ?? $student->name ?? '', 'UTF-8') }}"
                         data-id="{{ mb_strtolower($student->student_id_code ?? '', 'UTF-8') }}">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="h-12 w-12 rounded-2xl bg-emerald-600 text-white flex flex-col items-center justify-center shadow-lg shadow-emerald-100">
                                            <span class="text-[10px] font-black uppercase leading-none mb-0.5 opacity-70">{{ __('ចំណាត់ថ្នាក់') }}</span>
                                            <span class="text-sm font-black leading-none">{{ $loop->iteration }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-slate-800 leading-tight">{{ $student->profile->full_name_km ?? $student->name }}</h3>
                                        <p class="text-xs text-slate-400 font-bold tracking-wider">{{ $student->student_id_code }}</p>
                                    </div>
                                </div>
                                <div class="h-12 w-12 rounded-2xl flex flex-col items-center justify-center text-sm font-black {{ \App\Services\GradingService::isPassing($grade) ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                                     <span class="text-[10px] uppercase mb-0.5 opacity-70">{{ __('និទ្ទេស') }}</span>
                                     {{ $grade }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-xs font-bold text-slate-500">{{ __('វត្តមាន (15%)') }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black text-slate-700">{{ number_format($attendanceScore, 1) }}</span>
                                    </div>
                                </div>

                                @foreach ($assessments as $assessment)
                                    @php
                                        $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                                        $score = $gradebook[$student->id][$type . '_' . $assessment->id] ?? 0;
                                    @endphp
                                    <a href="{{ route('professor.grades.edit', ['assessment_id' => $assessment->id, 'type' => $type]) }}"
                                       class="flex justify-between items-center p-4 bg-white hover:bg-emerald-50 border border-slate-100 rounded-2xl transition-all active:scale-95 group">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full {{ $type === 'exam' ? 'bg-rose-400' : ($type === 'quiz' ? 'bg-amber-400' : 'bg-emerald-400') }}"></div>
                                            <span class="text-xs font-bold text-slate-600 group-hover:text-emerald-600">{{ $assessment->title_km }}</span>
                                            @if ($type === 'quiz')
                                                <span class="text-[9px] font-bold text-amber-500 uppercase">+ Bonus</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black {{ $score < ($assessment->max_score/2) ? 'text-rose-500' : ($type === 'quiz' ? 'text-amber-700' : 'text-slate-800') }}">
                                                {{ $type === 'quiz' ? '+' : '' }}{{ number_format($score, 1) }}
                                            </span>
                                            <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                    </a>
                                @endforeach

                                <div class="flex justify-between items-center p-4 bg-emerald-600 rounded-2xl mt-4 shadow-lg shadow-emerald-100">
                                    <span class="text-xs font-black text-white uppercase">{{ __('សរុបរួម') }}</span>
                                    <span class="text-sm font-black text-white">{{ number_format($rowTotal, 1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white rounded-2xl border-2 border-dashed border-slate-200">
                        <p class="text-slate-400 font-bold">{{ __('មិនទាន់មាននិស្សិត') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden lg:block bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
                {{-- Print-only University Header --}}
                <div class="hidden print:block" id="printHeader">
                    <div class="text-center mb-2">
                        <p class="text-[11px] font-black text-slate-900 leading-tight">ពិធីការដាក់ពិន្ទុសរុប</p>
                        <p class="text-[10px] font-bold text-slate-700 mt-0.5">តំណាង សាខា មជ្ឈមណ្ឌបណ្តុះបណ្តាល</p>
                    </div>
                    <div class="text-[9px] text-slate-600 leading-relaxed mb-3">
                        <p>នាយកដ្ឋានសិក្សាអប់រំ និងបណ្តុះបណ្តាលវិជ្ជាជីវៈ</p>
                        <p>រដ្ឋបាលខេត្តស្វាយរៀង ឬ រដ្ឋបាលរាជធានីភ្នំពេញ</p>
                    </div>
                    <div class="text-center mb-3">
                        <p class="text-[9px] text-slate-700">
                            ប្រធានបទសិក្សាមុខវិជ្ជា <strong>{{ $courseOffering->course->title_km ?? 'មិនទាន់កំណត់' }}</strong> និងមានវិញ្ញាបនបត្រសម្គាល់លទ្ធផល។
                        </p>
                        <p class="text-[9px] text-slate-700">
                            ឈ្មោះសាខា <strong>សាខាស្វាយរៀង</strong> ឆមាសទី <strong>{{ $courseOffering->semester }}</strong> ឆ្នាំសិក្សា <strong>{{ $courseOffering->academic_year }}</strong> (សម័យប្រឡង)
                        </p>
                        <p class="text-[9px] text-slate-700">
                            ចំនួនសិស្សសរុប <strong>{{ $totalStudents }}</strong> នាក់ និស្សិតប្រុស <strong>{{ $maleCount }}</strong> នាក់ និស្សិតស្រី <strong>{{ $femaleCount }}</strong> នាក់
                        </p>
                    </div>
                </div>

                {{-- Print-only Simple Table --}}
                <div class="hidden print:block" id="printTable">
                    <table class="w-full text-center border-collapse" style="font-size:9px;">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-black px-2 py-1" style="width:30px;">ល.រ</th>
                                <th class="border border-black px-2 py-1" style="width:80px;">អត្តលេខ</th>
                                <th class="border border-black px-2 py-1 text-left" style="width:150px;">ឈ្មោះជាខ្មែរ</th>
                                <th class="border border-black px-2 py-1 text-left" style="width:120px;">ឈ្មោះជាអង់គ្លេស</th>
                                <th class="border border-black px-2 py-1" style="width:50px;">{{ __('ភេទ') }}</th>
                                <th class="border border-black px-2 py-1" style="width:50px;">{{ __('វត្តមាន') }}</th>
                                @foreach($assessments as $assessment)
                                    <th class="border border-black px-2 py-1" style="min-width:60px;">
                                        {{ Str::limit($assessment->title_km, 15) }}
                                    </th>
                                @endforeach
                                <th class="border border-black px-2 py-1" style="width:50px;">{{ __('សរុប') }}</th>
                                <th class="border border-black px-2 py-1" style="width:40px;">{{ __('និទ្ទេស') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $attendanceScore = $student->getAttendanceScoreByCourse($courseOffering->id);
                                    $baseScore = $attendanceScore;
                                    $quizBonus = 0;
                                @endphp
                                <tr>
                                    <td class="border border-black px-1 py-0.5">{{ $loop->iteration }}</td>
                                    <td class="border border-black px-1 py-0.5 text-left" style="font-size:8px;">{{ $student->student_id_code }}</td>
                                    <td class="border border-black px-1 py-0.5 text-left">{{ $student->profile->full_name_km ?? $student->name }}</td>
                                    <td class="border border-black px-1 py-0.5 text-left" style="font-size:8px;">{{ $student->profile?->full_name_en ?? '' }}</td>
                                    <td class="border border-black px-1 py-0.5">{{ $student->profile?->gender == 'male' ? 'ប្រុស' : 'ស្រី' }}</td>
                                    <td class="border border-black px-1 py-0.5">{{ number_format($attendanceScore, 1) }}</td>
                                    @foreach($assessments as $assessment)
                                        @php
                                            $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                                            $score = $gradebook[$student->id][$type . '_' . $assessment->id] ?? 0;
                                            if ($type === 'quiz') { $quizBonus += $score; } else { $baseScore += $score; }
                                        @endphp
                                        <td class="border border-black px-1 py-0.5">{{ number_format($score, 1) }}</td>
                                    @endforeach
                                    @php
                                        $rowTotal = min($baseScore + $quizBonus, 100);
                                        $grade = \App\Services\GradingService::getLetterGrade($rowTotal);
                                    @endphp
                                    <td class="border border-black px-1 py-0.5 font-bold">{{ number_format($rowTotal, 1) }}</td>
                                    <td class="border border-black px-1 py-0.5 font-bold">{{ $grade }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="overflow-x-auto scrollbar-thin">
                    <table class="w-full text-left border-collapse min-w-[1400px]" id="gradeTable">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="w-20 px-4 py-6 text-center text-[11px] font-black text-slate-400 uppercase">{{ __('Rank') }}</th>

                                <th class="sticky left-0 bg-white z-30 px-6 py-6 border-r border-slate-100 shadow-[4px_0_10px_-5px_rgba(0,0,0,0.05)] w-72">
                                    <button onclick="sortTable('name')" class="flex items-center gap-1 text-[11px] font-black text-slate-500 uppercase tracking-widest hover:text-emerald-600 transition-colors">
                                        {{ __('ឈ្មោះនិស្សិត') }}
                                        <svg class="w-3 h-3 sort-icon" data-col="name" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                    </button>
                                </th>

                                <th class="px-4 py-6 text-center w-32 border-r border-slate-50 bg-slate-50/30">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-[11px] font-black text-slate-500 uppercase">{{ __('វត្តមាន') }}</span>
                                        <span class="text-[10px] text-emerald-500 font-bold bg-emerald-50 px-2 py-0.5 rounded-full">15%</span>
                                    </div>
                                </th>

                                @foreach($assessments as $assessment)
                                    @php
                                        $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                                        $colors = [
                                            'assignment' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
                                            'quiz' => 'text-amber-600 bg-amber-50 border-amber-100',
                                            'exam' => 'text-rose-600 bg-rose-50 border-rose-100'
                                        ];
                                    @endphp
                                    <th class="px-4 py-6 text-center border-r border-slate-50 min-w-[175px] group relative bg-white transition-all">
                                        <div class="flex flex-col items-center gap-1.5">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase border {{ $colors[$type] }}">
                                                {{ $type === 'assignment' ? __('កិច្ចការ') : ($type === 'quiz' ? 'Quiz' : __('ប្រឡង')) }}
                                            </span>
                                            @if ($type === 'quiz')
                                                <span class="text-[9px] font-bold text-amber-500 uppercase tracking-wide">+ Bonus</span>
                                            @endif
                                            <a href="{{ route('professor.grades.edit', ['assessment_id' => $assessment->id, 'type' => $type]) }}"
                                               class="text-[13px] font-extrabold text-slate-700 hover:text-emerald-600 hover:scale-105 transform transition-all line-clamp-1">
                                                {{ $assessment->title_km }}
                                            </a>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                                                {{ $assessment->max_score }} {{ __('ពិន្ទុ') }}
                                            </span>

                                            <form action="{{ route('professor.send_all_telegram', $courseOffering->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
                                                <input type="hidden" name="assessment_type" value="{{ $type }}">
                                                <button type="submit" title="{{ __('ផ្ញើដំណឹងពិន្ទុ') }}"
                                                        class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm flex items-center gap-1 text-[10px] font-bold print:hidden">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                                    {{ __('ផ្ញើដំណឹង') }}
                                                </button>
                                            </form>
                                        </div>

                                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-all duration-200 flex gap-1">
                                            <a href="{{ route('professor.assessments.edit', ['id' => $assessment->id, 'type' => $type]) }}"
                                               class="p-1.5 bg-white text-slate-400 hover:text-emerald-600 border border-slate-100 rounded-lg shadow-sm" title="{{ __('កែសម្រួល') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </a>
                                            <button type="button"
                                                    @click="$dispatch('open-delete-modal', { url: '{{ route('professor.assessments.destroy', $assessment->id) }}', type: '{{ $type }}' })"
                                                    class="p-1.5 bg-rose-50 text-rose-500 rounded-lg hover:bg-rose-500 hover:text-white transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </th>
                                @endforeach

                                <th class="sticky right-24 bg-slate-50 z-20 px-6 py-6 text-center border-l border-slate-200 w-32 shadow-[-4px_0_10px_-5px_rgba(0,0,0,0.05)]">
                                    <button onclick="sortTable('total')" class="flex items-center justify-center gap-1 text-[11px] font-black text-emerald-700 uppercase tracking-widest hover:text-emerald-900 transition-colors mx-auto">
                                        {{ __('សរុប') }}
                                        <svg class="w-3 h-3 sort-icon" data-col="total" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                    </button>
                                </th>
                                <th class="sticky right-0 bg-slate-100 z-20 px-4 py-6 text-center border-l border-slate-200 w-24">
                                    <button onclick="sortTable('grade')" class="flex items-center justify-center gap-1 text-[11px] font-black text-slate-500 uppercase tracking-widest hover:text-emerald-600 transition-colors mx-auto">
                                        {{ __('និទ្ទេស') }}
                                        <svg class="w-3 h-3 sort-icon" data-col="grade" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                    </button>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-50" id="gradeBody">
                            @forelse ($students as $student)
                                @php
                                    $attendanceScore = $student->getAttendanceScoreByCourse($courseOffering->id);
                                    $baseScore = $attendanceScore;
                                    $quizBonus = 0;
                                    $studentScores = [];
                                    foreach ($assessments as $assessment) {
                                        $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                                        $score = $gradebook[$student->id][$type . '_' . $assessment->id] ?? 0;
                                        $studentScores[$type . '_' . $assessment->id] = ['type' => $type, 'score' => $score];
                                        if ($type === 'quiz') { $quizBonus += $score; } else { $baseScore += $score; }
                                    }
                                    $rowTotal = min($baseScore + $quizBonus, 100);
                                    $grade = \App\Services\GradingService::getLetterGrade($rowTotal);
                                    $isPassing = \App\Services\GradingService::isPassing($grade);
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150 group student-row"
                                    data-name="{{ mb_strtolower($student->profile->full_name_km ?? $student->name ?? '', 'UTF-8') }}"
                                    data-id="{{ mb_strtolower($student->student_id_code ?? '', 'UTF-8') }}"
                                    data-total="{{ $rowTotal }}"
                                    data-grade="{{ $grade }}">
                                    <td class="px-4 py-5 text-center">
                                        <span class="text-xs font-bold text-slate-400 rank-number">{{ $loop->iteration }}</span>
                                    </td>

                                    <td class="sticky left-0 bg-white group-hover:bg-slate-50 z-10 px-6 py-5 border-r border-slate-50 shadow-[4px_0_10px_-5px_rgba(0,0,0,0.05)]">
                                        <div class="flex items-center">
                                            <div class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 font-black text-xs group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                                                {{ mb_substr($student->profile->full_name_km ?? $student->name, 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-[13px] font-bold text-slate-800 leading-none group-hover:text-emerald-700 transition-colors">
                                                    {{ $student->profile->full_name_km ?? $student->name }}
                                                </div>
                                                <div class="text-[10px] text-slate-400 font-bold tracking-wider mt-1.5">{{ $student->student_id_code }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-5 text-center font-black text-slate-500 text-xs border-r border-slate-50 bg-slate-50/10">
                                        {{ number_format($attendanceScore, 1) }}
                                    </td>

                                    @foreach ($assessments as $assessment)
                                        @php
                                            $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                                            $sData = $studentScores[$type . '_' . $assessment->id];
                                            $score = $sData['score'];
                                        @endphp
                                        <td class="px-4 py-5 text-center border-r border-slate-50 {{ $type === 'quiz' ? 'bg-amber-50/20' : '' }}">
                                            <span class="text-xs font-black {{ $score < ($assessment->max_score/2) ? 'text-rose-500' : ($type === 'quiz' ? 'text-amber-700' : 'text-slate-700') }}">
                                                {{ $type === 'quiz' ? '+' : '' }}{{ number_format($score, 1) }}
                                            </span>
                                        </td>
                                    @endforeach

                                    <td class="sticky right-24 bg-emerald-50/30 group-hover:bg-emerald-50/60 z-10 px-6 py-5 text-center border-l border-emerald-100/50">
                                        <span class="text-sm font-black text-emerald-700 total-score">
                                            {{ number_format($rowTotal, 1) }}
                                        </span>
                                    </td>

                                    <td class="sticky right-0 bg-white group-hover:bg-slate-50 z-10 px-4 py-5 text-center border-l border-slate-100">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-xs font-black shadow-sm transition-transform group-hover:scale-110 {{ $isPassing ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                                            {{ $grade }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="py-32 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-slate-50 p-6 rounded-full mb-4">
                                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <p class="font-bold text-slate-400 text-lg">{{ __('មិនទាន់មាននិស្សិតក្នុងថ្នាក់នេះនៅឡើយ') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Print-only Footer --}}
                    <div class="hidden print:block border-t border-slate-400 pt-3 mt-4" id="printFooter">
                        <div class="text-[8px] text-slate-600 leading-relaxed mb-4">
                            <p class="mb-1"><strong>កំណត់សម្គាល់៖</strong></p>
                            <p class="flex gap-4">
                                <span>អត្រា = ពិន្ទុ / ពិន្ទុអតិបរិមា × ១០០</span>
                                <span>ឧទាហរណ៍ = ៧៥ / ១០០ × ១០០ = ៧៥%</span>
                            </p>
                            <p>ប្រសិនបើសិស្សមានអត្រាផ្ទៀងផ្ទាត់សម្គាល់តិចជាង ៨០% នឹងត្រូវបានគេចាត់ទុកជា « <strong>«មិនបានប្រឡង»</strong> ឬ <strong>«អវត្តមាន»</strong> ។</p>
                        </div>
                        <div class="grid grid-cols-3 gap-6 text-center text-[9px] text-slate-700 mt-8 pt-6 border-t border-slate-300">
                            <div>
                                <p class="font-bold">ធ្វើនៅ ថ្ងៃទី _____ ខែ _______ ឆ្នាំ _______</p>
                                <div class="mt-6 border-t border-slate-400 w-40 mx-auto"></div>
                                <p class="mt-2 font-bold">អ្នករៀបចំបញ្ជី</p>
                            </div>
                            <div>
                                <div class="w-20 h-20 border border-slate-300 rounded-full mx-auto mb-2"></div>
                                <p class="font-bold">ប្រធានការិយាល័យសិក្សា<br>មានការព្រមព្រៀង</p>
                                <div class="mt-2 border-t border-slate-400 w-40 mx-auto"></div>
                                <p class="mt-2 font-bold">ឈ្មោះ និងហត្ថលេខា</p>
                            </div>
                            <div>
                                <div class="w-20 h-20 border border-slate-300 rounded-full mx-auto mb-2"></div>
                                <p class="font-bold">នាយកសាខា<br>មានការព្រមព្រៀង</p>
                                <div class="mt-2 border-t border-slate-400 w-40 mx-auto"></div>
                                <p class="mt-2 font-bold">ឈ្មោះ និងហត្ថលេខា</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-data="{ open: false, postUrl: '', assessmentType: '' }"
        x-show="open"
        @open-delete-modal.window="open = true; postUrl = $event.detail.url; assessmentType = $event.detail.type"
        class="fixed inset-0 z-[100] overflow-y-auto"
        style="display: none;">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.away="open = false"
                class="relative transform overflow-hidden rounded-2xl bg-white px-4 pb-4 pt-5 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-9 border border-slate-100">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-rose-50 sm:mx-0">
                        <svg class="h-8 w-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-xl font-black leading-6 text-slate-800">{{ __('លុបការវាយតម្លៃ') }}</h3>
                        <div class="mt-3">
                            <p class="text-sm font-medium text-slate-500 leading-relaxed">
                                {{ __('តើអ្នកប្រាកដថាចង់លុបការវាយតម្លៃនេះមែនទេ? រាល់ទិន្នន័យពិន្ទុរបស់និស្សិតទាំងអស់ក្នុងផ្នែកនេះនឹងត្រូវបាត់បង់ជារៀងរហូត។') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button @click="open = false" type="button"
                            class="inline-flex justify-center rounded-2xl bg-white px-6 py-3 text-sm font-bold text-slate-700 border border-slate-200 hover:bg-slate-50 transition-all">
                        {{ __('បោះបង់') }}
                    </button>
                    <form :action="postUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="assessment_type" :value="assessmentType">
                        <button type="submit"
                                class="inline-flex w-full justify-center rounded-2xl bg-rose-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-rose-200 hover:bg-rose-600 transition-all">
                            {{ __('យល់ព្រមលុប') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        ::-webkit-scrollbar { height: 8px; width: 4px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 20px; border: 2px solid #f8fafc; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        .scrollbar-thin { scrollbar-gutter: stable; }
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down { animation: fade-in-down 0.4s ease-out forwards; }
        .sticky { position: sticky; }

        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body { background: white !important; font-size: 11px !important; margin: 0 !important; padding: 10mm !important; }

            .print\:hidden, nav, header, footer, .no-print, #chat-overlay, #confirm-modal,
            .fixed, .overflow-x-auto { display: none !important; }

            .hidden.lg\:block { display: block !important; }
            .block.lg\:hidden { display: none !important; }

            .sticky { position: static !important; }
            .shadow-sm, .shadow-md, .shadow-lg, .shadow-xl, .shadow-2xl,
            .shadow-\[4px_0_10px_-5px_rgba\(0\,0\,0\,0\.05\)\],
            .shadow-\[-4px_0_10px_-5px_rgba\(0\,0\,0\,0\.05\)\],
            .shadow-lg.shadow-emerald-100 { box-shadow: none !important; }

            table { min-width: 100% !important; border-collapse: collapse !important; }
            th, td { padding: 4px 6px !important; border: 1px solid black !important; font-size: 9px !important; }
            thead tr { background-color: #e5e5e5 !important; }

            .rounded-2xl, .rounded-3xl { border-radius: 0 !important; }

            #printHeader { display: block !important; margin-bottom: 4px; }
            #printTable { display: block !important; margin-bottom: 8px; }
            #printFooter { display: block !important; margin-top: 8px; }

            @page { margin: 8mm; size: landscape; }
        }

        #printHeader { display: none; }
        #printTable { display: none; }
        #printFooter { display: none; }
    </style>

    <script>
        let currentSort = { col: null, asc: true };

        function handleFileSelect(input) {
            if (input.files.length > 0) {
                const file = input.files[0];
                document.getElementById('importFileName').textContent = file.name;
                document.getElementById('fileSelectedState').classList.remove('hidden');
                document.getElementById('fileSelectedState').classList.add('flex');
                document.getElementById('importBtnSelect').classList.add('hidden');
            }
        }

        function clearFileSelection() {
            document.getElementById('excelFileInput').value = '';
            document.getElementById('fileSelectedState').classList.add('hidden');
            document.getElementById('fileSelectedState').classList.remove('flex');
            document.getElementById('importBtnSelect').classList.remove('hidden');
        }

        function filterStudents() {
            const query = document.getElementById('studentSearch').value.toLowerCase();
            document.querySelectorAll('.student-row, .student-card').forEach(row => {
                const name = row.dataset.name || '';
                const id = row.dataset.id || '';
                row.style.display = (name.includes(query) || id.includes(query)) ? '' : 'none';
            });
        }

        function sortTable(col) {
            const tbody = document.getElementById('gradeBody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('.student-row'));

            if (currentSort.col === col) {
                currentSort.asc = !currentSort.asc;
            } else {
                currentSort.col = col;
                currentSort.asc = true;
            }

            rows.sort((a, b) => {
                let va, vb;
                if (col === 'name') {
                    va = a.dataset.name || '';
                    vb = b.dataset.name || '';
                    return currentSort.asc ? va.localeCompare(vb) : vb.localeCompare(va);
                } else if (col === 'total') {
                    va = parseFloat(a.dataset.total) || 0;
                    vb = parseFloat(b.dataset.total) || 0;
                    return currentSort.asc ? va - vb : vb - va;
                } else if (col === 'grade') {
                    va = a.dataset.total || 0;
                    vb = b.dataset.total || 0;
                    return currentSort.asc ? va - vb : vb - va;
                }
                return 0;
            });

            rows.forEach((row, i) => {
                tbody.appendChild(row);
                const rankEl = row.querySelector('.rank-number');
                if (rankEl) rankEl.textContent = i + 1;
            });

            document.querySelectorAll('.sort-icon').forEach(icon => {
                icon.style.opacity = icon.dataset.col === col ? '1' : '0.3';
            });
        }
    </script>
</x-app-layout>
