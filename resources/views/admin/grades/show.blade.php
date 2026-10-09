<x-app-layout>
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">

        {{-- Hero Header --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-28 pt-10 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chart-line text-emerald-300 text-xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">
                                    {{ $courseOffering->course->title_km ?? $courseOffering->course->title_en ?? 'N/A' }}
                                </h2>
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    {{ $courseOffering->semester }}
                                </span>
                            </div>
                            <p class="text-slate-400 mt-1 text-sm flex items-center gap-2 flex-wrap">
                                <span>{{ $courseOffering->academic_year }}</span>
                                @if($courseOffering->department)
                                    <span>·</span>
                                    <span>{{ $courseOffering->department->name_km }} @if($courseOffering->generation)(G{{ $courseOffering->generation }})@endif</span>
                                @endif
                                @if($courseOffering->lecturer)
                                    <span>·</span>
                                    <span class="text-slate-300"><i class="fas fa-user-tie text-xs mr-1 text-slate-400"></i> {{ $courseOffering->lecturer->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('admin.grades.export', $courseOffering->id) }}"
                           class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 rounded-xl font-bold transition-all text-sm shadow-md active:scale-95">
                            <i class="fas fa-file-excel"></i>
                            <span>{{ __('download_excel') }}</span>
                        </a>
                        <a href="{{ route('admin.grades.re-exam-form', $courseOffering->id) }}"
                           class="flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-white px-4 py-2.5 rounded-xl font-bold transition-all text-sm shadow-md active:scale-95">
                            <i class="fas fa-redo"></i>
                            <span>{{ __('retake') }}</span>
                        </a>
                        <a href="{{ route('admin.grades.index') }}"
                           class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl font-bold transition-all text-sm backdrop-blur-sm active:scale-95">
                            <i class="fas fa-arrow-left"></i>
                            <span>{{ __('go_back') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 pb-12 relative z-10">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 text-slate-600">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 truncate">{{ __('total_students') }}</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0 text-emerald-600">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 truncate">{{ __('graded') }}</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-600">{{ number_format($stats['graded']) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-600">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 truncate">{{ __('average') }}</p>
                        <p class="mt-1 text-2xl font-bold text-blue-600">{{ number_format($stats['avg_grade'], 1) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center flex-shrink-0 text-teal-600">
                        <i class="fas fa-arrow-up text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 truncate">{{ __('highest') }}</p>
                        <p class="mt-1 text-2xl font-bold text-teal-600">{{ number_format($stats['max_grade'], 1) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 col-span-2 lg:col-span-1 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0 text-rose-600">
                        <i class="fas fa-arrow-down text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 truncate">{{ __('lowest') }}</p>
                        <p class="mt-1 text-2xl font-bold text-rose-600">{{ number_format($stats['min_grade'], 1) }}</p>
                    </div>
                </div>
            </div>

            {{-- Grade Table Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-sm"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ __('student_grade_list') }}</h3>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        {{ $students->count() }} {{ __('students_2') }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">{{ __('name') }}</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-emerald-700 uppercase tracking-wider bg-emerald-50/40">{{ __('attendance') }}</th>
                                @foreach($assessments as $assessment)
                                    <th class="px-3 py-3.5 text-center text-xs font-bold uppercase tracking-wider
                                        {{ $assessment instanceof \App\Models\Assignment ? 'text-teal-700 bg-teal-50/30' : ($assessment instanceof \App\Models\Quiz ? 'text-amber-700 bg-amber-50/30' : 'text-purple-700 bg-purple-50/30') }}">
                                        {{ Str::limit($assessment->title_km ?? $assessment->title_en, 15) }}
                                    </th>
                                @endforeach
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider bg-slate-50/50">{{ __('total_2') }}</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('grade') }}</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm">
                            @forelse($students as $index => $student)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-3.5 text-center font-bold text-gray-400 text-xs">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $profilePic = $student->userProfile?->profile_picture_url ?? $student->studentProfile?->profile_picture_url;
                                            $displayName = $student->studentProfile?->full_name_km ?? $student->name;
                                        @endphp
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center flex-shrink-0 overflow-hidden shadow-sm">
                                            @if($profilePic)
                                                <img src="{{ $profilePic }}?tr=w-80,h-80,fo-face" class="w-full h-full object-cover" alt="{{ $displayName }}">
                                            @else
                                                <span class="text-white font-bold text-sm">{{ mb_strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'), 'UTF-8') }}</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-gray-900 truncate">{{ $displayName }}</div>
                                            @if($student->student_id_code)
                                                <div class="font-mono text-xs text-gray-400 mt-0.5">{{ $student->student_id_code }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center bg-emerald-50/20">
                                    @php
                                        $attScore = $student->getAttendanceScoreByCourse($courseOffering->id) ?? 0;
                                        $attPass = $attScore >= 10;
                                    @endphp
                                    <span class="inline-flex items-center justify-center min-w-[32px] h-7 px-2.5 rounded-lg {{ $attPass ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }} font-bold text-xs">
                                        {{ $attScore }}
                                    </span>
                                </td>
                                @foreach($assessments as $assessment)
                                    @php
                                        $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' :
                                               (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                                        $key = $type . '_' . $assessment->id;
                                        $score = $gradebook[$student->id][$key] ?? 0;
                                        $maxScore = $assessment->max_score ?? 100;
                                        $assessmentType = match(true) {
                                            $assessment instanceof \App\Models\Assignment => 'assignment',
                                            $assessment instanceof \App\Models\Exam => \App\Services\GradingService::classifyExamType($assessment),
                                            default => 'quiz',
                                        };
                                        $isCritical = in_array($assessmentType, ['assignment', 'midterm', 'final']);
                                        $threshold = $isCritical ? \App\Services\GradingService::getPassThreshold($assessmentType) : 0;
                                        $isFailing = $isCritical && $score < $threshold;
                                    @endphp
                                    <td class="px-3 py-3.5 text-center">
                                        <span class="text-xs font-bold {{ $isFailing ? 'text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md' : 'text-gray-700' }}">
                                            {{ $score > 0 ? number_format($score, 1) : '-' }}
                                        </span>
                                    </td>
                                @endforeach
                                <td class="px-4 py-3.5 text-center bg-slate-50/30">
                                    <span class="text-sm font-black {{ $student->isPassing ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ number_format($student->temp_total, 1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @php
                                        $gradeColors = [
                                            'A'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'B+' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'B'  => 'bg-teal-50 text-teal-700 border-teal-200',
                                            'C+' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                            'C'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'D+' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'D'  => 'bg-orange-50 text-orange-700 border-orange-200',
                                            'F'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                        $colorClass = $gradeColors[$student->letterGrade] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                                    @endphp
                                    <span class="inline-flex items-center justify-center min-w-[28px] px-2.5 py-1 text-xs font-bold rounded-lg border {{ $colorClass }}">
                                        {{ $student->letterGrade }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($student->isPassing)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-check text-[10px]"></i> {{ __('pass') }}
                                        </span>
                                    @elseif($student->needs_retake_semester ?? false)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fas fa-exclamation-circle text-[10px]"></i> {{ __('retake_semester') }}
                                        </span>
                                    @elseif(!empty($student->needs_re_exam))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fas fa-redo text-[10px]"></i> {{ __('retake_needed') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fas fa-times text-[10px]"></i> {{ __('fail') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 6 + $assessments->count() }}" class="px-6 py-16">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-300">
                                            <i class="fas fa-inbox text-2xl"></i>
                                        </div>
                                        <h4 class="text-base font-bold text-gray-600">{{ __('no_grade_data') }}</h4>
                                        <p class="text-xs text-gray-400">{{ __('please_enter_student_grades_in_the_management_section') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Re-Exam Results Section --}}
            @php
                $reExamEntries = [];
                foreach ($students as $student) {
                    foreach (['assignment', 'midterm', 'final'] as $type) {
                        $cs = $student->component_status[$type] ?? null;
                        if ($cs && ($cs['has_re_exam'] ?? false)) {
                            $typeLabel = match($type) {
                                'assignment' => __('assignment'),
                                'midterm' => __('midterm_exam'),
                                'final' => __('final_exam'),
                                default => ucfirst($type),
                            };
                            $reExamEntries[] = [
                                'student' => $student,
                                'type' => $type,
                                'type_label' => $typeLabel,
                                'original_score' => $cs['original_score'] ?? 0,
                                're_exam_score' => $cs['re_exam_score'] ?? $cs['score'] ?? 0,
                                'final_score' => $cs['score'] ?? 0,
                                'passing' => $cs['passing'] ?? false,
                                'threshold' => \App\Services\GradingService::getPassThreshold($type),
                            ];
                        }
                    }
                }
            @endphp
            @if(count($reExamEntries) > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i class="fas fa-redo text-sm"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ __('re_exam_entries') }}</h3>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                        {{ count($reExamEntries) }} {{ __('entries') }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">{{ __('name') }}</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-amber-700 uppercase tracking-wider bg-amber-50/40">{{ __('type') }}</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-rose-600 uppercase tracking-wider">{{ __('original_score') }}</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-blue-600 uppercase tracking-wider">{{ __('re_exam_score') }}</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-emerald-700 uppercase tracking-wider bg-emerald-50/40">{{ __('final_score_used') }}</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('required') }}</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('results') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm">
                            @foreach($reExamEntries as $idx => $entry)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-3.5 text-center font-bold text-gray-400 text-xs">{{ $idx + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $stName = $entry['student']->studentProfile?->full_name_km ?? $entry['student']->name;
                                        @endphp
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-sm text-white font-bold text-sm">
                                            {{ mb_strtoupper(mb_substr($stName, 0, 1, 'UTF-8'), 'UTF-8') }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-gray-900 truncate">{{ $stName }}</div>
                                            @if($entry['student']->student_id_code)
                                                <div class="font-mono text-xs text-gray-400 mt-0.5">{{ $entry['student']->student_id_code }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center bg-amber-50/20">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ $entry['type_label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-100">
                                        {{ number_format($entry['original_score'], 1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
                                        {{ number_format($entry['re_exam_score'], 1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center bg-emerald-50/20">
                                    <span class="text-xs font-black text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                        {{ number_format($entry['final_score'], 1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="text-xs font-bold text-gray-500 font-mono">≥ {{ $entry['threshold'] }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($entry['passing'])
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-check text-[10px]"></i> {{ __('pass') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fas fa-times text-[10px]"></i> {{ __('fail') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
