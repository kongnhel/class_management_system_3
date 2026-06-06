<x-app-layout>
    {{-- Main Container: Light Gray Background for Professional Look --}}
    <div class="min-h-screen bg-slate-50/80 font-['Battambang'] pb-12">
        
        {{-- Header Section with Gradient Accent --}}
        <div class="bg-white border-b border-slate-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between py-4 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                            <i class="fas fa-graduation-cap text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight leading-none">{{ __('stu_grades_title') }}</h2>
                            <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Academic Performance Record</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase">{{ __('stu_semester') }}</span>
                        <div class="h-4 w-px bg-slate-300"></div>
                        <span class="text-sm font-bold text-indigo-600">ឆ្នាំសិក្សា ២០២៤-២០២៥</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">

            {{-- 1. OVERVIEW CARDS (Modern Grid) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                {{-- Rank Card --}}
                <div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-slate-100 flex items-center justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('stu_rank') }}</p>
                        <h3 class="text-3xl font-black text-slate-800">#{{ $overallRank ?? '-' }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 blur-2xl"></div>
                </div>

                {{-- Average Card --}}
                <div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-slate-100 flex items-center justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('stu_average') }}</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ number_format($averageScore ?? 0, 2) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 blur-2xl"></div>
                </div>

                {{-- Total Score Card --}}
                <div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-slate-100 flex items-center justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('stu_total_grade') }}</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalFinalScore ?? 0, 1) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 blur-2xl"></div>
                </div>

                {{-- Grade Card --}}
                <div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-slate-100 flex items-center justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('stu_gpa') }}</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ $overallGrade ?? '-' }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 blur-2xl"></div>
                </div>
            </div>

            {{-- 2. DETAILED REPORT (Table) --}}
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                        {{ __('ប្រតិបត្តិពិន្ទុតាមមុខវិជ្ជា') }}
                    </h3>
                </div>

                {{-- Desktop Table View --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-8 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('មុខវិជ្ជា') }}</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('ពិន្ទុបំបែក') }}</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('វត្តមាន') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-48">{{ __('សរុប & និទ្ទេស') }}</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('stu_rank') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($grades as $grade)
                                @php
                                    $percentage = min(100, ($grade->total_score / 100) * 100);
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    {{-- Subject --}}
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm border border-slate-200">
                                                {{ substr($grade->course_name_en, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $grade->course_name_en }}</div>
                                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $grade->course_name_km }}</div>
                                                @if($grade->is_failed)
                                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                                        <i class="fas fa-exclamation-circle"></i> {{ __('ប្រឡងសង') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Breakdown Scores (Horizontal Pills) --}}
                                    <td class="px-6 py-5">
                                        <div class="flex justify-center gap-2 flex-wrap">
                                            @foreach($grade->assessments as $asmt)
                                                @php
                                                    $isLow = ($asmt->display_type == 'final' && $asmt->score_obtained < 24) || ($asmt->display_type != 'final' && $asmt->score_obtained < ($asmt->max_score/2));
                                                @endphp
                                                <div class="flex flex-col items-center p-1.5 rounded-lg border {{ $isLow ? 'bg-rose-50 border-rose-100' : 'bg-white border-slate-100' }} shadow-sm w-20">
                                                    <span class="text-[9px] text-slate-400 font-bold uppercase">{{ $asmt->display_type == 'quiz' ? 'Quiz' : $asmt->display_type }}</span>
                                                    <span class="text-xs font-bold {{ $isLow ? 'text-rose-600' : 'text-slate-700' }}">
                                                        {{ number_format($asmt->score_obtained, 1) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Attendance --}}
                                    <td class="px-6 py-5 text-center">
                                        <span class="inline-flex flex-col items-center justify-center w-16 py-1 rounded-lg border border-slate-100 bg-slate-50">
                                            <span class="text-xs font-bold text-slate-700">{{ number_format($grade->attendance_score, 1) }}</span>
                                            <span class="text-[9px] font-bold {{ $grade->attendance_score >= 9 ? 'text-emerald-500' : 'text-rose-500' }}">
                                                {{ $grade->attendance_score >= 9 ? 'P' : 'F' }}
                                            </span>
                                        </span>
                                    </td>

                                    {{-- Total & Grade --}}
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-sm font-black text-slate-800">{{ number_format($grade->total_score, 1) }}</span>
                                            <span class="px-2 py-0.5 rounded-md text-xs font-bold {{ $grade->is_failed ? 'bg-rose-100 text-rose-600' : 'bg-indigo-50 text-indigo-600' }}">
                                                {{ $grade->grade }}
                                            </span>
                                        </div>
                                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $grade->is_failed ? 'bg-rose-500' : ($percentage >= 80 ? 'bg-emerald-500' : ($percentage >= 50 ? 'bg-indigo-500' : 'bg-amber-500')) }}" 
                                                 style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </td>

                                    {{-- Rank --}}
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-sm font-bold text-slate-400 italic">#{{ $grade->course_rank }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                                        <i class="fas fa-folder-open text-4xl mb-3 text-slate-200"></i>
                                        <p class="text-sm">{{ __('មិនទាន់មានទិន្នន័យសម្រាប់បង្ហាញ') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile List View (Clean Cards) --}}
                <div class="md:hidden bg-slate-50 p-4 space-y-4">
                    @foreach ($grades as $grade)
                        @php $percentage = min(100, ($grade->total_score / 100) * 100); @endphp
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $grade->course_name_en }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $grade->course_name_km }}</p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-lg font-black {{ $grade->is_failed ? 'text-rose-600' : 'text-indigo-600' }}">
                                        {{ $grade->grade }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-bold">Rank #{{ $grade->course_rank }}</span>
                                </div>
                            </div>

                            {{-- Scores Grid --}}
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                @foreach($grade->assessments->take(3) as $asmt)
                                    <div class="bg-slate-50 p-2 rounded-lg text-center border border-slate-100">
                                        <p class="text-[9px] text-slate-400 uppercase font-bold truncate">{{ $asmt->display_type }}</p>
                                        <p class="text-xs font-bold text-slate-700">{{ number_format($asmt->score_obtained, 1) }}</p>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Progress Bar --}}
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $grade->is_failed ? 'bg-rose-500' : 'bg-indigo-500' }}" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-700">{{ number_format($grade->total_score, 1) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($grades->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-white">
                        {{ $grades->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>