<x-app-layout>
    <div class="bg-gray-50 min-h-screen font-sans text-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-chart-line text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</h1>
                        <p class="text-gray-500 mt-0.5">
                            {{ $courseOffering->semester }} / {{ $courseOffering->academic_year }}
                            @if($courseOffering->lecturer)
                                <span class="mx-1">·</span> {{ __('គ្រូ៖') }} {{ $courseOffering->lecturer->name }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.grades.export', $courseOffering->id) }}" class="flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition-all text-sm">
                        <i class="fas fa-download"></i> {{ __('ទាញយក Excel') }}
                    </a>
                    <a href="{{ route('admin.grades.re-exam-form', $courseOffering->id) }}" class="flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition-all text-sm">
                        <i class="fas fa-redo"></i> {{ __('ប្រឡងសង') }}
                    </a>
                    <a href="{{ route('admin.grades.index') }}" class="flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-xl font-bold shadow-sm border border-gray-200 transition-all text-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('ត្រឡប់ក្រោយ') }}
                    </a>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-users text-gray-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">{{ __('សិស្សសរុប') }}</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-pen text-emerald-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">{{ __('មានពិន្ទុ') }}</span>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $stats['graded'] }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-chart-bar text-emerald-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">{{ __('មធ្យមភាគ') }}</span>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['avg_grade'], 1) }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-arrow-up text-emerald-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">{{ __('ខ្ពស់បំផុត') }}</span>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['max_grade'], 1) }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                            <i class="fas fa-arrow-down text-rose-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">{{ __('ទាបបំផុត') }}</span>
                    </div>
                    <div class="text-2xl font-bold text-rose-600">{{ number_format($stats['min_grade'], 1) }}</div>
                </div>
            </div>

            {{-- Grade Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">{{ __('បញ្ជីពិន្ទុសិស្ស') }}</h3>
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-bold text-gray-500">{{ $students->count() }} {{ __('នាក់') }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">{{ __('ឈ្មោះ') }}</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-emerald-600 uppercase tracking-wider">{{ __('វត្តមាន') }}</th>
                                @foreach($assessments as $assessment)
                                    <th class="px-3 py-3 text-center text-[11px] font-bold uppercase tracking-wider
                                        {{ $assessment instanceof \App\Models\Assignment ? 'text-emerald-600' : ($assessment instanceof \App\Models\Quiz ? 'text-amber-600' : 'text-purple-600') }}">
                                        {{ Str::limit($assessment->title_km, 15) }}
                                    </th>
                                @endforeach
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider">{{ __('សរុប') }}</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider">{{ __('និទ្ទេស') }}</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider">{{ __('ស្ថានភាព') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($students as $index => $student)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $profilePic = $student->userProfile?->profile_picture_url ?? $student->studentProfile?->profile_picture_url;
                                        @endphp
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                            @if($profilePic)
                                                <img src="{{ $profilePic }}?tr=w-64,h-64,fo-face" class="w-full h-full object-cover" alt="">
                                            @else
                                                <span class="text-white font-bold text-xs">{{ mb_substr($student->studentProfile?->full_name_km ?? $student->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-semibold text-sm text-gray-900">{{ $student->studentProfile?->full_name_km ?? $student->name }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $student->student_id_code ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $attScore = $student->getAttendanceScoreByCourse($courseOffering->id) ?? 0;
                                        $attPass = $attScore >= 10;
                                    @endphp
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg {{ $attPass ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} font-bold text-xs">
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
                                    <td class="px-3 py-3 text-center">
                                        <span class="text-xs font-bold {{ $isFailing ? 'text-rose-600' : 'text-gray-700' }}">
                                            {{ $score > 0 ? number_format($score, 1) : '-' }}
                                        </span>
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-bold {{ $student->isPassing ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ number_format($student->temp_total, 1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $gradeColors = [
                                            'A' => 'bg-emerald-100 text-emerald-700',
                                            'B+' => 'bg-emerald-50 text-emerald-600',
                                            'B' => 'bg-emerald-100 text-emerald-700',
                                            'C+' => 'bg-emerald-50 text-emerald-600',
                                            'C' => 'bg-amber-100 text-amber-700',
                                            'D+' => 'bg-amber-50 text-amber-600',
                                            'D' => 'bg-orange-100 text-orange-700',
                                            'F' => 'bg-rose-100 text-rose-700',
                                        ];
                                        $colorClass = $gradeColors[$student->letterGrade] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg {{ $colorClass }}">{{ $student->letterGrade }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($student->isPassing)
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-emerald-100 text-emerald-700">{{ __('ជាប់') }}</span>
                                    @elseif($student->needs_retake_semester ?? false)
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-rose-100 text-rose-700">
                                            <i class="fas fa-exclamation-circle mr-0.5"></i> {{ __('ត្រូវសិក្សាឡើងវិញ') }}
                                        </span>
                                    @elseif(!empty($student->needs_re_exam))
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-amber-100 text-amber-700">
                                            <i class="fas fa-redo mr-0.5"></i> {{ __('ត្រូវប្រឡងសង') }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-rose-100 text-rose-700">{{ __('មិនជាប់') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 6 + $assessments->count() }}" class="px-6 py-16">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-bold text-gray-400">{{ __('មិនមានទិន្នន័យពិន្ទុ') }}</p>
                                        <p class="text-xs text-gray-300">{{ __('សូមបញ្ចូលពិន្ទុសិស្សនៅក្នុងផ្នែកគ្រប់គ្រង') }}</p>
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
                                'assignment' => __('កិច្ចការ'),
                                'midterm' => __('ប្រឡងពាក់កណ្ដាល់'),
                                'final' => __('ប្រឡងប្រចាំឆមាស'),
                                default => ucfirst($type),
                            };
                            $reExamEntries[] = [
                                'student' => $student,
                                'type' => $type,
                                'type_label' => $typeLabel,
                                'original_score' => $cs['original_score'] ?? 0,
                                're_exam_score' => $cs['re_exam_score'] ?? $cs['score'] ?? 0,
                                'passing' => $cs['passing'] ?? false,
                                'threshold' => \App\Services\GradingService::getPassThreshold($type),
                            ];
                        }
                    }
                }
            @endphp
            @if(count($reExamEntries) > 0)
            <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                            <i class="fas fa-redo text-amber-500 text-sm"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ __('លទ្ធផលប្រឡងសង') }}</h3>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-amber-100 text-xs font-bold text-amber-600">{{ count($reExamEntries) }} {{ __('ករណី') }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">{{ __('ឈ្មោះ') }}</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-amber-600 uppercase tracking-wider">{{ __('ប្រភេទ') }}</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-rose-500 uppercase tracking-wider">{{ __('ពិន្ទុដើម') }}</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-blue-600 uppercase tracking-wider">{{ __('ពិន្ទុប្រឡងសង') }}</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">{{ __('ត្រូវការ') }}</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider">{{ __('លទ្ធផល') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($reExamEntries as $idx => $entry)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-400">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-bold text-xs">{{ mb_substr($entry['student']->studentProfile?->full_name_km ?? $entry['student']->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-sm text-gray-900">{{ $entry['student']->studentProfile?->full_name_km ?? $entry['student']->name }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $entry['student']->student_id_code ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-600">{{ $entry['type_label'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-bold text-rose-500">{{ number_format($entry['original_score'], 1) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-bold text-blue-600">{{ number_format($entry['re_exam_score'], 1) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-bold text-gray-500">≥ {{ $entry['threshold'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($entry['passing'])
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-emerald-100 text-emerald-700">
                                            <i class="fas fa-check mr-0.5"></i> {{ __('ជាប់') }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-rose-100 text-rose-700">
                                            <i class="fas fa-times mr-0.5"></i> {{ __('មិនជាប់') }}
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
