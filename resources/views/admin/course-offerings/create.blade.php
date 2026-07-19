<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-16 pt-10">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.manage-course-offerings') }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="fas fa-arrow-left text-white"></i>
                    </a>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                            <i class="fas fa-plus-circle text-emerald-300 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight">{{ __('បង្កើតការផ្តល់ជូនមុខវិជ្ជាថ្មី') }}</h2>
                            <p class="text-slate-400 mt-1 text-sm">{{ __('បំពេញព័ត៌មានខាងក្រោមដើម្បីបង្កើតការផ្តល់ជូនមុខវិជ្ជាថ្មី') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 pb-12 relative z-10">
            @if ($errors->any() || session('error'))
            <div x-data="{ show: true }" x-show="show" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 text-sm">{{ __('មានបញ្ហា!') }}</p>
                        <ul class="text-red-600 text-xs mt-1 space-y-0.5">
                            @if(session('error')) <li>{{ session('error') }}</li> @endif
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xs"></i></button>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.store-course-offering') }}" class="space-y-6">
                @csrf

                {{-- Section 1: Basic Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <span class="text-emerald-600 font-bold text-sm">1</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('ព័ត៌មានមូលដ្ឋាន') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('ជ្រើសរើសកម្មវិធីសិក្សា និងមុខវិជ្ជា') }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Dropdown 1: Program + Generation --}}
                        <div>
                            <label for="program_gen_select" class="block text-sm font-bold text-gray-700 mb-1.5">
                                {{ __('កម្មវិធីសិក្សា និងជំនាន់') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="program_gen_select" class="w-full min-w-0 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm px-4 py-2.5 bg-gray-50">
                                <option value="">{{ __('ជ្រើសរើសកម្មវិធីសិក្សា និងជំនាន់') }}</option>
                            </select>
                        </div>

                        {{-- Dropdown 2: Course (disabled until program+gen is selected) --}}
                        <div>
                            <label for="course_id" class="block text-sm font-bold text-gray-700 mb-1.5">
                                {{ __('មុខវិជ្ជា') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="course_id" name="course_id" disabled class="w-full min-w-0 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm px-4 py-2.5 bg-gray-100 text-gray-400 cursor-not-allowed" required>
                                <option value="">{{ __('សូមជ្រើសរើសកម្មវិធីសិក្សាមុន') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Target Programs --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <span class="text-emerald-600 font-bold text-sm">2</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('កម្មវិធីសិក្សា និងជំនាន់') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('កំណត់ជំនាញ និងជំនាន់ដែលគោលដៅ') }}</p>
                        </div>
                    </div>
                    <div id="programs-container" class="space-y-3">
                        <div id="program-placeholder" class="text-center py-8 text-gray-400 rounded-xl border-2 border-dashed border-gray-200">
                            <i class="fas fa-graduation-cap text-3xl mb-3 block text-gray-300"></i>
                            <p class="text-sm italic">{{ __('សូមជ្រើសរើសមុខវិជ្ជាដើម្បីបង្ហាញកម្មវិធីសិក្សា និងជំនាន់ដោយស្វ័យប្រវត្តិ') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Section 3 & 4: Details & Schedules --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Offering Details --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <span class="text-emerald-600 font-bold text-sm">3</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ __('ព័ត៌មានការផ្តល់ជូន') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('កំណត់សាស្ត្រាចារ្យ និងព័ត៌មានផ្សេងៗ') }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label for="lecturer_user_id" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('សាស្ត្រាចារ្យ') }} <span class="text-red-500">*</span></label>
                                <select id="lecturer_user_id" name="lecturer_user_id" class="w-full min-w-0 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm px-4 py-2.5" required>
                                    <option value="">{{ __('ជ្រើសរើសសាស្ត្រាចារ្យ') }}</option>
                                    @foreach ($professors as $professor)
                                        <option value="{{ $professor->id }}" {{ old('lecturer_user_id') == $professor->id ? 'selected' : '' }}>{{ $professor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="academic_year" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ឆ្នាំសិក្សា') }} <span class="text-red-500">*</span></label>
                                    <select name="academic_year" id="academic_year" class="w-full min-w-0 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm px-4 py-2.5" required>
                                        <option value="">{{ __('ជ្រើសរើស') }}</option>
                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year->name }}" data-start="{{ \Carbon\Carbon::parse($year->start_date)->format('Y-m-d') }}" data-end="{{ \Carbon\Carbon::parse($year->end_date)->format('Y-m-d') }}" {{ old('academic_year') == $year->name ? 'selected' : '' }}>
                                                {{ $year->name }} {{ $year->is_current ? '('.__('បច្ចុប្បន្ន').')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="semester" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ឆមាស') }} <span class="text-red-500">*</span></label>
                                    <select name="semester" id="semester" class="w-full min-w-0 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm px-4 py-2.5" required>
                                        <option value="" disabled {{ old('semester') ? '' : 'selected' }}>{{ __('ជ្រើសរើស') }}</option>
                                        <option value="ឆមាសទី១" {{ old('semester') == 'ឆមាសទី១' ? 'selected' : '' }}>{{ __('ឆមាសទី១') }}</option>
                                        <option value="ឆមាសទី២" {{ old('semester') == 'ឆមាសទី២' ? 'selected' : '' }}>{{ __('ឆមាសទី២') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="capacity" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ចំនួននិស្សិតអតិបរមា') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="capacity" id="capacity" class="w-full min-w-0 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm px-4 py-2.5" value="{{ old('capacity') }}" placeholder="ឧទាហរណ៍: ៣០" required>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="start_date" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('កាលបរិច្ឆេទចាប់ផ្តើម') }} <span class="text-red-500">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="w-full min-w-0 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm px-4 py-2.5" value="{{ old('start_date') }}" required>
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('កាលបរិច្ឆេទបញ្ចប់') }} <span class="text-red-500">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="w-full min-w-0 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm px-4 py-2.5" value="{{ old('end_date') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Schedule Type Selector --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('ជ្រើសរើសបែងប្រាក់វិភាគ') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="schedule-type-card p-4 border-2 rounded-xl cursor-pointer transition-all
                                {{ old('schedule_type', 'custom') === 'custom' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}"
                                for="schedule_type_custom">
                                <input type="radio" name="schedule_type" value="custom" id="schedule_type_custom" class="sr-only"
                                    {{ old('schedule_type', 'custom') === 'custom' ? 'checked' : '' }}>
                                <div class="text-center">
                                    <i class="fas fa-calendar-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="font-bold text-gray-900">{{ __('កំណត់ថ្ងៃខ្លី (Custom)') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ __('ជ្រើសរើសថ្ងៃមួយចំនួន') }}</p>
                                </div>
                            </label>

                            <label class="schedule-type-card p-4 border-2 rounded-xl cursor-pointer transition-all
                                {{ old('schedule_type') === 'weekday' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}"
                                for="schedule_type_weekday">
                                <input type="radio" name="schedule_type" value="weekday" id="schedule_type_weekday" class="sr-only"
                                    {{ old('schedule_type') === 'weekday' ? 'checked' : '' }}>
                                <div class="text-center">
                                    <i class="fas fa-calendar-week text-3xl text-blue-500 mb-2"></i>
                                    <p class="font-bold text-gray-900">{{ __('ចន្ទ-សុក្រ (Mon-Fri)') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ __('បែងប្រាក់វេនប្រចាំថ្ងៃ') }}</p>
                                </div>
                            </label>

                            <label class="schedule-type-card p-4 border-2 rounded-xl cursor-pointer transition-all
                                {{ old('schedule_type') === 'weekend' ? 'border-amber-500 bg-amber-50' : 'border-gray-200 hover:border-gray-300' }}"
                                for="schedule_type_weekend">
                                <input type="radio" name="schedule_type" value="weekend" id="schedule_type_weekend" class="sr-only"
                                    {{ old('schedule_type') === 'weekend' ? 'checked' : '' }}>
                                <div class="text-center">
                                    <i class="fas fa-calendar-day text-3xl text-amber-500 mb-2"></i>
                                    <p class="font-bold text-gray-900">{{ __('សៅរ៍-អាទិត្យ (Sat-Sun)') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ __('បែងប្រាក់វេនប្រចាំថ្ងៃ') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Weekday Pattern Form --}}
                    <div id="weekday-pattern-form" class="hidden bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-bold text-gray-900">{{ __('កំណត់វេនសិក្សាប្រចាំថ្ងៃចន្ទ-សុក្រ') }}</h4>
                            <button type="button" id="add-weekday-session" class="flex items-center gap-2 text-emerald-600 font-bold text-sm hover:text-emerald-700">
                                <i class="fas fa-plus text-xs"></i> {{ __('បន្ថែមវេនសិក្សា') }}
                            </button>
                        </div>
                        <div id="weekday-sessions-container" class="space-y-3"></div>
                        <p class="text-xs text-gray-500 mt-2">{{ __('បែងប្រាក់វេនសិក្សាដែលត្រូវបានបញ្ចូលនេះនឹងត្រូវបានអនុវត្តដោយស្វ័យប្រវត្តិលើថ្ងៃចន្ទ ទី២ ពុធ ព្រហស្បតិ៍ សុក្រ') }}</p>
                    </div>

                    {{-- Weekend Pattern Form --}}
                    <div id="weekend-pattern-form" class="hidden bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-bold text-gray-900">{{ __('កំណត់វេនសិក្សាប្រចាំថ្ងៃសៅរ៍-អាទិត្យ') }}</h4>
                            <button type="button" id="add-weekend-session" class="flex items-center gap-2 text-emerald-600 font-bold text-sm hover:text-emerald-700">
                                <i class="fas fa-plus text-xs"></i> {{ __('បន្ថែមវេនសិក្សា') }}
                            </button>
                        </div>
                        <div id="weekend-sessions-container" class="space-y-3"></div>
                        <p class="text-xs text-gray-500 mt-2">{{ __('បែងប្រាក់វេនសិក្សាដែលត្រូវបានបញ្ចូលនេះនឹងត្រូវបានអនុវត្តដោយស្វ័យប្រវត្តិលើថ្ងៃសៅរ៍ និងអាទិត្យ') }}</p>
                    </div>

                    {{-- Custom Schedule Form (existing) --}}
                    <div id="custom-schedule-form">
                        {{-- Schedules --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                                    <span class="text-amber-600 font-bold text-sm">4</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ __('កាលវិភាគសិក្សា') }}</h3>
                                    <p class="text-xs text-gray-500">{{ __('កំណត់ថ្ងៃ ម៉ោង និងបន្ទប់សិក្សា') }}</p>
                                </div>
                            </div>
                            <div id="schedules-container" class="space-y-3 flex-grow min-h-[120px]"></div>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <button type="button" id="add-schedule" class="flex items-center gap-2 text-emerald-600 font-bold text-sm hover:text-emerald-700 transition-colors">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                                        <i class="fas fa-plus text-xs"></i>
                                    </div>
                                    <span>{{ __('បន្ថែមម៉ោងសិក្សាថ្មី') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="{{ route('admin.manage-course-offerings') }}" class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold text-sm transition-colors">
                            <i class="fas fa-times"></i> <span>{{ __('បោះបង់') }}</span>
                        </a>
                        <button type="submit" class="flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/25 transition-all">
                            <i class="fas fa-check"></i> <span>{{ __('បង្កើតការផ្តល់ជូនមុខវិជ្ជា') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ──────────────────────────────────────────────
            //  1. Build data structures from Blade
            // ──────────────────────────────────────────────
            //
            //  courseDetails:  { courseId: { programs: [...], generation: N } }
            //  programGenGroups: { "programId-generation": { label, programId, generation, courses: [] } }
            //
            const courseDetails = {};
            const programGenGroups = {};
            const coursesRaw = {!! json_encode($courses->map(fn($c) => [
                'id' => $c->id,
                'title_km' => $c->title_km,
                'title_en' => $c->title_en,
                'generation' => $c->generation,
                'programs' => $c->programs->map(fn($p) => ['id' => $p->id, 'name_km' => $p->name_km, 'name_en' => $p->name_en]),
            ])) !!};

            coursesRaw.forEach(function(course) {
                // Store course details for later lookup
                courseDetails[course.id] = {
                    programs: course.programs,
                    generation: course.generation
                };

                // Group by each program this course belongs to + its generation
                course.programs.forEach(function(prog) {
                    const key = prog.id + '-' + course.generation;
                    if (!programGenGroups[key]) {
                        programGenGroups[key] = {
                            label: (prog.name_km || prog.name_en) + ' — ជំនាន់ទី ' + course.generation,
                            programId: prog.id,
                            programName: prog.name_km || prog.name_en,
                            generation: course.generation,
                            courses: []
                        };
                    }
                    programGenGroups[key].courses.push({
                        id: course.id,
                        title_km: course.title_km,
                        title_en: course.title_en
                    });
                });
            });

            // ──────────────────────────────────────────────
            //  2. Populate Dropdown 1 (Program + Generation)
            // ──────────────────────────────────────────────
            const programGenSelect = document.getElementById('program_gen_select');
            const courseSelect = document.getElementById('course_id');
            const programsContainer = document.getElementById('programs-container');

            // Sort groups by program name then generation
            const sortedKeys = Object.keys(programGenGroups).sort(function(a, b) {
                const ga = programGenGroups[a], gb = programGenGroups[b];
                if (ga.programName === gb.programName) return gb.generation - ga.generation;
                return ga.programName.localeCompare(gb.programName);
            });

            sortedKeys.forEach(function(key) {
                const group = programGenGroups[key];
                const opt = document.createElement('option');
                opt.value = key;
                opt.textContent = group.label + ' (' + group.courses.length + ' ' + '{{ __("មុខវិជ្ជា") }}' + ')';
                programGenSelect.appendChild(opt);
            });

            // ──────────────────────────────────────────────
            //  3. Dropdown 1 → Dropdown 2 (chained)
            // ──────────────────────────────────────────────
            programGenSelect.addEventListener('change', function() {
                const key = this.value;

                // Reset course dropdown
                courseSelect.innerHTML = '<option value="">{{ __("ជ្រើសរើសមុខវិជ្ជា") }}</option>';
                courseSelect.value = '';

                // Reset programs container
                programsContainer.innerHTML =
                    '<div id="program-placeholder" class="text-center py-8 text-gray-400 rounded-xl border-2 border-dashed border-gray-200">' +
                    '<i class="fas fa-graduation-cap text-3xl mb-3 block text-gray-300"></i>' +
                    '<p class="text-sm italic">{{ __("សូមជ្រើសរើសមុខវិជ្ជាដើម្បីបង្ហាញកម្មវិធីសិក្សា និងជំនាន់ដោយស្វ័យប្រវត្តិ") }}</p>' +
                    '</div>';

                if (!key) {
                    // Disable course select
                    courseSelect.disabled = true;
                    courseSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                    courseSelect.classList.remove('bg-white');
                    courseSelect.querySelector('option').textContent = '{{ __("សូមជ្រើសរើសកម្មវិធីសិក្សាមុន") }}';
                    return;
                }

                // Enable course select
                courseSelect.disabled = false;
                courseSelect.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                courseSelect.classList.add('bg-white');

                // Populate courses for this program+generation
                const group = programGenGroups[key];
                group.courses.forEach(function(c) {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = (c.title_km || c.title_en);
                    courseSelect.appendChild(opt);
                });
            });

            // ──────────────────────────────────────────────
            //  4. Dropdown 2 → Target Programs section
            // ──────────────────────────────────────────────
            courseSelect.addEventListener('change', function() {
                if (!this.value) {
                    programsContainer.innerHTML =
                        '<div id="program-placeholder" class="text-center py-8 text-gray-400 rounded-xl border-2 border-dashed border-gray-200">' +
                        '<i class="fas fa-graduation-cap text-3xl mb-3 block text-gray-300"></i>' +
                        '<p class="text-sm italic">{{ __("សូមជ្រើសរើសមុខវិជ្ជាដើម្បីបង្ហាញកម្មវិធីសិក្សា និងជំនាន់ដោយស្វ័យប្រវត្តិ") }}</p>' +
                        '</div>';
                    return;
                }

                const course = courseDetails[this.value];
                const programsData = course.programs || [];
                const courseGen = course.generation || '';

                if (programsData.length === 0) {
                    programsContainer.innerHTML =
                        '<div class="text-center py-6 text-gray-400 rounded-xl border-2 border-dashed border-gray-200">' +
                        '<p class="text-sm">{{ __("មិនមានកម្មវិធីសិក្សាសម្រាប់មុខវិជ្ជានេះទេ") }}</p></div>';
                    return;
                }

                programsContainer.innerHTML = '';
                programsData.forEach(function(prog, index) {
                    const row = document.createElement('div');
                    row.className = 'flex items-center gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200';
                    row.innerHTML =
                        '<div class="flex-grow grid grid-cols-2 gap-3">' +
                            '<div>' +
                                '<label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __("កម្មវិធីសិក្សា") }}</label>' +
                                '<div class="w-full py-2 px-3 bg-white rounded-xl text-sm font-semibold text-gray-700 border border-gray-200">' + (prog.name_km || prog.name_en) + '</div>' +
                                '<input type="hidden" name="target_programs[' + index + '][program_id]" value="' + prog.id + '">' +
                            '</div>' +
                            '<div>' +
                                '<label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __("ជំនាន់") }}</label>' +
                                '<div class="w-full py-2 px-3 bg-emerald-50 rounded-xl text-sm font-bold text-emerald-700 border border-emerald-100">' + (courseGen ? 'ជំនាន់ទី ' + courseGen : '{{ __("មិនទាន់កំណត់") }}') + '</div>' +
                                '<input type="hidden" name="target_programs[' + index + '][generation]" value="' + courseGen + '">' +
                            '</div>' +
                        '</div>';
                    programsContainer.appendChild(row);
                });
            });

            // ──────────────────────────────────────────────
            //  5. Academic year auto-fill dates
            // ──────────────────────────────────────────────
            const academicYearSelect = document.getElementById('academic_year');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            academicYearSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const startDate = selectedOption.getAttribute('data-start');
                const endDate = selectedOption.getAttribute('data-end');
                if (startDate && endDate) {
                    startDateInput.value = startDate;
                    endDateInput.value = endDate;
                }
            });
            if (academicYearSelect.value) academicYearSelect.dispatchEvent(new Event('change'));

            // ──────────────────────────────────────────────
            //  6. Schedule Pattern Selection & Session Builder
            // ──────────────────────────────────────────────
            const scheduleTypeRadios = document.querySelectorAll('input[name="schedule_type"]');
            const weekdayForm = document.getElementById('weekday-pattern-form');
            const weekendForm = document.getElementById('weekend-pattern-form');
            const customForm = document.getElementById('custom-schedule-form');
            const rooms = {!! json_encode($rooms->map(fn($r) => ['id' => $r->id, 'room_number' => $r->room_number])) !!};

            function toggleForms() {
                const type = document.querySelector('input[name="schedule_type"]:checked').value;

                weekdayForm.classList.toggle('hidden', type !== 'weekday');
                weekendForm.classList.toggle('hidden', type !== 'weekend');
                customForm.classList.toggle('hidden', type !== 'custom');

                // Disable inputs in hidden forms so they don't submit
                [weekdayForm, weekendForm, customForm].forEach(form => {
                    const inputs = form.querySelectorAll('input, select');
                    inputs.forEach(input => input.disabled = form.classList.contains('hidden'));
                });
            }

            scheduleTypeRadios.forEach(radio => radio.addEventListener('change', toggleForms));
            toggleForms(); // Initial

            // Session row builder
            function createSessionRow(containerId, prefix, rooms, initialData = {}) {
                const container = document.getElementById(containerId);
                const idx = Date.now() + Math.random();
                const sessionCount = container.querySelectorAll('.session-row').length + 1;

                const roomOptions = rooms.map(r =>
                    '<option value="' + r.id + '" ' + (initialData.room_id == r.id ? 'selected' : '') + '>' + r.room_number + '</option>'
                ).join('');

                const row = document.createElement('div');
                row.className = 'session-row group bg-gray-50 p-4 rounded-xl border border-gray-200';
                row.innerHTML =
                    '<div class="flex items-center justify-between mb-3">' +
                        '<div class="flex items-center gap-2 text-sm font-bold text-emerald-600">' +
                            '<i class="fas fa-clock text-xs"></i><span>{{ __('វេន') }} ' + sessionCount + '</span>' +
                        '</div>' +
                        '<button type="button" class="remove-session text-gray-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">' +
                            '<i class="fas fa-times-circle text-sm"></i>' +
                        '</button>' +
                    '</div>' +
                    '<div class="grid grid-cols-1 md:grid-cols-4 gap-3">' +
                        '<div>' +
                            '<label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ចាប់ផ្តើម') }} *</label>' +
                            '<input type="time" name="' + prefix + '[' + idx + '][start_time]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" value="' + (initialData.start_time || '') + '" required>' +
                        '</div>' +
                        '<div>' +
                            '<label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('បញ្ចប់') }} *</label>' +
                            '<input type="time" name="' + prefix + '[' + idx + '][end_time]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" value="' + (initialData.end_time || '') + '" required>' +
                        '</div>' +
                        '<div>' +
                            '<label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('បន្ទប់') }} *</label>' +
                            '<select name="' + prefix + '[' + idx + '][room_id]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>' +
                                '<option value="">{{ __('ជ្រើសរើសបន្ទប់') }}</option>' + roomOptions +
                            '</select>' +
                        '</div>' +
                        '<div class="flex items-end">' +
                            '<button type="button" class="remove-session text-red-500 hover:text-red-700 text-sm p-2">' +
                                '<i class="fas fa-trash"></i>' +
                            '</button>' +
                        '</div>' +
                    '</div>';

                container.appendChild(row);

                row.querySelector('.remove-session').addEventListener('click', function() {
                    row.remove();
                    updateSessionLabels(containerId);
                });

                return row;
            }

            function updateSessionLabels(containerId) {
                const container = document.getElementById(containerId);
                container.querySelectorAll('.session-row').forEach((row, i) => {
                    const label = row.querySelector('.flex.items-center span');
                    if (label) label.textContent = '{{ __('វេន') }} ' + (i + 1);
                });
            }

            // Add session buttons
            document.getElementById('add-weekday-session').addEventListener('click', function() {
                createSessionRow('weekday-sessions-container', 'weekday_sessions', rooms);
            });
            document.getElementById('add-weekend-session').addEventListener('click', function() {
                createSessionRow('weekend-sessions-container', 'weekend_sessions', rooms);
            });

            // Load old input for pattern forms
            const oldWeekday = {!! json_encode(old('weekday_sessions', [])) !!};
            const oldWeekend = {!! json_encode(old('weekend_sessions', [])) !!};

            if (Object.keys(oldWeekday).length > 0) {
                Object.values(oldWeekday).forEach(s => createSessionRow('weekday-sessions-container', 'weekday_sessions', rooms, s));
            } else {
                createSessionRow('weekday-sessions-container', 'weekday_sessions', rooms);
            }

            if (Object.keys(oldWeekend).length > 0) {
                Object.values(oldWeekend).forEach(s => createSessionRow('weekend-sessions-container', 'weekend_sessions', rooms, s));
            } else {
                createSessionRow('weekend-sessions-container', 'weekend_sessions', rooms);
            }

            // Re-apply toggleForms to disable inputs in hidden forms after session rows are created
            toggleForms();

            // Custom schedule form (existing)
            const scheduleContainer = document.getElementById('schedules-container');
            const addScheduleBtn = document.getElementById('add-schedule');
            let scheduleIndex = 0;

            function addScheduleRow(initialData = {}) {
                const scheduleDiv = document.createElement('div');
                scheduleDiv.className = 'schedule-row group bg-gray-50 p-4 rounded-xl border border-gray-200';
                const currentSessions = document.querySelectorAll('.schedule-row').length + 1;
                const roomOptions = rooms.map(room => '<option value="' + room.id + '" ' + (initialData.room_id == room.id ? 'selected' : '') + '>' + room.room_number + '</option>').join('');
                scheduleDiv.innerHTML =
                    '<div class="flex items-center justify-between mb-3">' +
                        '<div class="flex items-center gap-2 text-sm font-bold text-emerald-600">' +
                            '<i class="fas fa-clock text-xs"></i><span>Session ' + currentSessions + '</span>' +
                        '</div>' +
                        '<button type="button" class="remove-schedule text-gray-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">' +
                            '<i class="fas fa-times-circle text-sm"></i>' +
                        '</button>' +
                    '</div>' +
                    '<div class="grid grid-cols-2 md:grid-cols-4 gap-3">' +
                        '<div class="col-span-2 md:col-span-1">' +
                            '<label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __("ថ្ងៃ") }}</label>' +
                            '<select name="schedules[' + scheduleIndex + '][day_of_week]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>' +
                                '<option value="">{{ __("រើសថ្ងៃ") }}</option>' +
                                '<option value="Monday" ' + (initialData.day_of_week === 'Monday' ? 'selected' : '') + '>{{ __("ច័ន្ទ") }}</option>' +
                                '<option value="Tuesday" ' + (initialData.day_of_week === 'Tuesday' ? 'selected' : '') + '>{{ __("អង្គារ") }}</option>' +
                                '<option value="Wednesday" ' + (initialData.day_of_week === 'Wednesday' ? 'selected' : '') + '>{{ __("ពុធ") }}</option>' +
                                '<option value="Thursday" ' + (initialData.day_of_week === 'Thursday' ? 'selected' : '') + '>{{ __("ព្រហស្បតិ៍") }}</option>' +
                                '<option value="Friday" ' + (initialData.day_of_week === 'Friday' ? 'selected' : '') + '>{{ __("សុក្រ") }}</option>' +
                                '<option value="Saturday" ' + (initialData.day_of_week === 'Saturday' ? 'selected' : '') + '>{{ __("សៅរ៍") }}</option>' +
                                '<option value="Sunday" ' + (initialData.day_of_week === 'Sunday' ? 'selected' : '') + '>{{ __("អាទិត្យ") }}</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="col-span-2 md:col-span-1">' +
                            '<label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __("បន្ទប់") }}</label>' +
                            '<select name="schedules[' + scheduleIndex + '][room_id]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>' +
                                '<option value="">{{ __("រើសបន្ទប់") }}</option>' + roomOptions +
                            '</select>' +
                        '</div>' +
                        '<div>' +
                            '<label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __("ចាប់ផ្តើម") }}</label>' +
                            '<input type="time" name="schedules[' + scheduleIndex + '][start_time]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" value="' + (initialData.start_time || '') + '" required>' +
                        '</div>' +
                        '<div>' +
                            '<label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __("បញ្ចប់") }}</label>' +
                            '<input type="time" name="schedules[' + scheduleIndex + '][end_time]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" value="' + (initialData.end_time || '') + '" required>' +
                        '</div>' +
                    '</div>';
                scheduleContainer.appendChild(scheduleDiv);
                scheduleDiv.querySelector('.remove-schedule').addEventListener('click', function() { scheduleDiv.remove(); updateSessionNumbers(); });
                scheduleIndex++;
            }

            function updateSessionNumbers() {
                document.querySelectorAll('.schedule-row').forEach(function(row, i) {
                    const label = row.querySelector('.flex.items-center span');
                    if (label) label.textContent = 'Session ' + (i + 1);
                });
            }

            addScheduleBtn.addEventListener('click', function() { addScheduleRow(); });
            var oldSchedules = {!! json_encode(old('schedules', [])) !!};
            if (Object.keys(oldSchedules).length > 0) {
                Object.values(oldSchedules).forEach(function(s) { addScheduleRow(s); });
            } else {
                addScheduleRow();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var lecturerEl = document.getElementById('lecturer_user_id');
            if (lecturerEl) {
                new TomSelect(lecturerEl, {
                    maxItems: 1,
                    placeholder: '{{ __("ជ្រើសរើសសាស្ត្រាចារ្យ") }}',
                    searchField: ['text'],
                    sortField: { field: 'text', direction: 'asc' },
                    plugins: {
                        clear_button: { title: 'Remove' }
                    }
                });
            }
        });
    </script>

    <style>
        .ts-wrapper {
            position: relative;
            z-index: 50;
        }
        .ts-wrapper .ts-control {
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 0.625rem 0.75rem;
            font-size: 0.875rem;
            background-color: #f9fafb;
            min-height: 42px;
        }
        .ts-wrapper .ts-control:focus-within {
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.5);
            border-color: #10b981;
            background-color: #fff;
        }
        .ts-wrapper.has-focus .ts-control {
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.5);
            border-color: #10b981;
        }
        .ts-wrapper .ts-dropdown {
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            font-size: 0.875rem;
            z-index: 9999;
            background-color: #fff;
        }
        .ts-wrapper .ts-dropdown .active {
            background-color: #ecfdf5;
            color: #065f46;
        }
        .ts-wrapper .ts-dropdown .option {
            padding: 0.5rem 0.75rem;
        }
        .ts-wrapper .ts-control .item {
            font-weight: 600;
        }
        .ts-wrapper .ts-control .clear-button {
            opacity: 0.5;
        }
        .ts-wrapper .ts-control .clear-button:hover {
            opacity: 1;
        }
    </style>
</x-app-layout>
