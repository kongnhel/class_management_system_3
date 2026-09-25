<x-app-layout>
<x-slot name="header">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ __('my_teaching_courses') }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ __('all_course_offerings_you_are_teaching') }}</p>
        </div>
        <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 tabular-nums">
            {{ $courseOfferings->total() }} {{ __('courses_teaching') }}
        </span>
    </div>
</x-slot>

<div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
<div class="p-6 max-w-7xl mx-auto">

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center gap-3 text-sm font-semibold">
            <i class="fas fa-check-circle text-emerald-500"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center gap-3 text-sm font-semibold">
            <i class="fas fa-exclamation-triangle text-red-500"></i>
            {{ session('error') }}
        </div>
    @endif

    @if ($courseOfferings->isEmpty())
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center bg-white border border-dashed border-slate-300 rounded-2xl py-16 text-center">
            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mb-4">
                <i class="fas fa-book-open text-2xl"></i>
            </div>
            <p class="text-sm font-bold text-slate-500">{{ __('no_courses_assigned_yet') }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ __('please_contact_the_administration_if_you_have_any_questions') }}</p>
        </div>
    @else
        {{-- Course cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($courseOfferings as $offering)
                <div class="bg-white rounded-2xl border border-slate-200/70 p-6 hover:shadow-md hover:-translate-y-0.5 hover:border-slate-300 transition-all flex flex-col">

                    {{-- icon + year/semester chips --}}
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="flex flex-wrap justify-end gap-1.5">
                            <span class="text-[11px] font-semibold bg-slate-50 border border-slate-100 text-slate-500 px-2.5 py-1 rounded-lg tabular-nums">{{ $offering->academic_year }}</span>
                            <span class="text-[11px] font-semibold bg-slate-50 border border-slate-100 text-slate-500 px-2.5 py-1 rounded-lg">{{ __('semester') }} {{ $offering->semester }}</span>
                        </div>
                    </div>

                    {{-- titles --}}
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-slate-800 leading-snug break-words">
                            {{ $offering->course?->title_km ?? 'N/A' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                            {{ $offering->course?->title_en ?? 'N/A' }}
                        </p>

                        {{-- meta: department + student count --}}
                        <div class="flex flex-wrap items-center gap-2 mt-3">
                            @if($offering->course?->department?->name_km)
                                <span class="text-[11px] font-medium bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md">
                                    {{ $offering->course->department->name_km }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1 text-[11px] text-slate-500 font-medium tabular-nums">
                                <i class="fas fa-user-graduate text-slate-300 text-[10px]"></i>
                                {{ $offering->student_course_enrollments_count }} {{ __('students_2') }}
                            </span>
                        </div>
                    </div>

                    {{-- direct actions --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-5 pt-5 border-t border-slate-100">
                        <a wire:navigate href="{{ route('professor.students.in-course-offering', ['offering_id' => $offering->id]) }}"
                           class="flex items-center justify-center gap-2 h-11 rounded-xl bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 text-xs font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                            <i class="fas fa-users text-[11px]"></i>
                            {{ __('view_students') }}
                        </a>
                        <a wire:navigate href="{{ route('professor.manage-grades', ['offering_id' => $offering->id]) }}"
                           class="flex items-center justify-center gap-2 h-11 rounded-xl bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 text-xs font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                            <i class="fas fa-chart-line text-[11px]"></i>
                            {{ __('manage_grades') }}
                        </a>
                        <a wire:navigate href="{{ route('professor.attendance.index', ['courseOffering' => $offering->id]) }}"
                           class="flex items-center justify-center gap-2 h-11 rounded-xl bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 text-xs font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                            <i class="fas fa-clipboard-check text-[11px]"></i>
                            {{ __('attendance') }}
                        </a>
                        <a wire:navigate href="{{ route('professor.quizzes.index', ['offering_id' => $offering->id]) }}"
                           class="flex items-center justify-center gap-2 h-11 rounded-xl bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 text-xs font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                            <i class="fas fa-stopwatch text-[11px]"></i>
                            {{ __('quiz') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10 flex justify-center">
            {{ $courseOfferings->links('pagination::tailwind') }}
        </div>
    @endif
</div>
</div>
</x-app-layout>
