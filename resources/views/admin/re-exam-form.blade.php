<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-redo text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('retake') }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $offering->course->title_km ?? $offering->course->title_en ?? 'N/A' }} · {{ $offering->academic_year }} · {{ $offering->semester }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.grades.show', $offering->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 shadow-sm transition-all">
                    <i class="fas fa-arrow-left"></i> {{ __('go_back') }}
                </a>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium">
                    <i class="fas fa-check-circle text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-medium">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <span>{{ $error }}</span>{{ $loop->last ? '' : ', ' }}
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Info --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-info-circle text-amber-500"></i>
                    <h3 class="font-bold text-gray-800 text-sm">{{ __('re_exam_information') }}</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400 text-xs font-bold uppercase">{{ __('course') }}</span>
                        <p class="font-semibold text-gray-800">{{ $offering->course->title_km ?? $offering->course->title_en }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs font-bold uppercase">{{ __('academic_year') }}</span>
                        <p class="font-semibold text-gray-800">{{ $offering->academic_year }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs font-bold uppercase">{{ __('semester') }}</span>
                        <p class="font-semibold text-gray-800">{{ $offering->semester }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs font-bold uppercase">{{ __('students_needing_re_exam') }}</span>
                        <p class="font-semibold text-amber-600">{{ $studentsWithFailed->count() }} {{ __('students_2') }}</p>
                    </div>
                </div>
            </div>

            {{-- Re-exam Form --}}
            @if($studentsWithFailed->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ __('no_students_need_re_exam') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('all_students_have_passed_attendance_and_key_assessments') }}</p>
                </div>
            @else
                <form action="{{ route('admin.grades.re-exam-store', $offering->id) }}" method="POST" class="space-y-6">
                    @csrf

                    @foreach($studentsWithFailed as $studentData)
                        @php
                            $student = $studentData['student'];
                        @endphp
                        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                            {{-- Student Header --}}
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $student->name ?? 'N/A' }}</h4>
                                        <p class="text-xs text-gray-400">{{ $student->student_id_code ?? '' }} · {{ $student->email ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-gray-400">{{ __('attendance') }}:</span>
                                    <span class="px-2 py-0.5 rounded text-xs font-bold {{ $studentData['attendance_score'] >= 10 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        {{ number_format($studentData['attendance_score'], 1) }} / 15
                                    </span>
                                </div>
                            </div>

                            {{-- Failed Components --}}
                            <div class="p-6 space-y-4">
                                @foreach($studentData['failed_items'] as $idx => $item)
                                    @php
                                        $i = $loop->parent->index . '_' . $idx;
                                        $typeLabel = match($item['assessment_type']) {
                                            'assignment' => __('assignment'),
                                            'midterm' => __('midterm_exam'),
                                            'final' => __('final_exam'),
                                            default => ucfirst($item['assessment_type']),
                                        };
                                    @endphp
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-600">{{ $typeLabel }}</span>
                                            @if($item['has_re_exam'])
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-600">{{ __('re_exam_completed') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800 mb-2">{{ $item['title'] }}</p>
                                        <div class="flex items-center gap-3 mb-3 text-xs text-gray-400">
                                            <span>{{ __('current_score') }}: <span class="font-bold text-red-500">{{ number_format($item['current_score'], 1) }}</span> / {{ $item['max_score'] }}</span>
                                            <span>·</span>
                                            <span>{{ __('required') }}: <span class="font-bold text-amber-600">≥ {{ $item['threshold'] }}</span></span>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">{{ __('re_exam_score') }}</label>
                                            <input type="number"
                                                name="scores[{{ $i }}][new_score]"
                                                min="0"
                                                max="{{ $item['max_score'] }}"
                                                step="0.5"
                                                value="{{ $item['has_re_exam'] ? $item['current_score'] : '' }}"
                                                class="w-full sm:w-48 border-gray-200 rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500"
                                                placeholder="0 - {{ $item['max_score'] }}">
                                            <input type="hidden" name="scores[{{ $i }}][student_user_id]" value="{{ $student->id }}">
                                            <input type="hidden" name="scores[{{ $i }}][assessment_type]" value="{{ $item['assessment_type'] }}">
                                            <input type="hidden" name="scores[{{ $i }}][assessment_id]" value="{{ $item['assessment_id'] }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{-- Submit --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.grades.re-exam-form', $offering->id) }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                            {{ __('reset_2') }}
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-amber-500 text-white rounded-xl font-bold text-sm hover:bg-amber-600 shadow-md shadow-amber-200 transition-all">
                            <i class="fas fa-save mr-1.5"></i> {{ __('save_re_exam_scores') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
