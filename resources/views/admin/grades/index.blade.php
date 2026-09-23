<x-app-layout>
    <div class="bg-gray-50 min-h-screen font-sans text-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-chart-line text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('grade_management') }}</h1>
                    <p class="text-gray-500 mt-0.5">{{ __('view_all_student_grades_across_courses') }}</p>
                </div>
            </div>

            {{-- Filter Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
                <form action="{{ route('admin.grades.index') }}" method="GET" data-admin-realtime-filter class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('search_course_professor') }}</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-search text-gray-400"></i>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('type_a_course_or_lecturer_name') }}"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('study_program') }}</label>
                            <select name="department_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">{{ __('show_all') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name_km }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('professor') }}</label>
                            <select name="professor_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">{{ __('show_all') }}</option>
                                @foreach($professors as $professor)
                                    <option value="{{ $professor->id }}" {{ request('professor_id') == $professor->id ? 'selected' : '' }}>{{ $professor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('semester') }}</label>
                            <select name="semester" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">{{ __('all_2') }}</option>
                                <option value="ឆមាសទី១" {{ request('semester') == 'ឆមាសទី១' ? 'selected' : '' }}>{{ __('semester_1') }}</option>
                                <option value="ឆមាសទី២" {{ request('semester') == 'ឆមាសទី២' ? 'selected' : '' }}>{{ __('semester_2') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('generation') }}</label>
                            <select name="generation" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($generations as $gen)
                                    <option value="{{ $gen->name }}" {{ request('generation') == $gen->name ? 'selected' : '' }}>{{ __('generation_2') }}{{ $gen->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('academic_year') }}</label>
                            <select name="academic_year" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($academicYears as $academicYear)
                                    <option value="{{ $academicYear }}" {{ request('academic_year') === $academicYear ? 'selected' : '' }}>{{ $academicYear }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('grade_status') }}</label>
                            <select name="grade_status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">{{ __('all_2') }}</option>
                                <option value="not_graded" {{ request('grade_status') === 'not_graded' ? 'selected' : '' }}>{{ __('not_graded') }}</option>
                                <option value="partially_graded" {{ request('grade_status') === 'partially_graded' ? 'selected' : '' }}>{{ __('partially_graded') }}</option>
                                <option value="completed" {{ request('grade_status') === 'completed' ? 'selected' : '' }}>{{ __('all_students_have_scores') }}</option>
                                <option value="has_re_exam" {{ request('grade_status') === 'has_re_exam' ? 'selected' : '' }}>{{ __('has_re_exam_records') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 flex items-end gap-2">
                            <a href="{{ route('admin.grades.index') }}" title="{{ __('reset_2') }}" class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors font-bold text-sm">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl transition-all shadow-md text-sm whitespace-nowrap">
                                <i class="fas fa-filter mr-1"></i> {{ __('filter') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div data-admin-results>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400">{{ __('course_offerings') }}</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($gradeSummary['course_offerings']) }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400">{{ __('unique_enrolled_students') }}</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600">{{ number_format($gradeSummary['students']) }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400">{{ __('courses_with_re_exams') }}</p>
                    <p class="mt-2 text-2xl font-bold text-amber-600">{{ number_format($gradeSummary['re_exam_courses']) }}</p>
                </div>
            </div>
            {{-- Results Count --}}
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">{{ __('found') }} <span class="font-bold text-gray-700">{{ $courseOfferings->total() }}</span> {{ __('course') }}</p>
                <a href="{{ route('admin.grades.filtered-export', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i> {{ __('export_filtered_results') }}
                </a>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('course') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('professor') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('semester_academic_year') }}</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('students') }}</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('actions_2') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($courseOfferings as $offering)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-500 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-book text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $offering->course?->title_km ?? $offering->course?->title_en ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-400">{{ $offering->course?->title_en ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">{{ $offering->lecturer->name ?? __('not_set') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold">{{ $offering->semester }}</span>
                                        <span class="text-sm text-gray-500">{{ $offering->academic_year }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg bg-gray-100 text-gray-700 font-bold text-sm">
                                        {{ $offering->student_course_enrollments_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.grades.show', $offering->id) }}" class="p-2.5 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-colors" title="{{ __('view_grades') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.grades.export', $offering->id) }}" class="p-2.5 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-colors" title="{{ __('export_2') }}">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="{{ route('admin.grades.re-exam-form', $offering->id) }}" class="p-2.5 text-amber-600 hover:bg-amber-50 rounded-xl transition-colors" title="{{ __('re_exam_score') }}">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-bold text-gray-400">{{ __('no_data_2') }}</p>
                                        <p class="text-xs text-gray-300">{{ __('please_try_searching_again') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $courseOfferings->links() }}
            </div>
            </div>
        </div>
    </div>

</x-app-layout>
