<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-3xl text-gray-800 leading-tight">
                    {{ __('គ្រប់គ្រងវត្តមានសម្រាប់មុខវិជ្ជា') }}
                </h2>
                <p class="mt-1 text-lg text-gray-500">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en ?? 'N/A' }} ({{ $courseOffering->academic_year }} - {{ $courseOffering->semester }})</p>
            </div>

            <a href="{{ route('professor.my-course-offerings', ['offering_id' => $courseOffering->id]) }}"
                class="inline-flex items-center px-6 py-3 
                bg-gradient-to-r from-emerald-500 via-emerald-600 to-emerald-700 
                hover:from-emerald-600 hover:via-emerald-700 hover:to-emerald-800 
                text-white text-sm font-semibold rounded-lg shadow-md 
                hover:shadow-lg transform hover:scale-105 
                transition-all duration-300 ease-out focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('ត្រឡប់ទៅបញ្ជីមុខវិជ្ជា') }}
            </a>
        </div>
    </x-slot>
     {{-- Success/Error Messages (Existing) --}}
                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mx-6 mt-6 mb-0" role="alert">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-green-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <p class="font-semibold">{{ __('ជោគជ័យ!') }}</p>
                            <p class="ml-auto">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mx-6 mt-6 mb-0" role="alert">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-red-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <p class="font-semibold">{{ __('បរាជ័យ!') }}</p>
                            <p class="ml-auto">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-100 transition-transform duration-500 ease-in-out">

                <div class="bg-emerald-50 p-6 rounded-2xl shadow-md border-l-4 border-emerald-500 mb-10 transition-all duration-300 transform hover:scale-[1.005]">
                    <div class="flex items-center space-x-4">
                        <svg class="w-10 h-10 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="text-xl font-bold text-emerald-800">{{ __('ព័ត៌មានវគ្គសិក្សា') }}</p>
                            <ul class="list-disc list-inside text-gray-700 mt-2 text-sm md:text-base">
                                <li>{{ __('លេខកូដមុខវិជ្ជា:') }} <span class="font-semibold text-gray-900">{{ $courseOffering->course->code ?? 'N/A' }}</span></li>
                                <li>{{ __('គ្រូបង្រៀន:') }} <span class="font-semibold text-gray-900">{{ $courseOffering->lecturer->name ?? 'N/A' }}</span></li>
                                <li>{{ __('ចំនួននិស្សិតចុះឈ្មោះ:') }} <span class="font-semibold text-gray-900">{{ $courseOffering->studentCourseEnrollments->count() }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <hr class="border-gray-200 my-8">

                <h4 class="text-2xl font-bold text-gray-700 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('បន្ថែមកំណត់ត្រាវត្តមានថ្មី') }}
                </h4>
                <div class="bg-gray-100 p-6 rounded-2xl shadow-inner mb-12 border border-gray-200">
                    <form action="{{ route('professor.attendances.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @csrf
                        <input type="hidden" name="course_offering_id" value="{{ $courseOffering->id }}">

                        <div>
                            <label for="student_user_id" class="block text-sm font-medium text-gray-700">{{ __('និស្សិត') }}</label>
                            <select id="student_user_id" name="student_user_id" required class="mt-1 block w-full p-3 text-base border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                                <option value="">{{ __('ជ្រើសរើសនិស្សិត') }}</option>
                                @foreach ($courseOffering->studentCourseEnrollments as $enrollment)
                                    <option value="{{ $enrollment->student->id }}">
                                        {{ $enrollment->student->profile->full_name_km ?? $enrollment->student->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">{{ __('កាលបរិច្ឆេទ') }}</label>
                            <input type="date" id="date" name="date" required value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                            @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">{{ __('ស្ថានភាព') }}</label>
                            <select id="status" name="status" required class="mt-1 block w-full p-3 text-base border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                                <option value="present">{{ __('មានវត្តមាន') }}</option>
                                <option value="absent">{{ __('អវត្តមាន') }}</option>
                                <option value="late">{{ __('មកយឺត') }}</option>
                                <option value="permission">{{ __('មានច្បាប់') }}</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="remarks" class="block text-sm font-medium text-gray-700">{{ __('កំណត់ចំណាំ (ស្រេចចិត្ត)') }}</label>
                            <textarea id="remarks" name="remarks" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 transition-all duration-300"></textarea>
                            @error('remarks') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 lg:col-span-3 flex justify-end mt-4">
                            <button type="submit" class="w-full md:w-auto px-8 py-4 text-white font-extrabold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:scale-[1.01] bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800">
                                <span class="flex items-center justify-center space-x-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4a2 2 0 11-4 0m4 0a2 2 0 10-4 0m-9 8h10M4 16h10"></path></svg>
                                    <span>{{ __('រក្សាទុកវត្តមាន') }}</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <hr class="border-gray-200 my-8">

                <h4 class="text-2xl font-bold text-gray-700 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2m14 0V9a2 2 0 00-2-2H8m2-4h4a2 2 0 012 2v2M9 14h6"></path></svg>
                    {{ __('កំណត់ត្រាវត្តមានបច្ចុប្បន្ន') }}
                </h4>
<div class="overflow-x-auto bg-gray-50 rounded-2xl shadow-xl mb-6">
    <div class="block sm:table min-w-full leading-normal">

        {{-- Table Header (បង្ហាញតែនៅលើអេក្រង់ធំ) --}}
        <div class="hidden sm:table-header-group bg-gradient-to-r from-emerald-700 to-emerald-700">
            <div class="sm:table-row">
                <div class="sm:table-cell py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider rounded-tl-2xl">{{ __('ឈ្មោះនិស្សិត') }}</div>
                <div class="sm:table-cell py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('កាលបរិច្ឆេទ') }}</div>
                <div class="sm:table-cell py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('ស្ថានភាព') }}</div>
                {{-- <div class="sm:table-cell py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('កំណត់ចំណាំ') }}</div> --}}
                <div class="sm:table-cell py-4 px-6 text-center text-sm font-bold text-white uppercase tracking-wider rounded-tr-2xl">{{ __('សកម្មភាព') }}</div>
            </div>
        </div>

        {{-- Table Body (បង្ហាញជា Grid លើទូរស័ព្ទ, ជា Row លើអេក្រង់ធំ) --}}
        <div class="block sm:table-row-group bg-white divide-y divide-gray-100">
            @forelse ($attendanceRecords as $record)
                
                {{-- រចនាប័ទ្ម Card សម្រាប់អេក្រង់តូច (sm:hidden) --}}
                <div class="sm:hidden p-4 mb-4 bg-white rounded-xl shadow-md border border-gray-200 hover:bg-emerald-50 transition-colors duration-200">
                    <div class="flex justify-between items-center mb-2 border-b pb-2">
                        <span class="text-lg font-bold text-gray-800">{{ $record->student->profile->full_name_km ?? $record->student->name ?? 'N/A' }}</span>
                        @php
                            $color = '';
                            if ($record->status === 'present') $color = 'green';
                            elseif ($record->status === 'absent') $color = 'red';
                            elseif ($record->status === 'late') $color = 'yellow';
                            elseif ($record->status === 'permission') $color = 'blue';
                            else $color = 'gray';
                        @endphp
                        <span class="inline-block px-3 py-1 font-extrabold text-{{ $color }}-800 bg-{{ $color }}-200 rounded-full text-xs uppercase">
                            {{ $record->status_km }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 mb-1"><span class="font-semibold text-gray-700">{{ __('កាលបរិច្ឆេទ') }}:</span> {{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}</p>
                    {{-- <p class="text-sm text-gray-600 mb-4"><span class="font-semibold text-gray-700">{{ __('កំណត់ចំណាំ') }}:</span> {{ $record->notes ?? 'N/A' }}</p> --}}

                    <div class="flex justify-start space-x-2 border-t pt-3">
                        {{-- Edit Button --}}
                        <button type="button" class="text-purple-600 hover:text-white font-semibold py-1 px-3 rounded-full text-xs transition-all duration-200 hover:bg-purple-600 edit-attendance shadow-sm"
                                data-id="{{ $record->id }}"
                                data-student-user-id="{{ $record->student_user_id }}"
                                data-date="{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}"
                                data-status="{{ $record->status }}"
                                data-remarks="{{ $record->remarks ?? '' }}">
                            {{ __('កែសម្រួល') }}
                        </button>
                        {{-- Delete Form (Card View) --}}
                        <form action="{{ route('professor.attendances.destroy', $record->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('តើអ្នកពិតជាចង់លុបកំណត់ត្រាវត្តមាននេះមែនទេ?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-white font-semibold py-1 px-3 rounded-full text-xs transition-all duration-200 hover:bg-red-600 shadow-sm">
                                {{ __('លុប') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- រចនាប័ទ្ម Table Row សម្រាប់អេក្រង់ធំ (sm:table-row) --}}
                <div class="hidden sm:table-row hover:bg-emerald-50 transition-colors duration-200">
                    <div class="sm:table-cell py-4 px-6 text-gray-800 font-medium">{{ $record->student->profile->full_name_km ?? $record->student->name ?? 'N/A' }}</div>
                    <div class="sm:table-cell py-4 px-6 text-gray-600">{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}</div>
                    <div class="sm:table-cell py-4 px-6 font-bold uppercase">
                        @php
                            $color = '';
                            if ($record->status === 'present') $color = 'green';
                            elseif ($record->status === 'absent') $color = 'red';
                            elseif ($record->status === 'late') $color = 'yellow';
                            elseif ($record->status === 'permission') $color = 'blue';
                            else $color = 'gray';
                        @endphp
                        <span class="inline-block px-3 py-1 font-semibold text-{{ $color }}-800 bg-{{ $color }}-200 rounded-full text-xs">
                            {{ $record->status_km }}
                        </span>
                    </div>
                    {{-- <div class="sm:table-cell py-4 px-6 text-gray-600">{{ $record->remarks ?? '' }}</div> --}}
                    <div class="sm:table-cell py-4 px-6 text-center space-x-2 flex justify-center items-center">
                        {{-- Edit Button --}}
                        <button type="button" class="text-purple-600 hover:text-white font-semibold py-2 px-4 rounded-full text-sm transition-all duration-200 hover:bg-purple-600 edit-attendance shadow-sm"
                                data-id="{{ $record->id }}"
                                data-student-user-id="{{ $record->student_user_id }}"
                                data-date="{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}"
                                data-status="{{ $record->status }}"
                                data-remarks="{{ $record->remarks ?? '' }}">
                            {{ __('កែសម្រួល') }}
                        </button>

                        {{-- Delete Form (Table View) --}}
                        <form action="{{ route('professor.attendances.destroy', $record->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('តើអ្នកពិតជាចង់លុបកំណត់ត្រាវត្តមាននេះមែនទេ?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-white font-semibold py-2 px-4 rounded-full text-sm transition-all duration-200 hover:bg-red-600 shadow-sm">
                                {{ __('លុប') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="sm:table-row">
                    <div class="sm:table-cell py-10 px-6 text-center text-gray-500 bg-gray-50" colspan="5">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-xl font-semibold mb-1">{{ __('មិនទាន់មានកំណត់ត្រាវត្តមានណាមួយសម្រាប់វគ្គសិក្សានេះនៅឡើយទេ។') }}</p>
                            <p class="text-sm text-gray-400">{{ __('សូមប្រើទម្រង់ខាងលើដើម្បីចាប់ផ្តើមកត់ត្រាវត្តមាន។') }}</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Pagination Links --}}
<div class="mt-8">
    {{ $attendanceRecords->links() }}
</div>
    {{-- Edit Attendance Modal --}}
    <div x-data="{ 
            open: false, 
            attendanceId: '', 
            studentUserId: '', 
            date: '', 
            status: '', 
            remarks: '',
            // FIX: Generate the route with a placeholder (0) to satisfy the required parameter.
            updateRoute: '{{ route('professor.attendances.update', 0) }}' 
        }"
        @open-edit-modal.window="
            open = true; 
            attendanceId = $event.detail.id; 
            studentUserId = $event.detail.studentUserId; 
            date = $event.detail.date; 
            status = $event.detail.status; 
            remarks = $event.detail.remarks;">
        
        <div x-show="open" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50" style="display: none;">
            <div @click.away="open = false" class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-lg mx-auto transform transition-all duration-300 scale-95"
                  x-transition:enter="ease-out duration-300"
                  x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                  x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave="ease-in duration-200"
                  x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <h4 class="text-2xl font-bold text-gray-800 mb-6">{{ __('កែសម្រួលកំណត់ត្រាវត្តមាន') }}</h4>
                
                {{-- FIX: Use Alpine's replace() to swap the placeholder '0' with the dynamic attendanceId --}}
                <form :action="updateRoute.replace('0', attendanceId)" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="course_offering_id" value="{{ $courseOffering->id }}">

                    <div class="mb-4">
                        <label for="edit_student_user_id" class="block text-sm font-medium text-gray-700">{{ __('និស្សិត') }}</label>
                        <select id="edit_student_user_id" name="student_user_id" x-model="studentUserId" required class="mt-1 block w-full p-3 border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                            @foreach ($courseOffering->studentCourseEnrollments as $enrollment)
                                <option value="{{ $enrollment->student->id }}">
                                    {{ $enrollment->student->profile->full_name_km ?? $enrollment->student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="edit_date" class="block text-sm font-medium text-gray-700">{{ __('កាលបរិច្ឆេទ') }}</label>
                        <input type="date" id="edit_date" name="date" x-model="date" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                    </div>

                    <div class="mb-4">
                        <label for="edit_status" class="block text-sm font-medium text-gray-700">{{ __('ស្ថានភាព') }}</label>
                        <select id="edit_status" name="status" x-model="status" required class="mt-1 block w-full p-3 text-base border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                            <option value="present">{{ __('មានវត្តមាន') }}</option>
                            <option value="absent">{{ __('អវត្តមាន') }}</option>
                            <option value="late">{{ __('មកយឺត') }}</option>
                            <option value="permission">{{ __('មានច្បាប់') }}</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label for="edit_remarks" class="block text-sm font-medium text-gray-700">{{ __('កំណត់ចំណាំ (ស្រេចចិត្ត)') }}</label>
                        <input type="text" id="edit_remarks" name="remarks" x-model="remarks" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="open = false" class="px-6 py-3 text-gray-700 font-semibold rounded-xl shadow-sm transition-all duration-200 hover:bg-gray-200">
                            {{ __('បោះបង់') }}
                        </button>
                        <button type="submit" class="px-6 py-3 text-white font-semibold rounded-xl shadow-md transition-all duration-200 bg-emerald-600 hover:bg-emerald-700">
                            {{ __('រក្សាទុកការកែប្រែ') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-attendance').forEach(button => {
                button.addEventListener('click', function() {
                    const data = this.dataset;
                    const remarksValue = data.remarks === undefined ? '' : data.remarks; 

                    window.dispatchEvent(new CustomEvent('open-edit-modal', {
                        detail: {
                            id: data.id,
                            studentUserId: data.studentUserId,
                            date: data.date,
                            status: data.status,
                            remarks: remarksValue,
                        }
                    }));
                });
            });
        });
    </script>
</x-app-layout>