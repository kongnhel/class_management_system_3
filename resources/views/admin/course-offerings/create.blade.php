<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">
            {{ __('បង្កើតការផ្តល់ជូនមុខវិជ្ជាថ្មី') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-2xl shadow-gray-200/50 sm:rounded-3xl overflow-hidden">
                
                {{-- Flash Messages --}}
                <div class="p-6 sm:px-12 pt-10">
                    @if (session('success'))
                        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-6 flex items-center shadow-sm">
                            <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-semibold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any() || session('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
                            <div class="flex items-center mb-2">
                                <svg class="h-6 w-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <strong class="font-bold">{{ __('មានបញ្ហា!') }}</strong>
                            </div>
                            <ul class="text-sm list-disc list-inside space-y-1 ml-9">
                                @if(session('error')) <li>{{ session('error') }}</li> @endif
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.store-course-offering') }}" class="p-6 sm:px-12 pb-12">
                    @csrf
                    <div class="space-y-12">
                        
                        {{-- 1. Basic Info & Course Selection --}}
                        <section>
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 font-bold text-sm">1</span>
                                <h3 class="text-xl font-bold text-gray-800">{{ __('ព័ត៌មានមូលដ្ឋាន') }}</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                {{-- Course Selection --}}
                                <div class="space-y-2">
                                    <label for="course_id" class="block text-sm font-semibold text-gray-700 uppercase tracking-wider">{{ __('មុខវិជ្ជា') }}<span class="text-red-500">*</span></label>
                                    <select id="course_id" name="course_id" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition duration-200" required>
                                        <option value="">{{ __('ជ្រើសរើសមុខវិជ្ជា') }}</option>
                                        @foreach ($courses as $course)
                                            {{-- <option value="{{ $course->id }}" 
                                                    data-programs="{{ json_encode($course->programs) }}"
                                                    data-generation="{{ $course->generation }}"
                                                    {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->title_km }} (Gen: {{ $course->generation ?? 'N/A' }})
                                            </option> --}}
                                            {{-- Replace the option tag inside your @foreach --}}
<option value="{{ $course->id }}" 
        data-programs='@json($course->programs)'
        data-generation="{{ $course->generation }}"
        {{ old('course_id') == $course->id ? 'selected' : '' }}>
    {{ $course->title_km }} (Gen: {{ $course->generation ?? 'N/A' }})
</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </section>

                        {{-- 2. Target Programs (NOW FULLY AUTO-SELECTED) --}}
                        <section class="bg-blue-50/50 p-8 rounded-3xl border border-blue-100 shadow-inner">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-3">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold text-sm">2</span>
                                    <h3 class="text-xl font-bold text-gray-800">{{ __('កម្មវិធីសិក្សា និង ជំនាន់ (Target Audience)') }}</h3>
                                </div>
                            </div>

                            <div id="programs-container" class="space-y-3">
                                <div id="program-placeholder" class="text-center py-6 text-gray-400 italic">
                                    {{ __('សូមជ្រើសរើសមុខវិជ្ជាដើម្បីបង្ហាញកម្មវិធីសិក្សា និងជំនាន់ដោយស្វ័យប្រវត្តិ') }}
                                </div>
                            </div>
                        </section>

                        {{-- 3. Offering Details --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                            <section class="bg-gray-50/80 p-8 rounded-3xl border border-gray-100 shadow-inner">
                                <div class="flex items-center space-x-3 mb-8">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 font-bold text-sm">3</span>
                                    <h3 class="text-xl font-bold text-gray-800">{{ __('ព័ត៌មានការផ្តល់ជូន') }}</h3>
                                </div>
                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        <label for="lecturer_user_id" class="block text-sm font-medium text-gray-700">{{ __('សាស្រ្តាចារ្យ') }}</label>
                                        <select id="lecturer_user_id" name="lecturer_user_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200" required>
                                            <option value="">{{ __('ជ្រើសរើសសាស្រ្តាចារ្យ') }}</option>
                                            @foreach ($professors as $professor)
                                                <option value="{{ $professor->id }}" {{ old('lecturer_user_id') == $professor->id ? 'selected' : '' }}>{{ $professor->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label for="academic_year" class="block text-sm font-medium text-gray-700">{{ __('ឆ្នាំសិក្សា') }}</label>
                                            <select name="academic_year" id="academic_year" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                                                <option value="">{{ __('ជ្រើសរើសឆ្នាំសិក្សា') }}</option>
                                                @foreach ($academicYears as $year)
                                                    <option value="{{ $year->name }}" 
                                                            data-start="{{ \Carbon\Carbon::parse($year->start_date)->format('Y-m-d') }}" 
                                                            data-end="{{ \Carbon\Carbon::parse($year->end_date)->format('Y-m-d') }}"
                                                            {{ old('academic_year') == $year->name ? 'selected' : '' }}>
                                                        {{ $year->name }} {{ $year->is_current ? '('.__('បច្ចុប្បន្ន').')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="space-y-2">
                                            <label for="semester" class="block text-sm font-medium text-gray-700">{{ __('ឆមាស') }}</label>
                                            <select name="semester" id="semester" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                                                <option value="" disabled {{ old('semester') ? '' : 'selected' }}>{{ __('ជ្រើសរើស') }}</option>
                                                <option value="ឆមាសទី១" {{ old('semester') == 'ឆមាសទី១' ? 'selected' : '' }}>{{ __('ឆមាសទី១') }}</option>
                                                <option value="ឆមាសទី២" {{ old('semester') == 'ឆមាសទី២' ? 'selected' : '' }}>{{ __('ឆមាសទី២') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="capacity" class="block text-sm font-medium text-gray-700">{{ __('ចំនួននិស្សិតអតិបរមា') }}</label>
                                        <input type="number" name="capacity" id="capacity" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" value="{{ old('capacity') }}" placeholder="ឧទាហរណ៍: ៣០" required>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 pt-2">
                                        <div class="space-y-2">
                                            <label for="start_date" class="block text-sm font-medium text-gray-700 text-green-600">{{ __('កាលបរិច្ឆេទចាប់ផ្តើម') }}</label>
                                            <input type="date" name="start_date" id="start_date" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" value="{{ old('start_date') }}" required>
                                        </div>
                                        <div class="space-y-2">
                                            <label for="end_date" class="block text-sm font-medium text-gray-700 text-red-600">{{ __('កាលបរិច្ឆេទបញ្ចប់') }}</label>
                                            <input type="date" name="end_date" id="end_date" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" value="{{ old('end_date') }}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="pt-2">
                                        <label class="flex items-center space-x-3">
                                            <input type="checkbox" name="is_open_for_self_enrollment" value="1" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50" {{ old('is_open_for_self_enrollment') ? 'checked' : '' }}>
                                            <span class="text-sm font-medium text-gray-700">{{ __('អនុញ្ញាតឱ្យសិស្សចុះឈ្មោះដោយខ្លួនឯង') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </section>

                            {{-- 4. Schedules --}}
                            <section class="border-2 border-dashed border-gray-200 p-8 rounded-3xl flex flex-col">
                                <div class="flex items-center justify-between mb-8">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 font-bold text-sm">4</span>
                                        <h3 class="text-xl font-bold text-gray-800">{{ __('កាលវិភាគសិក្សា') }}</h3>
                                    </div>
                                </div>
                                
                                <div id="schedules-container" class="space-y-4 flex-grow">
                                    {{-- Schedule Rows --}}
                                </div>

                                <div class="mt-8 pt-6 border-t border-gray-100">
                                    <button type="button" id="add-schedule" class="group flex items-center space-x-2 text-green-600 font-bold hover:text-green-700 transition duration-200">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-50 group-hover:bg-green-100 transition duration-200">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        </span>
                                        <span>{{ __('បន្ថែមម៉ោងសិក្សាថ្មី') }}</span>
                                    </button>
                                </div>
                            </section>
                        </div>

                        <div class="pt-10 flex flex-col sm:flex-row items-center justify-between border-t border-gray-100 gap-4">
                            <a href="{{ route('admin.manage-course-offerings') }}" class="w-full sm:w-auto text-center px-10 py-3.5 text-gray-500 font-bold rounded-2xl hover:bg-gray-100 transition duration-200">
                                {{ __('បោះបង់') }}
                            </a>
                            
                            <button type="submit" class="w-full sm:w-auto flex items-center justify-center space-x-3 px-12 py-4 bg-green-600 text-white font-bold rounded-2xl shadow-lg shadow-green-200 hover:bg-green-700 hover:-translate-y-0.5 transition duration-200">
                                <span>{{ __('បង្កើតការផ្តល់ជូនមុខវិជ្ជា') }}</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseSelect = document.getElementById('course_id');
            const programsContainer = document.getElementById('programs-container');
            const academicYearSelect = document.getElementById('academic_year');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            // ============================================
            // AUTO-FILL DATES FROM ACADEMIC YEAR
            // ============================================
            academicYearSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const startDate = selectedOption.getAttribute('data-start');
                const endDate = selectedOption.getAttribute('data-end');
                
                if (startDate && endDate) {
                    startDateInput.value = startDate;
                    endDateInput.value = endDate;
                }
            });

            // Auto-fill on page load if a year is already selected
            if (academicYearSelect.value) {
                academicYearSelect.dispatchEvent(new Event('change'));
            }

            // ============================================
            // 1. AUTO-POPULATE PROGRAM & GENERATION
            // ============================================
            // courseSelect.addEventListener('change', function() {
            //     const selectedOption = this.options[this.selectedIndex];
            //     const programsData = JSON.parse(selectedOption.getAttribute('data-programs') || '[]');
            //     const courseGen = selectedOption.getAttribute('data-generation') || '';
                
            //     programsContainer.innerHTML = '';

            //     if (programsData.length === 0) {
            //         programsContainer.innerHTML = `<div class="text-center py-6 text-gray-400 italic">{{ __('មិនមានកម្មវិធីសិក្សាសម្រាប់មុខវិជ្ជានេះទេ') }}</div>`;
            //         return;
            //     }

            //     programsData.forEach((prog, index) => {
            //         const row = document.createElement('div');
            //         row.className = 'flex items-center gap-4 bg-white p-4 rounded-xl border border-blue-100 shadow-sm animate-fadeIn mb-3';
                    
            //         row.innerHTML = `
            //             <div class="flex-grow grid grid-cols-2 gap-4">
            //                 <div>
            //                     <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('កម្មវិធីសិក្សា') }}</label>
            //                     <div class="w-full py-2 px-3 bg-gray-50 rounded-lg text-sm font-semibold text-gray-700 border border-gray-200">
            //                         ${prog.name_km ?? prog.name}
            //                     </div>
            //                     <input type="hidden" name="target_programs[${index}][program_id]" value="${prog.id}">
            //                 </div>
            //                 <div>
            //                     <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('ជំនាន់ (Generation)') }}</label>
            //                     <div class="w-full py-2 px-3 bg-blue-50 rounded-lg text-sm font-bold text-blue-700 border border-blue-200">
            //                         ${courseGen ? 'ជំនាន់ទី ' + courseGen : '{{ __("មិនទាន់កំណត់") }}'}
            //                     </div>
            //                     <input type="hidden" name="target_programs[${index}][generation]" value="${courseGen}">
            //                 </div>
            //             </div>
            //         `;
            //         programsContainer.appendChild(row);
            //     });
            // });
            courseSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    
    // Safety check: if no course is selected, show the placeholder
    if (!this.value) {
        programsContainer.innerHTML = `<div id="program-placeholder" class="text-center py-6 text-gray-400 italic">{{ __('សូមជ្រើសរើសមុខវិជ្ជាដើម្បីបង្ហាញកម្មវិធីសិក្សា និងជំនាន់ដោយស្វ័យប្រវត្តិ') }}</div>`;
        return;
    }

    try {
        const rawData = selectedOption.getAttribute('data-programs');
        console.log("Raw Data:", rawData); // Check your browser console!

        const programsData = JSON.parse(rawData || '[]');
        const courseGen = selectedOption.getAttribute('data-generation') || '';
        
        programsContainer.innerHTML = '';

        if (programsData.length === 0) {
            programsContainer.innerHTML = `<div class="text-center py-6 text-gray-400 italic">{{ __('មិនមានកម្មវិធីសិក្សាសម្រាប់មុខវិជ្ជានេះទេ') }}</div>`;
            return;
        }

        programsData.forEach((prog, index) => {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-4 bg-white p-4 rounded-xl border border-blue-100 shadow-sm animate-fadeIn mb-3';
            
            row.innerHTML = `
                <div class="flex-grow grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('កម្មវិធីសិក្សា') }}</label>
                        <div class="w-full py-2 px-3 bg-gray-50 rounded-lg text-sm font-semibold text-gray-700 border border-gray-200">
                            ${prog.name_km || prog.name}
                        </div>
                        <input type="hidden" name="target_programs[${index}][program_id]" value="${prog.id}">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('ជំនាន់ (Generation)') }}</label>
                        <div class="w-full py-2 px-3 bg-blue-50 rounded-lg text-sm font-bold text-blue-700 border border-blue-200">
                            ${courseGen ? 'ជំនាន់ទី ' + courseGen : '{{ __("មិនទាន់កំណត់") }}'}
                        </div>
                        <input type="hidden" name="target_programs[${index}][generation]" value="${courseGen}">
                    </div>
                </div>
            `;
            programsContainer.appendChild(row);
        });
    } catch (e) {
        console.error("Parsing Error:", e);
        programsContainer.innerHTML = `<div class="text-center py-6 text-red-400">Error loading data. Check console.</div>`;
    }
});

            if (courseSelect.value) courseSelect.dispatchEvent(new Event('change'));

            // ============================================
            // 2. SCHEDULE LOGIC (Kept stable)
            // ============================================
            const scheduleContainer = document.getElementById('schedules-container');
            const addScheduleBtn = document.getElementById('add-schedule');
            const rooms = {!! json_encode($rooms) !!};
            let scheduleIndex = 0;

            function addScheduleRow(initialData = {}) {
                const scheduleDiv = document.createElement('div');
                scheduleDiv.className = 'schedule-row group relative bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:border-green-200 transition duration-200 animate-fadeIn mb-4';
                
                const currentSessions = document.querySelectorAll('.schedule-row').length + 1;
                const roomOptions = rooms.map(room => `<option value="${room.id}" ${initialData.room_id == room.id ? 'selected' : ''}>${room.room_number}</option>`).join('');

                scheduleDiv.innerHTML = `
                    <div class="flex items-center mb-3 text-sm font-bold text-green-600">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path></svg>
                        Session ${currentSessions}
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('ថ្ងៃសិក្សា') }}</label>
                            <select name="schedules[${scheduleIndex}][day_of_week]" class="w-full rounded-xl border-gray-200 text-sm focus:ring-green-500 focus:border-green-500" required>
                                <option value="">{{ __('រើសថ្ងៃ') }}</option>
                                <option value="Monday" ${initialData.day_of_week === 'Monday' ? 'selected' : ''}>{{ __('ថ្ងៃច័ន្ទ') }}</option>
                                <option value="Tuesday" ${initialData.day_of_week === 'Tuesday' ? 'selected' : ''}>{{ __('ថ្ងៃអង្គារ') }}</option>
                                <option value="Wednesday" ${initialData.day_of_week === 'Wednesday' ? 'selected' : ''}>{{ __('ថ្ងៃពុធ') }}</option>
                                <option value="Thursday" ${initialData.day_of_week === 'Thursday' ? 'selected' : ''}>{{ __('ថ្ងៃព្រហស្បតិ៍') }}</option>
                                <option value="Friday" ${initialData.day_of_week === 'Friday' ? 'selected' : ''}>{{ __('ថ្ងៃសុក្រ') }}</option>
                                <option value="Saturday" ${initialData.day_of_week === 'Saturday' ? 'selected' : ''}>{{ __('ថ្ងៃសៅរ៍') }}</option>
                                <option value="Sunday" ${initialData.day_of_week === 'Sunday' ? 'selected' : ''}>{{ __('ថ្ងៃអាទិត្យ') }}</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('បន្ទប់') }}</label>
                            <select name="schedules[${scheduleIndex}][room_id]" class="w-full rounded-xl border-gray-200 text-sm focus:ring-green-500 focus:border-green-500" required>
                                <option value="">{{ __('រើសបន្ទប់') }}</option>
                                ${roomOptions}
                            </select>
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('ចាប់ផ្តើម') }}</label>
                            <input type="time" name="schedules[${scheduleIndex}][start_time]" class="w-full rounded-xl border-gray-200 text-sm" value="${initialData.start_time || ''}" required>
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('បញ្ចប់') }}</label>
                            <input type="time" name="schedules[${scheduleIndex}][end_time]" class="w-full rounded-xl border-gray-200 text-sm" value="${initialData.end_time || ''}" required>
                        </div>
                    </div>
                    <button type="button" class="remove-schedule absolute -top-2 -right-2 bg-white text-gray-300 hover:text-red-500 rounded-full border border-gray-100 p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                `;
                scheduleContainer.appendChild(scheduleDiv);
                
                scheduleDiv.querySelector('.remove-schedule').addEventListener('click', function() { 
                    scheduleDiv.remove();
                    updateSessionNumbers();
                });
                scheduleIndex++;
            }

            function updateSessionNumbers() {
                document.querySelectorAll('.schedule-row').forEach((row, i) => {
                    const label = row.querySelector('.flex.items-center.text-green-600');
                    if (label) label.innerHTML = `<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path></svg> Session ${i + 1}`;
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

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
    </style>
</x-app-layout>