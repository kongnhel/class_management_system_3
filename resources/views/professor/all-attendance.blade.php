<x-app-layout>
    @php
        $allStudentsJson = $students->map(fn($s) => ['id' => $s->id, 'name' => $s->profile?->full_name_km ?? $s->name])->values()->toJson();
    @endphp
    <script type="application/json" id="students-data">{!! $allStudentsJson !!}</script>

    <div class="bg-gray-50 min-h-screen" x-data="attendanceApp()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

            {{-- Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('all_attendance') }}</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ __('check_and_record_attendance_for_your_courses') }}</p>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium">
                    <i class="fas fa-check-circle text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-medium">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Add Form --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
                <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-emerald-500"></i> {{ __('add_new_record') }}
                </h3>
                <form action="{{ route('professor.attendances.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="student_user_ids" :value="selectedStudents.join(',')">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('course_2') }}</label>
                            <select name="course_offering_id" x-model="selectedCourse" @change="fetchStudents($event.target.value)" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">{{ __('select_course') }}</option>
                                @foreach($professorCourseOfferings as $offering)
                                    <option value="{{ $offering->id }}">{{ $offering->course?->title_km ?? $offering->course?->title_en ?? 'N/A' }} ({{ $offering->academic_year }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Student Multi-Select Dropdown --}}
                        <div class="relative" @click.away="studentDropdownOpen = false">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('students_2') }}</label>
                            <button type="button" @click="toggleStudentDropdown()"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all text-left flex items-center justify-between">
                                <span x-text="getStudentNames() || '{{ __("select_students") }}'"></span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs" :class="studentDropdownOpen ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="studentDropdownOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                class="absolute z-30 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" x-model="selectAll" @change="toggleAllStudents()"
                                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                        <span class="text-sm font-bold text-gray-700">{{ __('select_all') }}</span>
                                    </label>
                                </div>
                                <div class="p-1">
                                    <template x-for="student in courseFilterStudents" :key="student.id">
                                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-emerald-50 cursor-pointer" :class="selectedStudents.includes(student.id) ? 'bg-emerald-50' : ''">
                                            <input type="checkbox" :value="student.id"
                                                @change="toggleStudent(student.id)"
                                                :checked="selectedStudents.includes(student.id)"
                                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                            <span class="text-sm text-gray-700" x-text="student.name"></span>
                                        </label>
                                    </template>
                                </div>
                                <div class="p-2 border-t border-gray-100 sticky bottom-0 bg-white" x-show="selectedStudents.length > 0">
                                    <button type="button" @click="clearStudents()" class="w-full text-xs font-bold text-gray-400 hover:text-gray-600 py-1">{{ __('clear_selection') }}</button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('date_4') }}</label>
                            <input type="date" name="date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('status_2') }}</label>
                            <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="present">{{ __('present_2') }}</option>
                                <option value="absent">{{ __('absent_2') }}</option>
                                <option value="permission">{{ __('permission_2') }}</option>
                                <option value="late">{{ __('late_2') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl font-bold text-sm shadow-md transition-all active:scale-95">
                            <i class="fas fa-save"></i> {{ __('save_4') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6" x-data="attendanceFilters()">
                <div class="flex flex-col sm:flex-row gap-3">
                    {{-- Search by student name --}}
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input
                            type="text"
                            x-model="search"
                            @compositionstart="isComposing = true" @compositionend="isComposing = false; filterRows()" @input="if (!isComposing && !$event.isComposing) filterRows()"
                            placeholder="{{ __('search_student_name') }}"
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50"
                        >
                    </div>
                    {{-- Filter by course --}}
                    <div class="relative sm:w-48">
                        <i class="fas fa-book absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <select
                            x-model="courseFilter"
                            @change="filterRows()"
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50 appearance-none"
                        >
                            <option value="">{{ __('all_courses') }}</option>
                            <template x-for="c in courses" :key="c">
                                <option :value="c" x-text="c"></option>
                            </template>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                    </div>
                    {{-- Filter by status --}}
                    <div class="relative sm:w-40">
                        <i class="fas fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <select
                            x-model="statusFilter"
                            @change="filterRows()"
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50 appearance-none"
                        >
                            <option value="">{{ __('all_statuses') }}</option>
                            <option value="present">{{ __('present_2') }}</option>
                            <option value="absent">{{ __('absent_2') }}</option>
                            <option value="permission">{{ __('permission_2') }}</option>
                            <option value="late">{{ __('late_2') }}</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                    </div>
                    {{-- Filter by date --}}
                    <div class="relative sm:w-44">
                        <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <select
                            x-model="dateFilter"
                            @change="filterRows()"
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50 appearance-none"
                        >
                            <option value="">{{ __('all_dates') }}</option>
                            <template x-for="d in dates" :key="d">
                                <option :value="d" x-text="d"></option>
                            </template>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                    </div>
                    {{-- Clear --}}
                    <button
                        x-show="search || courseFilter || statusFilter || dateFilter"
                        @click="clearFilters()"
                        class="px-4 py-2.5 rounded-xl bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100 transition-colors whitespace-nowrap"
                    >
                        <i class="fas fa-times mr-1"></i> {{ __('clear_2') }}
                    </button>
                </div>
                {{-- Result count --}}
                <div class="mt-3 text-xs text-gray-400 font-bold" x-show="search || courseFilter || statusFilter || dateFilter">
                    {{ __('found') }} <span x-text="visibleCount" class="text-emerald-600"></span> {{ __('out_of') }} <span x-text="totalCount" class="text-gray-600"></span> {{ __('records') }}
                </div>
            </div>

            {{-- Records Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm">{{ __('attendance_records') }}</h3>
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-bold text-gray-500">{{ $attendances->total() }} {{ __('records') }}</span>
                </div>

                @if($attendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">{{ __('student_2') }}</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">{{ __('course_2') }}</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">{{ __('date_4') }}</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">{{ __('status_2') }}</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">{{ __('remarks_2') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($attendances as $record)
                                @php
                                    $colors = ['present' => 'green', 'absent' => 'red', 'permission' => 'blue', 'late' => 'amber'];
                                    $color = $colors[$record->status] ?? 'gray';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors attendance-row"
                                    data-name="{{ mb_strtolower($record->student?->studentProfile?->full_name_km ?? $record->student?->name ?? '') }}"
                                    data-course="{{ mb_strtolower($record->courseOffering?->course?->title_km ?? '') }}"
                                    data-status="{{ $record->status }}"
                                    data-date="{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            @php $profilePic = $record->student?->studentProfile?->profile_picture_url ?? $record->student?->profile?->profile_picture_url ?? null; @endphp
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center text-white font-bold text-[10px] shadow-sm shrink-0 overflow-hidden">
                                                @if($profilePic)
                                                    <img src="{{ $profilePic }}" class="w-full h-full rounded-full object-cover" alt="">
                                                @else
                                                    {{ mb_substr($record->student?->studentProfile?->full_name_km ?? $record->student?->name ?? '?', 0, 1) }}
                                                @endif
                                            </div>
                                            <span class="text-sm font-semibold text-gray-800">{{ $record->student?->studentProfile?->full_name_km ?? $record->student?->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $record->courseOffering?->course?->title_km ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-400">{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100">
                                            {{ $record->status_km }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-400">{{ $record->remarks ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
<p class="text-sm font-bold text-gray-400">{{ __('no_records') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- No results message --}}
                <div id="no-results" class="px-6 py-16 text-center hidden">
                    <i class="fas fa-search text-gray-300 text-3xl mb-3"></i>
                    <p class="text-sm font-bold text-gray-400">{{ __('no_results_found') }}</p>
                    <p class="text-xs text-gray-300 mt-1">{{ __('please_try_another_search') }}</p>
                </div>
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $attendances->links('pagination::tailwind') }}
                </div>
                @else
                <div class="px-6 py-16 text-center">
                    <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                    <p class="text-sm font-bold text-gray-400">{{ __('no_records') }}</p>
                    <p class="text-xs text-gray-300 mt-1">{{ __('please_use_the_form_above_to_add') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function attendanceApp() {
            const allStudents = JSON.parse(document.getElementById('students-data').textContent || '[]');
            return {
                selectedStudents: [],
                selectAll: false,
                studentDropdownOpen: false,
                students: allStudents,
                courseFilterStudents: allStudents,
                selectedCourse: '',

                toggleStudentDropdown() { this.studentDropdownOpen = !this.studentDropdownOpen; },
                toggleStudent(id) {
                    const idx = this.selectedStudents.indexOf(id);
                    if (idx === -1) this.selectedStudents.push(id);
                    else this.selectedStudents.splice(idx, 1);
                    this.selectAll = this.selectedStudents.length === this.courseFilterStudents.length && this.courseFilterStudents.length > 0;
                },
                toggleAllStudents() {
                    if (this.selectAll) {
                        this.selectedStudents = this.courseFilterStudents.map(s => s.id);
                    } else {
                        this.selectedStudents = [];
                    }
                },
                clearStudents() { this.selectedStudents = []; this.selectAll = false; this.studentDropdownOpen = false; },
                getStudentNames() {
                    if (this.selectedStudents.length === 0) return '';
                    if (this.selectedStudents.length === 1) {
                        const s = this.courseFilterStudents.find(s => s.id === this.selectedStudents[0]);
                        return s ? s.name : '';
                    }
                    return this.selectedStudents.length + ' {{ __("students_selected") }}';
                },
                fetchStudents(courseId) {
                    this.selectedStudents = [];
                    this.selectAll = false;
                    if (!courseId) {
                        this.courseFilterStudents = this.students;
                        return;
                    }
                    fetch('/professor/api/course-offering/' + courseId + '/students', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.courseFilterStudents = data.students || [];
                    })
                    .catch(() => {
                        this.courseFilterStudents = this.students;
                    });
                }
            }
        }

        function attendanceFilters() {
            return {
                search: '',
                isComposing: false,
                courseFilter: '',
                statusFilter: '',
                dateFilter: '',
                courses: [],
                dates: [],
                visibleCount: 0,
                totalCount: 0,

                init() {
                    const rows = document.querySelectorAll('.attendance-row');
                    this.totalCount = rows.length;
                    this.visibleCount = rows.length;

                    const courseSet = new Set();
                    const dateSet = new Set();
                    rows.forEach(row => {
                        const c = row.getAttribute('data-course');
                        if (c) courseSet.add(c);
                        const d = row.getAttribute('data-date');
                        if (d) dateSet.add(d);
                    });
                    this.courses = [...courseSet].sort();
                    this.dates = [...dateSet].sort().reverse();
                },

                filterRows() {
                    const rows = document.querySelectorAll('.attendance-row');
                    const noResults = document.getElementById('no-results');
                    const pagination = document.querySelector('.px-5.py-3.border-t');
                    let count = 0;

                    rows.forEach(row => {
                        const name = row.getAttribute('data-name') || '';
                        const course = row.getAttribute('data-course') || '';
                        const status = row.getAttribute('data-status') || '';
                        const date = row.getAttribute('data-date') || '';

                        const matchSearch = !this.search || name.includes(this.search.toLowerCase());
                        const matchCourse = !this.courseFilter || course === this.courseFilter.toLowerCase();
                        const matchStatus = !this.statusFilter || status === this.statusFilter;
                        const matchDate = !this.dateFilter || date === this.dateFilter;

                        if (matchSearch && matchCourse && matchStatus && matchDate) {
                            row.style.display = '';
                            count++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    this.visibleCount = count;

                    const hasFilter = this.search || this.courseFilter || this.statusFilter || this.dateFilter;
                    if (count === 0 && hasFilter) {
                        noResults.classList.remove('hidden');
                        if (pagination) pagination.style.display = 'none';
                    } else {
                        noResults.classList.add('hidden');
                        if (pagination) pagination.style.display = '';
                    }
                },

                clearFilters() {
                    this.search = '';
                    this.courseFilter = '';
                    this.statusFilter = '';
                    this.dateFilter = '';
                    this.filterRows();
                }
            }
        }
    </script>
</x-app-layout>
