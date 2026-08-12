<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

            {{-- Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-star text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">ពិន្ទុទាំងអស់</h1>
                    <p class="text-sm text-gray-500 mt-0.5">បញ្ជីពិន្ទុដែលអ្នកគ្រប់គ្រង</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6" x-data="gradeFilters()">
                <div class="flex flex-col sm:flex-row gap-3">
                    {{-- Search by student name --}}
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input
                            type="text"
                            x-model="search"
                            @compositionstart="isComposing = true" @compositionend="isComposing = false; filterRows()" @input="if (!isComposing && !$event.isComposing) filterRows()"
                            placeholder="ស្វែងរកឈ្មោះសិស្ស..."
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
                            <option value="">មុខវិជ្ជា​ទាំងអស់</option>
                            <template x-for="c in courses" :key="c">
                                <option :value="c" x-text="c"></option>
                            </template>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                    </div>
                    {{-- Filter by type --}}
                    <div class="relative sm:w-40">
                        <i class="fas fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <select
                            x-model="typeFilter"
                            @change="filterRows()"
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50 appearance-none"
                        >
                            <option value="">ប្រភេទ​ទាំងអស់</option>
                            <option value="exam">ប្រឡង</option>
                            <option value="assignment">កិច្ចការ</option>
                            <option value="quiz">Quiz</option>
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
                            <option value="">កាលបរិច្ឆេទ​ទាំងអស់</option>
                            <template x-for="d in dates" :key="d">
                                <option :value="d" x-text="d"></option>
                            </template>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                    </div>
                    {{-- Clear --}}
                    <button
                        x-show="search || courseFilter || typeFilter || dateFilter"
                        @click="clearFilters()"
                        class="px-4 py-2.5 rounded-xl bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100 transition-colors whitespace-nowrap"
                    >
                        <i class="fas fa-times mr-1"></i> សម្អាត
                    </button>
                </div>
                {{-- Result count --}}
                <div class="mt-3 text-xs text-gray-400 font-bold" x-show="search || courseFilter || typeFilter || dateFilter">
                    រកឃើញ <span x-text="visibleCount" class="text-emerald-600"></span> ក្នុងចំណោម <span x-text="totalCount" class="text-gray-600"></span> កំណត់ត្រា
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <div class="text-xs font-bold text-gray-400 uppercase mb-1">សរុបកត់ត្រា</div>
                    <div class="text-2xl font-black text-gray-800">{{ $grades->total() }}</div>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm">បញ្ជីពិន្ទុ</h3>
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-bold text-gray-500">{{ $grades->total() }} កំណត់ត្រា</span>
                </div>

                @if($grades->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">សិស្ស</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">មុខវិជ្ជា</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">ប្រភេទ</th>
                                <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase">ពិន្ទុ</th>
                                <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase">អតិបរមា</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">កាលបរិច្ឆេទ</th>
                            </tr>
                        </thead>
                        <tbody id="grades-tbody" class="divide-y divide-gray-100">
                            @forelse($grades as $grade)
                                @php
                                    $percent = $grade->max_score > 0 ? round(($grade->score / $grade->max_score) * 100) : 0;
                                    $typeLabels = ['exam' => 'ប្រឡង', 'assignment' => 'កិច្ចការ', 'quiz' => 'Quiz'];
                                    $typeColors = ['exam' => 'purple', 'assignment' => 'emerald', 'quiz' => 'amber'];
                                    $tColor = $typeColors[$grade->assessment_type] ?? 'gray';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors grade-row"
                                    data-name="{{ mb_strtolower($grade->student_name ?? '') }}"
                                    data-course="{{ mb_strtolower($grade->course_title_km ?? '') }}"
                                    data-type="{{ $grade->assessment_type }}"
                                    data-date="{{ \Carbon\Carbon::parse($grade->date)->format('Y-m-d') }}">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center text-white font-bold text-[10px] shadow-sm shrink-0 overflow-hidden">
                                                @if($grade->profile_pic)
                                                    <img src="{{ $grade->profile_pic }}" class="w-full h-full rounded-full object-cover" alt="">
                                                @else
                                                    {{ mb_substr($grade->student_name ?? '?', 0, 1) }}
                                                @endif
                                            </div>
                                            <span class="text-sm font-semibold text-gray-800">{{ $grade->student_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $grade->course_title_km }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-{{ $tColor }}-50 text-{{ $tColor }}-700 border border-{{ $tColor }}-100">
                                            {{ $typeLabels[$grade->assessment_type] ?? $grade->assessment_type }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="text-sm font-bold {{ $percent >= 50 ? 'text-emerald-600' : 'text-red-500' }}">
                                            {{ $grade->score }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center text-sm text-gray-500">{{ $grade->max_score }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-400">{{ \Carbon\Carbon::parse($grade->date)->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                                        <p class="text-sm font-bold text-gray-400">មិនមានពិន្ទុ</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- No results message --}}
                <div id="no-results" class="px-6 py-16 text-center hidden">
                    <i class="fas fa-search text-gray-300 text-3xl mb-3"></i>
                    <p class="text-sm font-bold text-gray-400">មិនឃើញលទ្ធផល</p>
                    <p class="text-xs text-gray-300 mt-1">សូមព្យាយាមស្វែងរកផ្សេង</p>
                </div>
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $grades->links('pagination::tailwind', ['pageName' => 'gradesPage']) }}
                </div>
                @else
                <div class="px-6 py-16 text-center">
                    <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                    <p class="text-sm font-bold text-gray-400">មិនមានពិន្ទុ</p>
                    <p class="text-xs text-gray-300 mt-1">ពិន្ទុនឹងបង្ហាញនៅទីនេះនៅពេលអ្នកបញ្ចូល</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function gradeFilters() {
            return {
                search: '',
                isComposing: false,
                courseFilter: '',
                typeFilter: '',
                dateFilter: '',
                courses: [],
                dates: [],
                visibleCount: 0,
                totalCount: 0,

                init() {
                    const rows = document.querySelectorAll('.grade-row');
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
                    const rows = document.querySelectorAll('.grade-row');
                    const noResults = document.getElementById('no-results');
                    const pagination = document.querySelector('.px-5.py-3.border-t');
                    let count = 0;

                    rows.forEach(row => {
                        const name = row.getAttribute('data-name') || '';
                        const course = row.getAttribute('data-course') || '';
                        const type = row.getAttribute('data-type') || '';
                        const date = row.getAttribute('data-date') || '';

                        const matchSearch = !this.search || name.includes(this.search.toLowerCase());
                        const matchCourse = !this.courseFilter || course === this.courseFilter.toLowerCase();
                        const matchType = !this.typeFilter || type === this.typeFilter;
                        const matchDate = !this.dateFilter || date === this.dateFilter;

                        if (matchSearch && matchCourse && matchType && matchDate) {
                            row.style.display = '';
                            count++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    this.visibleCount = count;

                    const hasFilter = this.search || this.courseFilter || this.typeFilter || this.dateFilter;
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
                    this.typeFilter = '';
                    this.dateFilter = '';
                    this.filterRows();
                }
            }
        }
    </script>
</x-app-layout>
