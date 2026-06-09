<x-app-layout>
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <div class="bg-slate-900 text-white pb-32 pt-12 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</h2>
                        <p class="text-slate-400 mt-2 max-w-2xl text-sm leading-relaxed">
                            {{ $courseOffering->semester }} / {{ $courseOffering->academic_year }}
                            @if($courseOffering->lecturer)
                                <span class="ml-2">| គ្រូ៖ {{ $courseOffering->lecturer->name }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.grades.export', $courseOffering->id) }}" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 rounded-lg font-bold shadow-lg transition-all text-sm">
                            <i class="fas fa-download"></i> នាំចេញ CSV
                        </a>
                        <a href="{{ route('admin.grades.index') }}" class="flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white px-4 py-2.5 rounded-lg font-bold shadow-lg transition-all text-sm">
                            <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-10">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-gray-900">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">សិស្សសរុប</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-blue-600">{{ $stats['graded'] }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">មានពិន្ទុ</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-emerald-600">{{ number_format($stats['avg_grade'], 1) }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">មធ្យមភាគ</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-emerald-600">{{ number_format($stats['max_grade'], 1) }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">ខ្ពស់បំផុត</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-rose-600">{{ number_format($stats['min_grade'], 1) }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">ទាបបំផុត</div>
                </div>
            </div>

            {{-- Grade Table --}}
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">បញ្ជីពិន្ទុសិស្ស</h3>
                    <span class="text-xs text-gray-400 font-medium bg-gray-50 px-3 py-1 rounded-full">{{ $students->count() }} នាក់</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-slate-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-500 uppercase">ឈ្មោះ</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-emerald-600 uppercase">វត្តមាន</th>
                                @foreach($assessments as $assessment)
                                    <th class="px-3 py-3 text-center text-[11px] font-bold uppercase
                                        {{ $assessment instanceof \App\Models\Assignment ? 'text-blue-600' : ($assessment instanceof \App\Models\Quiz ? 'text-amber-600' : 'text-purple-600') }}">
                                        {{ Str::limit($assessment->title_km, 15) }}
                                    </th>
                                @endforeach
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-600 uppercase">សរុប</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-600 uppercase">និទ្ទេស</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-600 uppercase">ស្ថានភាព</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($students as $index => $student)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $profilePic = $student->userProfile?->profile_picture_url ?? $student->studentProfile?->profile_picture_url;
                                        @endphp
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                            @if($profilePic)
                                                <img src="{{ $profilePic }}?tr=w-64,h-64,fo-face" class="w-full h-full object-cover" alt="">
                                            @else
                                                <span class="text-white font-bold text-xs">{{ mb_substr($student->studentProfile->full_name_km ?? $student->name, 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-semibold text-sm text-gray-900">{{ $student->studentProfile->full_name_km ?? $student->name }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $student->student_id_code ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg bg-emerald-50 text-emerald-600 font-bold text-xs">
                                        {{ $student->getAttendanceScoreByCourse($courseOffering->id) ?? 0 }}
                                    </span>
                                </td>
                                @foreach($assessments as $assessment)
                                    @php
                                        $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' :
                                               (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                                        $key = $type . '_' . $assessment->id;
                                        $score = $gradebook[$student->id][$key] ?? 0;
                                    @endphp
                                    <td class="px-3 py-3 text-center">
                                        <span class="text-xs font-bold {{ $score > 0 ? 'text-gray-700' : 'text-gray-300' }}">
                                            {{ $score > 0 ? number_format($score, 1) : '-' }}
                                        </span>
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-extrabold {{ $student->isPassing ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ number_format($student->temp_total, 1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $gradeColors = [
                                            'A' => 'bg-emerald-100 text-emerald-700',
                                            'B+' => 'bg-emerald-50 text-emerald-600',
                                            'B' => 'bg-blue-100 text-blue-700',
                                            'C+' => 'bg-blue-50 text-blue-600',
                                            'C' => 'bg-amber-100 text-amber-700',
                                            'D+' => 'bg-amber-50 text-amber-600',
                                            'D' => 'bg-orange-100 text-orange-700',
                                            'F' => 'bg-rose-100 text-rose-700',
                                        ];
                                        $colorClass = $gradeColors[$student->letterGrade] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-bold rounded-full {{ $colorClass }}">{{ $student->letterGrade }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($student->isPassing)
                                        <span class="px-2 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700">ជាប់</span>
                                    @else
                                        <span class="px-2 py-1 text-[11px] font-bold rounded-full bg-rose-100 text-rose-700">មិនជាប់</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 6 + $assessments->count() }}" class="px-6 py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </div>
                                        <p class="text-sm font-bold">មិនមានទិន្នន័យពិន្ទុ</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
