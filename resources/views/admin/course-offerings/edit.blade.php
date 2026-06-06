<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">
            {{ __('កែសម្រួលការផ្តល់ជូនមុខវិជ្ជា') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 font-['Battambang']">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-3xl overflow-hidden">
                
                <div class="p-6 sm:px-12 pt-10">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">
                            <span class="font-semibold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">
                            <strong class="font-bold">{{ __('មានបញ្ហា!') }}</strong>
                            <ul class="mt-2 text-sm list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <form action="{{ route('admin.course-offerings.update', $courseOffering->id) }}" method="POST" class="p-6 sm:px-12 pb-12">
                    @csrf
                    @method('PUT')

                    <div class="space-y-10">
                        
                        {{-- 1. Basic Info --}}
                        <div class="bg-gray-50 p-6 rounded-2xl shadow-inner">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">{{ __('ព័ត៌មានមូលដ្ឋាន') }}</h3>
                            <div class="space-y-6">
                                <div>
                                    <label for="course_id" class="block text-sm font-medium text-gray-700">មុខវិជ្ជា <span class="text-red-500">*</span></label>
                                    <select id="course_id" name="course_id" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-gray-100" required readonly>
                                        <option value="{{ $selectedCourse->id }}" selected>
                                            {{ $selectedCourse->title_km ?? $selectedCourse->title }}
                                        </option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">* {{ __('មិនអាចផ្លាស់ប្តូរមុខវិជ្ជាបានទេនៅពេលកែសម្រួល') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Target Programs (Multiple) --}}
                        <div class="bg-blue-50/50 p-8 rounded-3xl border border-blue-100 shadow-inner">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-3">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold text-sm">2</span>
                                    <h3 class="text-xl font-bold text-gray-800">{{ __('កម្មវិធីសិក្សា និង ជំនាន់ (Target Audience)') }}</h3>
                                </div>
                                <button type="button" id="add-program" class="text-sm bg-white border border-blue-200 text-blue-600 px-4 py-2 rounded-xl font-bold shadow-sm hover:bg-blue-50 transition">
                                    + {{ __('បន្ថែមកម្មវិធីសិក្សា') }}
                                </button>
                            </div>

                            <div id="programs-container" class="space-y-3">
                                {{-- Rows will be populated via JS --}}
                            </div>
                            @error('target_programs') <p class="text-red-500 text-xs mt-2 italic">* {{ $message }}</p> @enderror
                        </div>

                        {{-- 3. Offering Details --}}
                        <div class="bg-gray-50 p-6 rounded-2xl shadow-inner">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">{{ __('ព័ត៌មានការផ្តល់ជូន') }}</h3>
                            <div class="space-y-6">
                                <div>
                                    <label for="lecturer_user_id" class="block text-sm font-medium text-gray-700">សាស្រ្តាចារ្យ <span class="text-red-500">*</span></label>
                                    <select id="lecturer_user_id" name="lecturer_user_id" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                                        @foreach($lecturers as $lecturer)
                                            <option value="{{ $lecturer->id }}" {{ $courseOffering->lecturer_user_id == $lecturer->id ? 'selected' : '' }}>{{ $lecturer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="academic_year" class="block text-sm font-medium text-gray-700">{{ __('ឆ្នាំសិក្សា') }}</label>
                                        <input type="text" id="academic_year" name="academic_year" value="{{ $courseOffering->academic_year }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm" required>
                                    </div>
                                    <div>
                                        <label for="semester" class="block text-sm font-medium text-gray-700">{{ __('ឆមាស') }}</label>
                                        <select id="semester" name="semester" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm" required>
                                            <option value="ឆមាសទី១" {{ $courseOffering->semester == 'ឆមាសទី១' ? 'selected' : '' }}>{{ __('ឆមាសទី១') }}</option>
                                            <option value="ឆមាសទី២" {{ $courseOffering->semester == 'ឆមាសទី២' ? 'selected' : '' }}>{{ __('ឆមាសទី២') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="capacity" class="block text-sm font-medium text-gray-700">ចំនួនអតិបរមានិស្សិត</label>
                                        <input type="number" id="capacity" name="capacity" value="{{ $courseOffering->capacity }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm" required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700">{{ __('កាលបរិច្ឆេទចាប់ផ្តើម') }}</label>
                                        <input type="date" id="start_date" name="start_date" value="{{ \Carbon\Carbon::parse($courseOffering->start_date)->format('Y-m-d') }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm" required>
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700">{{ __('កាលបរិច្ឆេទបញ្ចប់') }}</label>
                                        <input type="date" id="end_date" name="end_date" value="{{ \Carbon\Carbon::parse($courseOffering->end_date)->format('Y-m-d') }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm" required>
                                    </div>
                                </div>
                                
                                <div class="pt-2">
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="is_open_for_self_enrollment" value="1" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300" {{ $courseOffering->is_open_for_self_enrollment ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-gray-700">{{ __('អនុញ្ញាតឱ្យសិស្សចុះឈ្មោះដោយខ្លួនឯង') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Schedules --}}
                        <div class="bg-white p-6 rounded-2xl shadow-inner border border-gray-200">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-800">{{ __('កាលវិភាគសិក្សា') }}</h3>
                                <button type="button" id="add-schedule-btn" class="group flex items-center space-x-2 text-green-600 font-bold hover:text-green-700 transition duration-200">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-50 group-hover:bg-green-100">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    </span>
                                    <span>{{ __('បន្ថែមម៉ោងសិក្សាថ្មី') }}</span>
                                </button>
                            </div>
                            
                            <div id="schedules-container" class="space-y-4">
                                {{-- Pre-load existing schedules --}}
                                @foreach ($courseOffering->schedules as $index => $schedule)
                                    <div class="schedule-item group relative bg-gray-50 p-5 rounded-2xl border border-gray-100 shadow-sm transition duration-200 mb-4">
                                        <div class="flex items-center mb-3 text-sm font-bold text-green-600 session-label">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path></svg>
                                            Session {{ $index + 1 }}
                                        </div>

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            <div class="col-span-2 md:col-span-1">
                                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('ថ្ងៃសិក្សា') }}</label>
                                                <select name="schedules[{{ $index }}][day_of_week]" class="w-full rounded-xl border-gray-200 text-sm focus:ring-green-500" required>
                                                    @php $khmerDays = ['Monday' => 'ច័ន្ទ', 'Tuesday' => 'អង្គារ', 'Wednesday' => 'ពុធ', 'Thursday' => 'ព្រហស្បតិ៍', 'Friday' => 'សុក្រ', 'Saturday' => 'សៅរ៍', 'Sunday' => 'អាទិត្យ']; @endphp
                                                    @foreach ($khmerDays as $en => $kh)
                                                        <option value="{{ $en }}" {{ $schedule->day_of_week == $en ? 'selected' : '' }}>{{ $kh }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-2 md:col-span-1">
                                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('បន្ទប់') }}</label>
                                                <select name="schedules[{{ $index }}][room_id]" class="w-full rounded-xl border-gray-200 text-sm focus:ring-green-500" required>
                                                    @foreach($rooms as $room)
                                                        <option value="{{ $room->id }}" {{ $schedule->room_id == $room->id ? 'selected' : '' }}>{{ $room->room_number }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-1">
                                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('ចាប់ផ្តើម') }}</label>
                                                <input type="time" name="schedules[{{ $index }}][start_time]" value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" class="w-full rounded-xl border-gray-200 text-sm" required>
                                            </div>
                                            <div class="col-span-1">
                                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('បញ្ចប់') }}</label>
                                                <input type="time" name="schedules[{{ $index }}][end_time]" value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" class="w-full rounded-xl border-gray-200 text-sm">
                                            </div>
                                        </div>
                                        <button type="button" class="remove-schedule absolute -top-2 -right-2 bg-white text-gray-300 hover:text-red-500 rounded-full border border-gray-100 shadow-sm p-1 transition-colors duration-200 opacity-0 group-hover:opacity-100" onclick="removeRow(this)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t">
                            <a href="{{ route('admin.manage-course-offerings') }}" class="px-6 py-3 mr-4 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">{{ __('បោះបង់') }}</a>
                            <button type="submit" class="px-8 py-3 bg-green-600 text-white font-bold rounded-xl shadow-lg hover:bg-green-700 transition transform hover:scale-105">
                                {{ __('រក្សាទុកការកែប្រែ') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data passed from Controller
            const allPrograms = {!! json_encode($programs) !!};
            const existingPrograms = {!! json_encode($courseOffering->targetPrograms) !!}; // From pivot table
            const rooms = {!! json_encode($rooms->map(fn($r) => ['id' => $r->id, 'room_number' => $r->room_number])) !!};
            
            // --- 1. Program Logic ---
            const programsContainer = document.getElementById('programs-container');
            const addProgramBtn = document.getElementById('add-program');
            let programIndex = 0;

            function addProgramRow(data = {}) {
                const rowId = `program-row-${programIndex}`;
                const div = document.createElement('div');
                div.className = 'flex items-center gap-4 bg-white p-3 rounded-xl border border-blue-100 shadow-sm animate-fadeIn';
                div.id = rowId;

                // Build Options
                let optionsHtml = `<option value="">{{ __('ជ្រើសរើសជំនាញ') }}</option>`;
                allPrograms.forEach(p => {
                    // Check ID: existing data might have program_id or pivot.program_id (or just id from program model)
                    const pId = data.pivot ? data.id : data.program_id; // If from pivot: program model has ID. If from old input: program_id.
                    const selected = (pId == p.id) ? 'selected' : '';
                    optionsHtml += `<option value="${p.id}" ${selected}>${p.name_km ?? p.name}</option>`;
                });

                // Check Generation value (handle pivot data structure)
                const genValue = data.pivot ? data.pivot.generation : (data.generation || '');

                div.innerHTML = `
                    <div class="flex-grow grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('ជំនាញ (Program)') }}</label>
                            <select name="target_programs[${programIndex}][program_id]" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500" required>
                                ${optionsHtml}
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('ជំនាន់ (Generation)') }}</label>
                            <input type="text" name="target_programs[${programIndex}][generation]" 
                                   value="${genValue}" 
                                   placeholder="{{ __('ជំនាន់ (ឧ. 16)') }}" 
                                   class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500" required>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('${rowId}').remove()" class="mt-5 text-red-400 hover:text-red-600 p-2 rounded-full hover:bg-red-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                `;
                programsContainer.appendChild(div);
                programIndex++;
            }

            addProgramBtn.addEventListener('click', () => addProgramRow());

            // Pre-load Existing Programs
            // Note: existingPrograms contains Program models with pivot data attached
            if (existingPrograms && existingPrograms.length > 0) {
                existingPrograms.forEach(p => addProgramRow(p));
            } else {
                // If old input exists (validation fail), load that instead
                const oldPrograms = {!! json_encode(old('target_programs', [])) !!};
                if (Object.keys(oldPrograms).length > 0) {
                    Object.values(oldPrograms).forEach(p => addProgramRow(p));
                } else {
                    addProgramRow(); // Fallback empty row
                }
            }


            // --- 2. Schedule Logic (Add Button) ---
            const addScheduleBtn = document.getElementById('add-schedule-btn');
            const schedulesContainer = document.getElementById('schedules-container');
            const khmerDays = {'Monday': 'ច័ន្ទ', 'Tuesday': 'អង្គារ', 'Wednesday': 'ពុធ', 'Thursday': 'ព្រហស្បតិ៍', 'Friday': 'សុក្រ', 'Saturday': 'សៅរ៍', 'Sunday': 'អាទិត្យ'};

            addScheduleBtn.addEventListener('click', function() {
                const index = Date.now(); // Unique index
                const sessionCount = document.querySelectorAll('.schedule-item').length + 1;
                
                const row = document.createElement('div');
                row.className = 'schedule-item group relative bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:border-green-200 transition duration-200 mb-4 animate-fadeIn';
                
                let roomOptions = rooms.map(r => `<option value="${r.id}">${r.room_number}</option>`).join('');
                let dayOptions = Object.keys(khmerDays).map(k => `<option value="${k}">${khmerDays[k]}</option>`).join('');

                row.innerHTML = `
                    <div class="flex items-center mb-3 text-sm font-bold text-green-600 session-label">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path></svg>
                        Session ${sessionCount}
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('ថ្ងៃសិក្សា') }}</label>
                            <select name="schedules[${index}][day_of_week]" class="w-full rounded-xl border-gray-200 text-sm focus:ring-green-500" required>${dayOptions}</select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('បន្ទប់') }}</label>
                            <select name="schedules[${index}][room_id]" class="w-full rounded-xl border-gray-200 text-sm focus:ring-green-500" required>${roomOptions}</select>
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('ចាប់ផ្តើម') }}</label>
                            <input type="time" name="schedules[${index}][start_time]" class="w-full rounded-xl border-gray-200 text-sm" required>
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('បញ្ចប់') }}</label>
                            <input type="time" name="schedules[${index}][end_time]" class="w-full rounded-xl border-gray-200 text-sm">
                        </div>
                    </div>
                    <button type="button" class="remove-schedule absolute -top-2 -right-2 bg-white text-gray-300 hover:text-red-500 rounded-full border border-gray-100 shadow-sm p-1 transition-colors duration-200 opacity-0 group-hover:opacity-100" onclick="removeRow(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                `;
                schedulesContainer.appendChild(row);
            });
        });

        // Global functions for inline onclicks
        function removeRow(btn) {
            const row = btn.closest('.schedule-item');
            row.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                row.remove();
                updateSessionLabels();
            }, 200);
        }

        function updateSessionLabels() {
            document.querySelectorAll('.session-label').forEach((label, i) => {
                label.innerHTML = `
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path></svg>
                    Session ${i + 1}
                `;
            });
        }
    </script>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
    </style>
</x-app-layout>