<x-app-layout>
    <div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-6">

            {{-- Header --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('student.my-grades') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
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
                                    <p class="text-xs text-gray-400">{{ $courseData['offering']->academic_year }} • ឆមាស {{ $courseData['offering']->semester }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black text-emerald-600">{{ number_format($courseData['total_score'], 1) }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">សរុប</p>
                            </div>
                        </div>
                        {{-- Summary chips --}}
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-[10px] font-bold">
                                <i class="fas fa-user-check"></i> វត្តមាន: {{ $courseData['attendance_score'] }}/15
                            </span>
                            @if($courseData['quiz_bonus'] > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-[10px] font-bold">
                                    <i class="fas fa-star"></i> Quiz Bonus: +{{ number_format($courseData['quiz_bonus'], 1) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Assessment Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">ប្រភេទ</th>
                                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">ឈ្មោះ</th>
                                    <th class="text-center px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">ពិន្ទុ</th>
                                    <th class="text-center px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">អតិបរមា</th>
                                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">កំណត់ចំណាំ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($courseData['assessments'] as $assessment)
                                    @php
                                        $typeColors = [
                                            'assignment' => 'bg-emerald-50 text-emerald-700',
                                            'midterm' => 'bg-blue-50 text-blue-700',
                                            'final' => 'bg-rose-50 text-rose-700',
                                            'quiz' => 'bg-amber-50 text-amber-700',
                                        ];
                                        $typeClass = $typeColors[$assessment['type']] ?? 'bg-gray-50 text-gray-700';
                                    @endphp
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-6 py-3">
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold {{ $typeClass }}">{{ $assessment['type_label'] }}</span>
                                        </td>
                                        <td class="px-6 py-3 font-semibold text-gray-700">{{ $assessment['title'] }}</td>
                                        <td class="px-6 py-3 text-center">
                                            @php
                                                $scoreClass = $assessment['score'] >= ($assessment['max_score'] * 0.5) ? 'text-emerald-600' : 'text-rose-500';
                                            @endphp
                                            <span class="font-black {{ $scoreClass }}">{{ number_format($assessment['score'], 1) }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-center text-gray-400 font-bold">{{ $assessment['max_score'] }}</td>
                                        <td class="px-6 py-3 text-xs text-gray-400">{{ $assessment['notes'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-list text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-400">{{ __('មិនមានពិន្ទុនៅឡើយទេ') }}</p>
                    <p class="text-xs text-gray-300 mt-1">{{ __('សាស្ត្រាចារ្យនឹងបញ្ចូលពិន្ទុនៅពេលក្រោយ') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
