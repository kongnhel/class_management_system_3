<x-app-layout>
    <div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-6">

            {{-- Header --}}
            <div class="flex items-center gap-4">
                <a wire:navigate href="{{ route('student.my-grades') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-black text-gray-900">{{ __('ពិន្ទុការវាយតម្លៃ') }}</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ __('ពិន្ទុរបស់អ្នកសម្រាប់មុខវិជ្ជាទាំងអស់') }}</p>
                </div>
            </div>

            {{-- Course Assessments --}}
            @forelse($assessmentsByCourse as $courseData)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    {{-- Course Header --}}
                    <div class="px-6 py-4 border-b border-slate-50 bg-gradient-to-r from-emerald-50/50 to-transparent">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $courseData['course_name'] }}</h3>
                                    <p class="text-xs text-gray-400">{{ $courseData['offering']->academic_year }} • {{ __('ឆមាស') }} {{ $courseData['offering']->semester }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-2xl font-black text-emerald-600">{{ number_format($courseData['total_score'], 1) }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">{{ __('សរុប') }}</p>
                                </div>
                                <div class="w-12 h-12 rounded-xl flex flex-col items-center justify-center text-sm font-black {{ !$courseData['is_failed'] ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                                    <span class="text-[8px] uppercase opacity-60 leading-none mb-0.5">{{ __('និទ្ទេស') }}</span>
                                    {{ $courseData['letter_grade'] }}
                                </div>
                            </div>
                        </div>
                        {{-- Summary chips --}}
                        <div class="flex flex-wrap gap-2 mt-3">
                            @php
                                $attPassing = \App\Services\GradingService::isComponentPassing('attendance', $courseData['attendance_score']);
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg {{ $attPassing ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700' }} text-[10px] font-bold">
                                <i class="fas fa-user-check"></i> {{ __('វត្តមាន') }}: {{ number_format($courseData['attendance_score'], 1) }}/15
                                @if(!$attPassing)
                                    <span class="ml-1 text-rose-500">✗</span>
                                @else
                                    <span class="ml-1 text-emerald-500">✓</span>
                                @endif
                            </span>
                            @if($courseData['quiz_bonus'] > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-[10px] font-bold">
                                    <i class="fas fa-star"></i> Quiz Bonus: +{{ number_format($courseData['quiz_bonus'], 1) }}
                                </span>
                            @endif
                            @if(!empty($courseData['needs_re_exam']))
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-[10px] font-bold">
                                    <i class="fas fa-redo"></i> {{ __('ត្រូវប្រឡងសង') }}: {{ implode(', ', array_map(fn($t) => match($t) { 'assignment' => __('កិច្ចការ'), 'midterm' => __('ប្រឡងពាក់កណ្ដាល់'), 'final' => __('ប្រឡងប្រចាំឆមាស'), default => $t }, $courseData['needs_re_exam'])) }}
                                </span>
                            @endif
                            @if($courseData['needs_retake_semester'] ?? false)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 text-[10px] font-bold">
                                    <i class="fas fa-exclamation-circle"></i> {{ __('ត្រូវសិក្សាឡើងវិញ') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Assessment Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ __('ប្រភេទ') }}</th>
                                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ __('ឈ្មោះ') }}</th>
                                    <th class="text-center px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ __('ពិន្ទុ') }}</th>
                                    <th class="text-center px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ __('អតិបរមា') }}</th>
                                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ __('កំណត់ចំណាំ') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                {{-- Attendance row --}}
                                @php
                                    $attPass = \App\Services\GradingService::isComponentPassing('attendance', $courseData['attendance_score']);
                                @endphp
                                <tr class="{{ $attPass ? 'hover:bg-blue-50/30' : 'hover:bg-rose-50/30' }}">
                                    <td class="px-6 py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold {{ $attPass ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-700' }}">{{ __('វត្តមាន') }}</span>
                                    </td>
                                    <td class="px-6 py-3 font-semibold text-gray-700">{{ __('ពិន្ទុវត្តមាន (15%)') }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="font-black {{ $attPass ? 'text-blue-600' : 'text-rose-600' }}">{{ number_format($courseData['attendance_score'], 1) }}</span>
                                        <span class="text-[10px] font-bold {{ $attPass ? 'text-emerald-500' : 'text-rose-500' }}">{{ $attPass ? '✓' : '✗' }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-center text-gray-400 font-bold">15</td>
                                    <td class="px-6 py-3 text-xs {{ $attPass ? 'text-gray-400' : 'text-rose-500 font-bold' }}">
                                        {{ $attPass ? '—' : __('ត្រូវសិក្សាឡើងវិញ') }}
                                    </td>
                                </tr>

                                @foreach($courseData['assessments'] as $assessment)
                                    @php
                                        $typeColors = [
                                            'assignment' => 'bg-emerald-50 text-emerald-700',
                                            'midterm' => 'bg-blue-50 text-blue-700',
                                            'final' => 'bg-rose-50 text-rose-700',
                                            'quiz' => 'bg-amber-50 text-amber-700',
                                        ];
                                        $typeClass = $typeColors[$assessment['type']] ?? 'bg-gray-50 text-gray-700';
                                        $scoreVal = (float) ($assessment['score'] ?? 0);
                                        $isCritical = in_array($assessment['type'], ['assignment', 'midterm', 'final']);
                                        $threshold = $isCritical ? \App\Services\GradingService::getPassThreshold($assessment['type']) : 0;
                                        $isPassing = !$isCritical || $scoreVal >= $threshold;
                                        $hasReExam = $assessment['has_re_exam'] ?? false;
                                    @endphp
                                    <tr class="{{ $isPassing ? 'hover:bg-slate-50/50' : 'hover:bg-rose-50/30' }}">
                                        <td class="px-6 py-3">
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold {{ $typeClass }}">{{ $assessment['type_label'] }}</span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="font-semibold text-gray-700">{{ $assessment['title'] }}</span>
                                            @if($hasReExam)
                                                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-600">
                                                    <i class="fas fa-redo mr-0.5"></i> {{ __('ប្រឡងសង') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            @if($hasReExam && ($assessment['original_score'] ?? 0) != $scoreVal)
                                                <span class="text-gray-300 line-through text-xs mr-1">{{ number_format($assessment['original_score'] ?? 0, 1) }}</span>
                                            @endif
                                            <span class="font-black {{ $isPassing ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ number_format($scoreVal, 1) }}
                                            </span>
                                            <span class="text-[10px] font-bold {{ $isPassing ? 'text-emerald-500' : 'text-rose-500' }}">{{ $isPassing ? '✓' : '✗' }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-center text-gray-400 font-bold">{{ $assessment['max_score'] }}</td>
                                        <td class="px-6 py-3 text-xs {{ $isPassing ? 'text-gray-400' : 'text-rose-500 font-bold' }}">
                                            {{ $isPassing ? ($assessment['notes'] ?? '—') : __('ត្រូវការ') . ' ≥ ' . $threshold }}
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Total row --}}
                                <tr class="bg-emerald-50/30 border-t-2 border-emerald-100">
                                    <td colspan="2" class="px-6 py-3 font-black text-gray-700 text-right">{{ __('សរុបរួម') }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="font-black text-emerald-600 text-base">{{ number_format($courseData['total_score'], 1) }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-center text-gray-400 font-bold">100</td>
                                    <td class="px-6 py-3"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-list text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-400">{{ __('មិនទាន់មានមុខវិជ្ជាសិក្សា') }}</p>
                    <p class="text-xs text-gray-300 mt-1">{{ __('អ្នកមិនទាន់បានចុះឈ្មោះក្នុងមុខវិជ្ជាណាមួយនៅឡើយ') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
