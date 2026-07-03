<x-app-layout>
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
            {{-- Toast --}}
            @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
                class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-emerald-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 text-sm">{{ __('ជោគជ័យ!') }}</p>
                        <p class="text-gray-500 text-xs mt-0.5">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xs"></i></button>
                </div>
            </div>
            @endif

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
                            <p class="text-xs text-gray-500">{{ __('ជ្រើសរើសមុខវិជ្ជាសម្រាប់ការផ្តល់ជូន') }}</p>
                        </div>
                    </div>
                    <div>
                        <label for="course_id" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('មុខវិជ្ជា') }} <span class="text-red-500">*</span></label>
                        <select id="course_id" name="course_id" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm" required>
                            <option value="">{{ __('ជ្រើសរើសមុខវិជ្ជា') }}</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" data-programs='@json($course->programs)' data-generation="{{ $course->generation }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title_km }} (Gen: {{ $course->generation ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
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
                                <select id="lecturer_user_id" name="lecturer_user_id" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm" required>
                                    <option value="">{{ __('ជ្រើសរើសសាស្ត្រាចារ្យ') }}</option>
                                    @foreach ($professors as $professor)
                                        <option value="{{ $professor->id }}" {{ old('lecturer_user_id') == $professor->id ? 'selected' : '' }}>{{ $professor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="academic_year" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ឆ្នាំសិក្សា') }} <span class="text-red-500">*</span></label>
                                    <select name="academic_year" id="academic_year" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
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
                                    <select name="semester" id="semester" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                                        <option value="" disabled {{ old('semester') ? '' : 'selected' }}>{{ __('ជ្រើសរើស') }}</option>
                                        <option value="ឆមាសទី១" {{ old('semester') == 'ឆមាសទី១' ? 'selected' : '' }}>{{ __('ឆមាសទី១') }}</option>
                                        <option value="ឆមាសទី២" {{ old('semester') == 'ឆមាសទី២' ? 'selected' : '' }}>{{ __('ឆមាសទី២') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="capacity" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ចំនួននិស្សិតអតិបរមា') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="capacity" id="capacity" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" value="{{ old('capacity') }}" placeholder="ឧទាហរណ៍: ៣០" required>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="start_date" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('កាលបរិច្ឆេទចាប់ផ្តើម') }} <span class="text-red-500">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" value="{{ old('start_date') }}" required>
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('កាលបរិច្ឆេទបញ្ចប់') }} <span class="text-red-500">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" value="{{ old('end_date') }}" required>
                                </div>
                            </div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_open_for_self_enrollment" value="1" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" {{ old('is_open_for_self_enrollment') ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700">{{ __('អនុញ្ញាតឱ្យសិស្សចុះឈ្មោះដោយខ្លួនឯង') }}</span>
                            </label>
                        </div>
                    </div>

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
            const courseSelect = document.getElementById('course_id');
            const programsContainer = document.getElementById('programs-container');
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

            courseSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (!this.value) {
                    programsContainer.innerHTML = `<div id="program-placeholder" class="text-center py-8 text-gray-400 rounded-xl border-2 border-dashed border-gray-200"><i class="fas fa-graduation-cap text-3xl mb-3 block text-gray-300"></i><p class="text-sm italic">{{ __('សូមជ្រើសរើសមុខវិជ្ជាដើម្បីបង្ហាញកម្មវិធីសិក្សា និងជំនាន់ដោយស្វ័យប្រវត្តិ') }}</p></div>`;
                    return;
                }
                try {
                    const programsData = JSON.parse(selectedOption.getAttribute('data-programs') || '[]');
                    const courseGen = selectedOption.getAttribute('data-generation') || '';
                    programsContainer.innerHTML = '';
                    if (programsData.length === 0) {
                        programsContainer.innerHTML = `<div class="text-center py-6 text-gray-400 rounded-xl border-2 border-dashed border-gray-200"><p class="text-sm">{{ __('មិនមានកម្មវិធីសិក្សាសម្រាប់មុខវិជ្ជានេះទេ') }}</p></div>`;
                        return;
                    }
                    programsData.forEach((prog, index) => {
                        const row = document.createElement('div');
                        row.className = 'flex items-center gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200';
                        row.innerHTML = `
                            <div class="flex-grow grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('កម្មវិធីសិក្សា') }}</label>
                                    <div class="w-full py-2 px-3 bg-white rounded-xl text-sm font-semibold text-gray-700 border border-gray-200">${prog.name_km || prog.name}</div>
                                    <input type="hidden" name="target_programs[${index}][program_id]" value="${prog.id}">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ជំនាន់') }}</label>
                                    <div class="w-full py-2 px-3 bg-emerald-50 rounded-xl text-sm font-bold text-emerald-700 border border-emerald-100">${courseGen ? 'ជំនាន់ទី ' + courseGen : '{{ __("មិនទាន់កំណត់") }}'}</div>
                                    <input type="hidden" name="target_programs[${index}][generation]" value="${courseGen}">
                                </div>
                            </div>
                        `;
                        programsContainer.appendChild(row);
                    });
                } catch (e) {
                    programsContainer.innerHTML = `<div class="text-center py-6 text-red-400"><i class="fas fa-exclamation-triangle mb-2 block"></i><p class="text-sm">{{ __('មានបញ្ហាក្នុងការទាញយកទិន្នន័យ') }}</p></div>`;
                }
            });
            if (courseSelect.value) courseSelect.dispatchEvent(new Event('change'));

            const scheduleContainer = document.getElementById('schedules-container');
            const addScheduleBtn = document.getElementById('add-schedule');
            const rooms = {!! json_encode($rooms) !!};
            let scheduleIndex = 0;

            function addScheduleRow(initialData = {}) {
                const scheduleDiv = document.createElement('div');
                scheduleDiv.className = 'schedule-row group bg-gray-50 p-4 rounded-xl border border-gray-200';
                const currentSessions = document.querySelectorAll('.schedule-row').length + 1;
                const roomOptions = rooms.map(room => `<option value="${room.id}" ${initialData.room_id == room.id ? 'selected' : ''}>${room.room_number}</option>`).join('');
                scheduleDiv.innerHTML = `
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2 text-sm font-bold text-emerald-600">
                            <i class="fas fa-clock text-xs"></i>
                            <span>Session ${currentSessions}</span>
                        </div>
                        <button type="button" class="remove-schedule text-gray-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                            <i class="fas fa-times-circle text-sm"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ថ្ងៃ') }}</label>
                            <select name="schedules[${scheduleIndex}][day_of_week]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                                <option value="">{{ __('រើសថ្ងៃ') }}</option>
                                <option value="Monday" ${initialData.day_of_week === 'Monday' ? 'selected' : ''}>{{ __('ច័ន្ទ') }}</option>
                                <option value="Tuesday" ${initialData.day_of_week === 'Tuesday' ? 'selected' : ''}>{{ __('អង្គារ') }}</option>
                                <option value="Wednesday" ${initialData.day_of_week === 'Wednesday' ? 'selected' : ''}>{{ __('ពុធ') }}</option>
                                <option value="Thursday" ${initialData.day_of_week === 'Thursday' ? 'selected' : ''}>{{ __('ព្រហស្បតិ៍') }}</option>
                                <option value="Friday" ${initialData.day_of_week === 'Friday' ? 'selected' : ''}>{{ __('សុក្រ') }}</option>
                                <option value="Saturday" ${initialData.day_of_week === 'Saturday' ? 'selected' : ''}>{{ __('សៅរ៍') }}</option>
                                <option value="Sunday" ${initialData.day_of_week === 'Sunday' ? 'selected' : ''}>{{ __('អាទិត្យ') }}</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('បន្ទប់') }}</label>
                            <select name="schedules[${scheduleIndex}][room_id]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" required>
                                <option value="">{{ __('រើសបន្ទប់') }}</option>
                                ${roomOptions}
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ចាប់ផ្តើម') }}</label>
                            <input type="time" name="schedules[${scheduleIndex}][start_time]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" value="${initialData.start_time || ''}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('បញ្ចប់') }}</label>
                            <input type="time" name="schedules[${scheduleIndex}][end_time]" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm" value="${initialData.end_time || ''}" required>
                        </div>
                    </div>
                `;
                scheduleContainer.appendChild(scheduleDiv);
                scheduleDiv.querySelector('.remove-schedule').addEventListener('click', function() { scheduleDiv.remove(); updateSessionNumbers(); });
                scheduleIndex++;
            }

            function updateSessionNumbers() {
                document.querySelectorAll('.schedule-row').forEach((row, i) => {
                    const label = row.querySelector('.flex.items-center span');
                    if (label) label.textContent = `Session ${i + 1}`;
                });
            }

            addScheduleBtn.addEventListener('click', () => addScheduleRow());
            const oldSchedules = {!! json_encode(old('schedules', [])) !!};
            if (Object.keys(oldSchedules).length > 0) {
                Object.values(oldSchedules).forEach(s => addScheduleRow(s));
            } else {
                addScheduleRow();
            }
        });
    </script>
</x-app-layout>
