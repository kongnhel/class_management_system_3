<x-app-layout>
    {{-- Pattern Detection PHP --}}
    @php
        // Group schedules by day
        $byDay = $courseOffering->schedules->groupBy('day_of_week');

        $weekdayDays = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
        $weekendDays = ['Saturday','Sunday'];

        // Check weekday pattern (all 5 days identical)
        $firstWeekday = $byDay->get('Monday');
        $isWeekdayPattern = $firstWeekday && $firstWeekday->count() > 0;

        if ($isWeekdayPattern) {
            $template = $firstWeekday->map(fn($s) => [
                'start_time' => \Carbon\Carbon::parse($s->start_time)->format('H:i'),
                'end_time' => \Carbon\Carbon::parse($s->end_time)->format('H:i'),
                'room_id' => $s->room_id,
            ])->values()->all();

            foreach ($weekdayDays as $day) {
                $daySchedules = $byDay->get($day);
                if (!$daySchedules || $daySchedules->count() !== count($template)) {
                    $isWeekdayPattern = false;
                    break;
                }
                $dayTemplate = $daySchedules->map(fn($s) => [
                    'start_time' => \Carbon\Carbon::parse($s->start_time)->format('H:i'),
                    'end_time' => \Carbon\Carbon::parse($s->end_time)->format('H:i'),
                    'room_id' => $s->room_id,
                ])->values()->all();

                if ($dayTemplate !== $template) {
                    $isWeekdayPattern = false;
                    break;
                }
            }
        }

        // Check weekend pattern (both days identical)
        $firstWeekend = $byDay->get('Saturday');
        $isWeekendPattern = $firstWeekend && $firstWeekend->count() > 0;

        if ($isWeekendPattern) {
            $weekendTemplate = $firstWeekend->map(fn($s) => [
                'start_time' => \Carbon\Carbon::parse($s->start_time)->format('H:i'),
                'end_time' => \Carbon\Carbon::parse($s->end_time)->format('H:i'),
                'room_id' => $s->room_id,
            ])->values()->all();

            foreach ($weekendDays as $day) {
                $daySchedules = $byDay->get($day);
                if (!$daySchedules || $daySchedules->count() !== count($weekendTemplate)) {
                    $isWeekendPattern = false;
                    break;
                }
                $dayTemplate = $daySchedules->map(fn($s) => [
                    'start_time' => \Carbon\Carbon::parse($s->start_time)->format('H:i'),
                    'end_time' => \Carbon\Carbon::parse($s->end_time)->format('H:i'),
                    'room_id' => $s->room_id,
                ])->values()->all();

                if ($dayTemplate !== $weekendTemplate) {
                    $isWeekendPattern = false;
                    break;
                }
            }
        }

        $detectedType = $isWeekdayPattern ? 'weekday' : ($isWeekendPattern ? 'weekend' : 'custom');
    @endphp

    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-16 pt-10">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.manage-course-offerings') }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="fas fa-arrow-left text-white"></i>
                    </a>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/20 flex items-center justify-center">
                            <i class="fas fa-edit text-amber-300 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight">{{ __('កែសម្រួលការផ្តល់ជូនមុខវិជ្ជា') }}</h2>
                            <p class="text-slate-400 mt-1 text-sm">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 pb-12 relative z-10">
            @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 text-sm">{{ __('មានបញ្ហា!') }}</p>
                        <ul class="text-red-600 text-xs mt-1 space-y-0.5">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xs"></i></button>
                </div>
            </div>
            @endif

            <form action="{{ route('admin.course-offerings.update', $courseOffering->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Section 1: Basic Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <span class="text-emerald-600 font-bold text-sm">1</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('ព័ត៌មានមូលដ្ឋាន') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('មុខវិជ្ជាមិនអាចផ្លាស់ប្តូរបានទេនៅពេលកែសម្រួល') }}</p>
                        </div>
                    </div>
                    <div>
                        <label for="course_id" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('មុខវិជ្ជា') }}</label>
                        <input type="text" value="{{ $selectedCourse->title_km ?? $selectedCourse->title }}" class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm text-gray-600" readonly>
                        <input type="hidden" name="course_id" value="{{ $courseOffering->course_id }}">
                    </div>
                </div>

                {{-- Section 2: Target Programs --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <span class="text-emerald-600 font-bold text-sm">2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ __('កម្មវិធីសិក្សា និងជំនាន់') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('កំណត់ជំនាញ និងជំនាន់ដែលគោលដៅ') }}</p>
                            </div>
                        </div>
                        <button type="button" id="add-program" class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-sm font-bold hover:bg-emerald-100 transition-colors">
                            <i class="fas fa-plus text-xs"></i> <span>{{ __('បន្ថែម') }}</span>
                        </button>
                    </div>
                    <div id="programs-container" class="space-y-3"></div>
                    @error('target_programs') <p class="text-red-500 text-xs mt-2 italic">* {{ $message }}</p> @enderror
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
                                <select id="lecturer_user_id" name="lecturer_user_id" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm" required>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" {{ $courseOffering->lecturer_user_id == $lecturer->id ? 'selected' : '' }}>{{ $lecturer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="academic_year" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ឆ្នាំសិក្សា') }} <span class="text-red-500">*</span></label>
                                    <select id="academic_year" name="academic_year" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                                        <option value="">{{ __('ជ្រើសរើស') }}</option>
                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year->name }}" data-start="{{ \Carbon\Carbon::parse($year->start_date)->format('Y-m-d') }}" data-end="{{ \Carbon\Carbon::parse($year->end_date)->format('Y-m-d') }}" {{ $courseOffering->academic_year == $year->name ? 'selected' : '' }}>
                                                {{ $year->name }} {{ $year->is_current ? '('.__('បច្ចុប្បន្ន').')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="semester" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ឆមាស') }} <span class="text-red-500">*</span></label>
                                    <select id="semester" name="semester" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                                        <option value="ឆមាសទី១" {{ $courseOffering->semester == 'ឆមាសទី១' ? 'selected' : '' }}>{{ __('ឆមាសទី១') }}</option>
                                        <option value="ឆមាសទី២" {{ $courseOffering->semester == 'ឆមាសទី២' ? 'selected' : '' }}>{{ __('ឆមាសទី២') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="capacity" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ចំនួននិស្សិតអតិបរមា') }} <span class="text-red-500">*</span></label>
                                <input type="number" id="capacity" name="capacity" value="{{ $courseOffering->capacity }}" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="start_date" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('កាលបរិច្ឆេទចាប់ផ្តើម') }} <span class="text-red-500">*</span></label>
                                    <input type="date" id="start_date" name="start_date" value="{{ \Carbon\Carbon::parse($courseOffering->start_date)->format('Y-m-d') }}" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('កាលបរិច្ឆេទបញ្ចប់') }} <span class="text-red-500">*</span></label>
                                    <input type="date" id="end_date" name="end_date" value="{{ \Carbon\Carbon::parse($courseOffering->end_date)->format('Y-m-d') }}" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Schedule Type Selector --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('ជ្រើសរើសបែងប្រាក់វិភាគ') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="schedule-type-card p-4 border-2 rounded-xl cursor-pointer transition-all
                                {{ old('schedule_type', $detectedType) === 'custom' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}"
                                for="schedule_type_custom">
                                <input type="radio" name="schedule_type" value="custom" id="schedule_type_custom" class="sr-only"
                                    {{ old('schedule_type', $detectedType) === 'custom' ? 'checked' : '' }}>
                                <div class="text-center">
                                    <i class="fas fa-calendar-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="font-bold text-gray-900">{{ __('កំណត់ថ្ងៃខ្លី (Custom)') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ __('ជ្រើសរើសថ្ងៃមួយចំនួន') }}</p>
                                </div>
                            </label>

                            <label class="schedule-type-card p-4 border-2 rounded-xl cursor-pointer transition-all
                                {{ old('schedule_type', $detectedType) === 'weekday' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}"
                                for="schedule_type_weekday">
                                <input type="radio" name="schedule_type" value="weekday" id="schedule_type_weekday" class="sr-only"
                                    {{ old('schedule_type', $detectedType) === 'weekday' ? 'checked' : '' }}>
                                <div class="text-center">
                                    <i class="fas fa-calendar-week text-3xl text-blue-500 mb-2"></i>
                                    <p class="font-bold text-gray-900">{{ __('ចន្ទ-សុក្រ (Mon-Fri)') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ __('បែងប្រាក់វេនប្រចាំថ្ងៃ') }}</p>
                                </div>
                            </label>

                            <label class="schedule-type-card p-4 border-2 rounded-xl cursor-pointer transition-all
                                {{ old('schedule_type', $detectedType) === 'weekend' ? 'border-amber-500 bg-amber-50' : 'border-gray-200 hover:border-gray-300' }}"
                                for="schedule_type_weekend">
                                <input type="radio" name="schedule_type" value="weekend" id="schedule_type_weekend" class="sr-only"
                                    {{ old('schedule_type', $detectedType) === 'weekend' ? 'checked' : '' }}>
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
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                                        <span class="text-amber-600 font-bold text-sm">4</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ __('កាលវិភាគសិក្សា') }}</h3>
                                        <p class="text-xs text-gray-500">{{ __('កំណត់ថ្ងៃ ម៉ោង និងបន្ទប់សិក្សា') }}</p>
                                    </div>
                                </div>
                                <button type="button" id="add-schedule-btn" class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-sm font-bold hover:bg-emerald-100 transition-colors">
                                    <i class="fas fa-plus text-xs"></i> <span>{{ __('បន្ថែម') }}</span>
                                </button>
                            </div>
                            <div id="schedules-container" class="space-y-3">
                                @foreach ($courseOffering->schedules as $index => $schedule)
                                    <div class="schedule-item group bg-gray-50 p-4 rounded-xl border border-gray-200">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2 text-sm font-bold text-emerald-600 session-label">
                                                <i class="fas fa-clock text-xs"></i>
                                                <span>Session {{ $index + 1 }}</span>
                                            </div>
                                            <button type="button" class="text-gray-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100" onclick="removeRow(this)">
                                                <i class="fas fa-times-circle text-sm"></i>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            <div class="col-span-2 md:col-span-1">
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ថ្ងៃ') }}</label>
                                                <select name="schedules[{{ $index }}][day_of_week]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                                                    @php $khmerDays = ['Monday' => 'ច័ន្ទ', 'Tuesday' => 'អង្គារ', 'Wednesday' => 'ពុធ', 'Thursday' => 'ព្រហស្បតិ៍', 'Friday' => 'សុក្រ', 'Saturday' => 'សៅរ៍', 'Sunday' => 'អាទិត្យ']; @endphp
                                                    @foreach ($khmerDays as $en => $kh)
                                                        <option value="{{ $en }}" {{ $schedule->day_of_week == $en ? 'selected' : '' }}>{{ $kh }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-2 md:col-span-1">
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('បន្ទប់') }}</label>
                                                <select name="schedules[{{ $index }}][room_id]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                                                    @foreach($rooms as $room)
                                                        <option value="{{ $room->id }}" {{ $schedule->room_id == $room->id ? 'selected' : '' }}>{{ $room->room_number }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ចាប់ផ្តើម') }}</label>
                                                <input type="time" name="schedules[{{ $index }}][start_time]" value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('បញ្ចប់') }}</label>
                                                <input type="time" name="schedules[{{ $index }}][end_time]" value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
                            <i class="fas fa-save"></i> <span>{{ __('រក្សាទុកការកែប្រែ') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
        document.addEventListener('DOMContentLoaded', function () {
            const allPrograms = {!! json_encode($programs) !!};
            const existingPrograms = {!! json_encode($courseOffering->targetPrograms) !!};
            const rooms = {!! json_encode($rooms->map(fn($r) => ['id' => $r->id, 'room_number' => $r->room_number])) !!};

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

            const programsContainer = document.getElementById('programs-container');
            const addProgramBtn = document.getElementById('add-program');
            let programIndex = 0;

            function addProgramRow(data = {}) {
                const rowId = `program-row-${programIndex}`;
                const div = document.createElement('div');
                div.className = 'flex items-center gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200';
                div.id = rowId;

                let optionsHtml = `<option value="">{{ __('ជ្រើសរើសជំនាញ') }}</option>`;
                allPrograms.forEach(p => {
                    const pId = data.pivot ? data.id : data.program_id;
                    const selected = (pId == p.id) ? 'selected' : '';
                    optionsHtml += `<option value="${p.id}" ${selected}>${p.name_km ?? p.name}</option>`;
                });
                const genValue = data.pivot ? data.pivot.generation : (data.generation || '');

                div.innerHTML = `
                    <div class="flex-grow grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ជំនាញ') }}</label>
                            <select name="target_programs[${programIndex}][program_id]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>${optionsHtml}</select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ជំនាន់') }}</label>
                            <input type="text" name="target_programs[${programIndex}][generation]" value="${genValue}" placeholder="{{ __('ឧ. 16') }}" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('${rowId}').remove()" class="text-gray-400 hover:text-red-500 transition-colors mt-5">
                        <i class="fas fa-times-circle text-sm"></i>
                    </button>
                `;
                programsContainer.appendChild(div);
                programIndex++;
            }

            addProgramBtn.addEventListener('click', () => addProgramRow());

            if (existingPrograms && existingPrograms.length > 0) {
                existingPrograms.forEach(p => addProgramRow(p));
            } else {
                const oldPrograms = {!! json_encode(old('target_programs', [])) !!};
                if (Object.keys(oldPrograms).length > 0) {
                    Object.values(oldPrograms).forEach(p => addProgramRow(p));
                } else {
                    addProgramRow();
                }
            }

            // ──────────────────────────────────────────────
            // Schedule Pattern Selection & Session Builder
            // ──────────────────────────────────────────────
            const scheduleTypeRadios = document.querySelectorAll('input[name="schedule_type"]');
            const weekdayForm = document.getElementById('weekday-pattern-form');
            const weekendForm = document.getElementById('weekend-pattern-form');
            const customForm = document.getElementById('custom-schedule-form');

            const detectedType = '{{ $detectedType }}';
            const weekdayTemplate = {!! json_encode(isset($template) ? $template : []) !!};
            const weekendTemplate = {!! json_encode(isset($weekendTemplate) ? $weekendTemplate : []) !!};

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

            // Pre-fill forms based on detected pattern
            if (detectedType === 'weekday' && weekdayTemplate.length > 0) {
                document.querySelector('input[value="weekday"]').checked = true;
                toggleForms();
                weekdayTemplate.forEach((session, i) => {
                    const row = createSessionRow('weekday-sessions-container', 'weekday_sessions', rooms);
                    row.querySelector('[name$="[start_time]"]').value = session.start_time;
                    row.querySelector('[name$="[end_time]"]').value = session.end_time;
                    row.querySelector('[name$="[room_id]"]').value = session.room_id;
                });
            } else if (detectedType === 'weekend' && weekendTemplate.length > 0) {
                document.querySelector('input[value="weekend"]').checked = true;
                toggleForms();
                weekendTemplate.forEach((session, i) => {
                    const row = createSessionRow('weekend-sessions-container', 'weekend_sessions', rooms);
                    row.querySelector('[name$="[start_time]"]').value = session.start_time;
                    row.querySelector('[name$="[end_time]"]').value = session.end_time;
                    row.querySelector('[name$="[room_id]"]').value = session.room_id;
                });
            } else {
                // Custom - existing schedules already rendered in Blade
            }

            // Load old input for pattern forms
            const oldWeekday = {!! json_encode(old('weekday_sessions', [])) !!};
            const oldWeekend = {!! json_encode(old('weekend_sessions', [])) !!};

            if (Object.keys(oldWeekday).length > 0) {
                Object.values(oldWeekday).forEach(s => createSessionRow('weekday-sessions-container', 'weekday_sessions', rooms, s));
            } else if (detectedType !== 'weekday' || weekdayTemplate.length === 0) {
                createSessionRow('weekday-sessions-container', 'weekday_sessions', rooms);
            }

            if (Object.keys(oldWeekend).length > 0) {
                Object.values(oldWeekend).forEach(s => createSessionRow('weekend-sessions-container', 'weekend_sessions', rooms, s));
            } else if (detectedType !== 'weekend' || weekendTemplate.length === 0) {
                createSessionRow('weekend-sessions-container', 'weekend_sessions', rooms);
            }

            // Re-apply toggleForms to disable inputs in hidden forms after session rows are created
            toggleForms();

            // Custom schedule form (existing)
            const addScheduleBtn = document.getElementById('add-schedule-btn');
            const schedulesContainer = document.getElementById('schedules-container');
            const khmerDays = {'Monday': 'ច័ន្ទ', 'Tuesday': 'អង្គារ', 'Wednesday': 'ពុធ', 'Thursday': 'ព្រហស្បតិ៍', 'Friday': 'សុក្រ', 'Saturday': 'សៅរ៍', 'Sunday': 'អាទិត្យ'};

            addScheduleBtn.addEventListener('click', function() {
                const index = Date.now();
                const sessionCount = document.querySelectorAll('.schedule-item').length + 1;
                const row = document.createElement('div');
                row.className = 'schedule-item group bg-gray-50 p-4 rounded-xl border border-gray-200';
                let roomOptions = rooms.map(r => `<option value="${r.id}">${r.room_number}</option>`).join('');
                let dayOptions = Object.keys(khmerDays).map(k => `<option value="${k}">${khmerDays[k]}</option>`).join('');

                row.innerHTML = `
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2 text-sm font-bold text-emerald-600 session-label">
                            <i class="fas fa-clock text-xs"></i>
                            <span>Session ${sessionCount}</span>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100" onclick="removeRow(this)">
                            <i class="fas fa-times-circle text-sm"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ថ្ងៃ') }}</label>
                            <select name="schedules[${index}][day_of_week]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>${dayOptions}</select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('បន្ទប់') }}</label>
                            <select name="schedules[${index}][room_id]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>${roomOptions}</select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ចាប់ផ្តើម') }}</label>
                            <input type="time" name="schedules[${index}][start_time]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('បញ្ចប់') }}</label>
                            <input type="time" name="schedules[${index}][end_time]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                        </div>
                    </div>
                `;
                schedulesContainer.appendChild(row);
            });

            function removeRow(btn) {
                const row = btn.closest('.schedule-item, [id^="program-row-"]');
                row.style.opacity = '0';
                row.style.transform = 'scale(0.95)';
                row.style.transition = 'all 0.2s ease';
                setTimeout(() => {
                    row.remove();
                    updateSessionLabels();
                }, 200);
            }

            function updateSessionLabels() {
                document.querySelectorAll('.session-label').forEach((label, i) => {
                    label.querySelector('span').textContent = `Session ${i + 1}`;
                });
            }
        });
    </script>
</x-app-layout>
