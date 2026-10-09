<x-app-layout>
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">

        {{-- Hero Header --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-28 pt-10 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                            <i class="fas fa-calendar-check text-emerald-300 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight">{{ __('attendance_data') }}</h2>
                            <p class="text-slate-400 mt-1 text-sm">{{ __('track_student_attendance_across_courses') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 pb-12 relative z-10">

            {{-- Filter Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
                <form action="{{ route('admin.attendance.index') }}" method="GET" data-admin-realtime-filter class="space-y-4">
                    {{-- Row 1: Search --}}
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('search_course_professor') }}</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="{{ __('type_a_course_or_lecturer_name') }}"
                                   class="w-full pl-10 pr-4 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                    </div>

                    {{-- Row 2: Filters --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('study_program') }}</label>
                            <select name="department_id" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name_km }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('professor') }}</label>
                            <select name="professor_id" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($professors as $professor)
                                    <option value="{{ $professor->id }}" {{ request('professor_id') == $professor->id ? 'selected' : '' }}>{{ $professor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('semester') }}</label>
                            <select name="semester" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                <option value="ឆមាសទី១" {{ request('semester') == 'ឆមាសទី១' ? 'selected' : '' }}>{{ __('semester_1') }}</option>
                                <option value="ឆមាសទី២" {{ request('semester') == 'ឆមាសទី២' ? 'selected' : '' }}>{{ __('semester_2') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('generation') }}</label>
                            <select name="generation" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($generations as $gen)
                                    <option value="{{ $gen->name }}" {{ request('generation') == $gen->name ? 'selected' : '' }}>{{ __('generation_2') }}{{ $gen->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('academic_year') }}</label>
                            <select name="academic_year" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($academicYears as $academicYear)
                                    <option value="{{ $academicYear }}" {{ request('academic_year') === $academicYear ? 'selected' : '' }}>{{ $academicYear }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 3: Actions --}}
                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.attendance.index') }}"
                           class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold text-sm transition-colors">
                            <i class="fas fa-undo"></i>
                            <span>{{ __('reset_2') }}</span>
                        </a>
                    </div>
                </form>
            </div>

            <div data-admin-results>

                {{-- Results Count --}}
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-500">{{ __('found') }} <span class="font-bold text-gray-700">{{ $courseOfferings->total() }}</span> {{ __('course') }}</p>
                </div>

                {{-- Table Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('course') }}</th>
                                    <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('professor') }}</th>
                                    <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('semester_academic_year') }}</th>
                                    <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('students') }}</th>
                                    <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('actions_2') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($courseOfferings as $offering)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                                <i class="fas fa-book text-white text-sm"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-gray-900 text-sm truncate">{{ $offering->course?->title_km ?? $offering->course?->title_en ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-400 truncate">{{ $offering->course?->title_en ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 flex-shrink-0">
                                                <i class="fas fa-user-tie text-xs"></i>
                                            </div>
                                            <span class="text-sm text-gray-700 font-medium">{{ $offering->lecturer?->name ?? __('not_set') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">{{ $offering->semester }}</span>
                                            <span class="text-sm text-gray-500">{{ $offering->academic_year }}</span>
                                        </div>
                                        @if($offering->department)
                                            <div class="mt-1.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">
                                                    {{ $offering->department->name_km }} @if($offering->generation)(G{{ $offering->generation }})@endif
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] h-8 px-3 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-sm border border-emerald-100">
                                            {{ $offering->student_course_enrollments_count }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <a href="{{ route('admin.attendance.show', $offering->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-bold hover:bg-emerald-600 hover:text-white transition-colors">
                                            <i class="fas fa-eye"></i>
                                            <span>{{ __('view') }}</span>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-inbox text-gray-300 text-3xl"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-600">{{ __('no_data_2') }}</h3>
                                            <p class="text-sm text-gray-400">{{ __('please_try_searching_again') }}</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $courseOfferings->links() }}
                </div>

            </div>
        </div>
    </div>

</x-app-layout>
