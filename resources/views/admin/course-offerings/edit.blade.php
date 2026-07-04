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
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_open_for_self_enrollment" value="1" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" {{ $courseOffering->is_open_for_self_enrollment ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700">{{ __('អនុញ្ញាតឱ្យសិស្សចុះឈ្មោះដោយខ្លួនឯង') }}</span>
                            </label>
                        </div>
                    </div>

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
    </script>
</x-app-layout>
