<x-app-layout>
    <div class="min-h-screen bg-slate-50/80 font-['Battambang'] pb-12">

        {{-- Header --}}
        <div class="bg-white border-b border-slate-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between py-4 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                            <i class="fas fa-graduation-cap text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800 tracking-tight leading-none">{{ __('stu_grades_title') }}</h2>
                            <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Academic Performance Record</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="window.print()" class="hidden md:inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all">
                            <i class="fas fa-print"></i> {{ __('ព្រីន') }}
                        </button>
                        <div class="flex items-center gap-2 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                            <span class="text-xs font-bold text-slate-400 uppercase">{{ __('stu_semester') }}</span>
                            <div class="h-4 w-px bg-slate-300"></div>
                            <span class="text-sm font-bold text-emerald-600">{{ $currentYear }}</span>
                            @if($currentSemester)
                                <span class="text-xs font-bold text-slate-500">· {{ $currentSemester }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">

            {{-- Filter Bar --}}
            @if($academicYears->count() > 1 || $semesters->count() > 1)
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6">
                <form action="{{ route('student.my-grades') }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">{{ __('ឆ្នាំសិក្សា') }}</label>
                        <select name="academic_year" class="w-full border-slate-200 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">{{ __('គ្រប់ឆ្នាំសិក្សា') }}</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">{{ __('ឆមាស') }}</label>
                        <select name="semester" class="w-full border-slate-200 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">{{ __('គ្រប់ឆមាស') }}</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem }}" {{ $currentSemester == $sem ? 'selected' : '' }}>{{ $sem }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition-all shadow-md shadow-emerald-200">
                            <i class="fas fa-filter mr-1.5"></i> {{ __('ចម្រាញ់') }}
                        </button>
                        <a href="{{ route('student.my-grades') }}" class="px-5 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition-all">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
            @endif

            {{-- 1. Overview Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                {{-- Rank --}}
                <div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-slate-100 flex items-center justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('stu_rank') }}</p>
                        <h3 class="text-3xl font-black text-slate-800">#{{ $overallRank }}</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">/ {{ $totalClassmates }} {{ __('នាក់') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 blur-2xl"></div>
                </div>

                {{-- GPA --}}
                <div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-slate-100 flex items-center justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('stu_gpa') }}</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ number_format($gpa, 2) }}</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $overallGrade }} · {{ $totalCredits }} {{ __(' Credits') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 blur-2xl"></div>
                </div>

                {{-- Average --}}
                <div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-slate-100 flex items-center justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('stu_average') }}</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ number_format($averageScore, 1) }}<span class="text-lg text-slate-400">%</span></h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ __('ពិន្ទុមធ្យម') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 blur-2xl"></div>
                </div>

                {{-- Total Score --}}
                <div class="bg-white p-5 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-slate-100 flex items-center justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('stu_total_grade') }}</p>
                        <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalFinalScore, 1) }}</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $grades->count() }} {{ __('មុខវិជ្ជា') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 blur-2xl"></div>
                </div>
            </div>

            {{-- 2. Detailed Table --}}
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-emerald-500 rounded-full"></span>
                        {{ __('ប្រតិបត្តិពិន្ទុតាមមុខវិជ្ជា') }}
                    </h3>
                    <button onclick="window.print()" class="md:hidden inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">
                        <i class="fas fa-print"></i> {{ __('ព្រីន') }}
                    </button>
                </div>

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('មុខវិជ្ជា') }}</th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Credits') }}</th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('ពិន្ទុបំបែក') }}</th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('វត្តមាន') }}</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-44">{{ __('សរុប & និទ្ទេស') }}</th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('stu_rank') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($grades as $grade)
                                @php
                                    $percentage = min(100, $grade->total_score);
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    {{-- Subject --}}
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm border border-slate-200 shrink-0">
                                                {{ strtoupper(substr($grade->course_name_en, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-sm font-bold text-slate-800 group-hover:text-emerald-600 transition-colors truncate">{{ $grade->course_name_en }}</div>
                                                <div class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $grade->course_name_km }}</div>
                                                @if($grade->is_failed)
                                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                                        <i class="fas fa-exclamation-circle"></i> {{ __('ប្រឡងសង') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Credits --}}
                                    <td class="px-4 py-5 text-center">
                                        <span class="text-sm font-bold text-slate-600">{{ $grade->credits }}</span>
                                    </td>

                                    {{-- Breakdown --}}
                                    <td class="px-4 py-5">
                                        <div class="flex justify-center gap-1.5 flex-wrap">
                                            @foreach($grade->assessments as $asmt)
                                                @php
                                                    $isLow = ($asmt->display_type == 'Final' && $asmt->score_obtained < 24) || ($asmt->display_type != 'Final' && $asmt->score_obtained < ($asmt->max_score/2));
                                                @endphp
                                                <div class="flex flex-col items-center p-1.5 rounded-lg border {{ $isLow ? 'bg-rose-50 border-rose-100' : 'bg-white border-slate-100' }} shadow-sm w-[4.5rem]">
                                                    <span class="text-[9px] text-slate-400 font-bold uppercase truncate max-w-full">{{ $asmt->display_type }}</span>
                                                    <span class="text-xs font-bold {{ $isLow ? 'text-rose-600' : 'text-slate-700' }}">
                                                        {{ number_format($asmt->score_obtained, 1) }}
                                                    </span>
                                                    <span class="text-[8px] text-slate-300">/ {{ number_format($asmt->max_score, 0) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Attendance --}}
                                    <td class="px-4 py-5 text-center">
                                        <span class="inline-flex flex-col items-center justify-center w-14 py-1 rounded-lg border border-slate-100 bg-slate-50">
                                            <span class="text-xs font-bold text-slate-700">{{ number_format($grade->attendance_score, 1) }}</span>
                                            <span class="text-[9px] font-bold {{ $grade->attendance_score >= 9 ? 'text-emerald-500' : 'text-rose-500' }}">
                                                {{ $grade->attendance_score >= 9 ? 'P' : 'F' }}
                                            </span>
                                        </span>
                                    </td>

                                    {{-- Total & Grade --}}
                                    <td class="px-4 py-5">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-sm font-black text-slate-800">{{ number_format($grade->total_score, 1) }}</span>
                                            <span class="px-2 py-0.5 rounded-md text-xs font-bold {{ $grade->is_failed ? 'bg-rose-100 text-rose-600' : 'bg-emerald-50 text-emerald-600' }}">
                                                {{ $grade->grade }}
                                            </span>
                                        </div>
                                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-500 {{ $grade->is_failed ? 'bg-rose-500' : ($percentage >= 80 ? 'bg-emerald-500' : ($percentage >= 50 ? 'bg-emerald-500' : 'bg-amber-500')) }}"
                                                 style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </td>

                                    {{-- Rank --}}
                                    <td class="px-4 py-5 text-center">
                                        <span class="text-sm font-bold text-slate-400 italic">#{{ $grade->course_rank }}</span>
                                        <span class="text-[10px] text-slate-300">/{{ $grade->total_students }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                        <i class="fas fa-folder-open text-4xl mb-3 text-slate-200"></i>
                                        <p class="text-sm">{{ __('មិនទាន់មានទិន្នន័យសម្រាប់បង្ហាញ') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden bg-slate-50 p-4 space-y-4">
                    @foreach ($grades as $grade)
                        @php $percentage = min(100, $grade->total_score); @endphp
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                            <div class="flex justify-between items-start mb-3">
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-bold text-slate-900 truncate">{{ $grade->course_name_en }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $grade->course_name_km }}</p>
                                    <span class="text-[10px] text-slate-400 font-bold">{{ $grade->credits }} Credits</span>
                                </div>
                                <div class="flex flex-col items-end shrink-0 ml-3">
                                    <span class="text-lg font-black {{ $grade->is_failed ? 'text-rose-600' : 'text-emerald-600' }}">
                                        {{ $grade->grade }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-bold">#{{ $grade->course_rank }}/{{ $grade->total_students }}</span>
                                </div>
                            </div>

                            {{-- All Assessments --}}
                            <div class="grid grid-cols-3 gap-1.5 mb-3">
                                @foreach($grade->assessments as $asmt)
                                    @php
                                        $isLow = ($asmt->display_type == 'Final' && $asmt->score_obtained < 24) || ($asmt->display_type != 'Final' && $asmt->score_obtained < ($asmt->max_score/2));
                                    @endphp
                                    <div class="bg-slate-50 p-1.5 rounded-lg text-center border border-slate-100">
                                        <p class="text-[9px] text-slate-400 uppercase font-bold truncate">{{ $asmt->display_type }}</p>
                                        <p class="text-xs font-bold {{ $isLow ? 'text-rose-600' : 'text-slate-700' }}">{{ number_format($asmt->score_obtained, 1) }}</p>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Progress --}}
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $grade->is_failed ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-700">{{ number_format($grade->total_score, 1) }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($grades->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-white">
                        {{ $grades->withQueryString()->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            nav, .sticky, button, .no-print { display: none !important; }
            .min-h-screen { background: white !important; }
            .bg-white { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
        }
    </style>
    @endpush
</x-app-layout>
