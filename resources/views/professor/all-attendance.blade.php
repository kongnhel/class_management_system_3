<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-800 leading-tight">
            {{ __('គ្រប់គ្រងវត្តមាន') }}
        </h2>
        <p class="mt-1 text-lg text-gray-500">{{ __('ពិនិត្យ និងកត់ត្រាវត្តមានសម្រាប់មុខវិជ្ជារបស់អ្នក') }}</p>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-100 transition-all duration-500 ease-in-out">

                <div class="mb-10 text-center">
                    <h3 class="text-4xl font-extrabold text-emerald-700 mb-2">{{ __('កំណត់ត្រាវត្តមានទាំងអស់') }}</h3>
                    <p class="text-gray-600">{{ __('គ្រប់គ្រង និងកត់ត្រាវត្តមានរបស់និស្សិតសម្រាប់វគ្គសិក្សារបស់អ្នក។') }}</p>
                </div>
                
                <hr class="mb-10 border-gray-200">

                <h4 class="text-2xl font-bold text-gray-700 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('បន្ថែមកំណត់ត្រាវត្តមានថ្មី') }}
                </h4>
                <div class="bg-emerald-50 p-6 md:p-8 rounded-2xl shadow-inner mb-12 border border-emerald-200 transition-all duration-300 transform hover:scale-[1.005]">
                    <form action="{{ route('professor.attendances.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @csrf
                        {{-- Course Offering and Student Selection --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 col-span-1 lg:col-span-2">
                            <div>
                                <label for="course_offering_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('វគ្គសិក្សា') }}</label>
                                <select id="course_offering_id" name="course_offering_id" required class="mt-1 block w-full p-3 text-base border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                                    <option value="">{{ __('ជ្រើសរើសវគ្គសិក្សា') }}</option>
                                    @isset($professorCourseOfferings)
                                        @foreach ($professorCourseOfferings as $offering)
                                            <option value="{{ $offering->id }}">{{ $offering->course->title_km ?? $offering->course->title_en }} ({{ $offering->academic_year }} - {{ $offering->semester }})</option>
                                        @endforeach
                                    @endisset
                                </select>
                                @error('course_offering_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="student_user_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('និស្សិត') }}</label>
                                <select id="student_user_id" name="student_user_id" required class="mt-1 block w-full p-3 text-base border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                                    <option value="">{{ __('ជ្រើសរើសនិស្សិត') }}</option>
                                    @isset($students)
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->profile->full_name_km ?? $student->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                                @error('student_user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Date, Status, and Note --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('កាលបរិច្ឆេទ') }}</label>
                                <input type="date" id="date" name="date" required value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                                @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ស្ថានភាព') }}</label>
                                <select id="status" name="status" required class="mt-1 block w-full p-3 text-base border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300">
                                    <option value="present">{{ __('មានវត្តមាន') }}</option>
                                    <option value="absent">{{ __('អវត្តមាន') }}</option>
                                    <option value="late">{{ __('មកយឺត') }}</option>
                                    <option value="permission">{{ __('មានច្បាប់') }}</option>
                                </select>
                                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="col-span-1 lg:col-span-2">
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">{{ __('កំណត់ចំណាំ (ស្រេចចិត្ត)') }}</label>
                            <textarea id="remarks" name="remarks" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 transition-all duration-300"></textarea>
                            @error('remarks') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1 lg:col-span-2 flex justify-end">
                            <button type="submit" class="w-full md:w-auto mt-4 px-8 py-4 text-white font-extrabold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:scale-[1.01] bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800">
                                <span class="flex items-center justify-center space-x-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4a2 2 0 11-4 0m4 0a2 2 0 10-4 0m-9 8h10M4 16h10"></path></svg>
                                    <span>{{ __('រក្សាទុកវត្តមាន') }}</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <h4 class="text-2xl font-bold text-gray-700 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('កំណត់ត្រាវត្តមាន') }}
                </h4>
                <div class="overflow-x-auto bg-gray-50 rounded-2xl shadow-xl">
                    <table class="min-w-full leading-normal">
                        <thead class="bg-gradient-to-r from-emerald-700 to-emerald-700">
                            <tr>
                                <th class="py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider rounded-tl-2xl">{{ __('ឈ្មោះនិស្សិត') }}</th>
                                <th class="py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('មុខវិជ្ជា') }}</th>
                                <th class="py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('កាលបរិច្ឆេទ') }}</th>
                                <th class="py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('ស្ថានភាព') }}</th>
                                <th class="py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider rounded-tr-2xl">{{ __('កំណត់ចំណាំ') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($attendances as $record)
                                <tr class="hover:bg-emerald-50 transition-colors duration-200">
                                    <td class="py-4 px-6 text-gray-800 font-medium">{{ $record->student->profile->full_name_km ?? $record->student->name ?? 'N/A' }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $record->courseOffering->course->title_km ?? 'N/A' }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}</td>
                                    <td class="py-4 px-6 text-{{ $record->status === 'present' ? 'green' : ($record->status === 'absent' ? 'red' : ($record->status === 'late' ? 'yellow' : ($record->status === 'permission' ? 'blue' : 'gray'))) }}-600 font-bold uppercase">
                                        {{ $record->status_km }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">{{ $record->remarks ?? '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 px-6 text-center text-gray-500 bg-gray-50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-xl font-semibold mb-1">{{ __('មិនទាន់មានកំណត់ត្រាវត្តមានណាមួយត្រូវបានកត់ត្រាសម្រាប់មុខវិជ្ជារបស់អ្នកទេ។') }}</p>
                                            <p class="text-sm text-gray-400">{{ __('សូមប្រើទម្រង់ខាងលើដើម្បីចាប់ផ្តើមកត់ត្រាវត្តមាន។') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="mt-8">
                    {{ $attendances->links('pagination::tailwind', ['pageName' => 'attendancePage']) }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>