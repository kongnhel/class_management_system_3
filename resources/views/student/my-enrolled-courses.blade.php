<x-app-layout>
    <x-slot name="header">
        {{-- Responsive Header: Smaller text on mobile --}}
        <h2 class="font-extrabold text-2xl md:text-4xl text-gray-900 leading-tight tracking-wide">
            {{ __('មុខវិជ្ជាដែលបានចុះឈ្មោះរបស់ខ្ញុំ') }}
        </h2>
        <p class="mt-1 md:mt-2 text-sm md:text-lg text-gray-500">{{ __('បញ្ជីឈ្មោះមុខវិជ្ជាដែលអ្នកបានចុះឈ្មោះ') }}</p>
    </x-slot>

    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-full mx-auto">
            {{-- Responsive Padding: p-4 on mobile, p-12 on desktop --}}
            <div class="bg-white overflow-hidden shadow-xl md:shadow-2xl p-4 md:p-8 lg:p-12 border border-gray-100">
                
                {{-- 1. Program Information --}}
                @if ($studentProgram)
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 md:mb-10 pb-5 border-b border-gray-200">
                        <h3 class="text-xl md:text-3xl font-extrabold text-green-700 mb-4 md:mb-0 flex items-center">
                            <i class="fas fa-graduation-cap text-xl md:text-3xl mr-3 text-green-600"></i>
                            {{ __('ជំនាញ') }}: {{ $studentProgram->name_km }}
                        </h3>
                    </div>
                @else
                    <div class="bg-gray-100 p-6 md:p-8 rounded-2xl text-center text-gray-500 mb-10 shadow-inner">
                        <p class="text-lg md:text-2xl font-bold text-gray-800 mb-2">{{ __('អ្នកមិនទាន់បានចុះឈ្មោះក្នុងកម្មវិធីសិក្សាណាមួយនៅឡើយទេ') }}</p>
                    </div>
                @endif

                {{-- 2. Success Message --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 md:px-6 md:py-4 rounded-xl mb-6 flex items-center space-x-3 shadow-sm text-sm md:text-base">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                {{-- 3. Course List --}}
                <div class="mt-6 md:mt-8">
                    @if ($enrollments->isEmpty())
                        <div class="bg-gray-100 p-8 rounded-2xl text-center text-gray-500 shadow-inner">
                            <p class="text-xl md:text-2xl font-bold text-gray-800">{{ __('អ្នកមិនទាន់បានចុះឈ្មោះក្នុងមុខវិជ្ជាណាមួយនៅឡើយទេ') }}</p>
                        </div>
                    @else
                        {{-- Responsive Grid: Gap reduced on mobile --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-8">
                            @foreach ($enrollments as $enrollment)
                                @php
                                    $lecturer = $enrollment->courseOffering->lecturer;
                                    $lecturerProfile = $lecturer ? $lecturer->userProfile : null;
                                @endphp
                                
                                <div class="bg-white rounded-2xl md:rounded-3xl shadow-lg border border-gray-100 p-5 md:p-8 flex flex-col justify-between hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.01] md:hover:scale-[1.02]">
                                    <div>
                                        {{-- Top Section: Course Name --}}
                                        <div class="flex items-start mb-4 md:mb-6">
                                            <div class="flex-shrink-0 w-12 h-12 md:w-16 md:h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl md:text-2xl mr-4 md:mr-5 shadow-md">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg md:text-xl font-bold text-gray-900 leading-tight">
                                                    {{ $enrollment->courseOffering->course->title_en ?? $enrollment->courseOffering->course->course_name }}
                                                </h4>
                                                <p class="text-[10px] md:text-xs text-gray-400 mt-1 uppercase tracking-wider font-semibold">
                                                    {{ $enrollment->courseOffering->course->course_code }}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Middle Section: Lecturer & Status --}}
                                        <div class="mb-4 md:mb-6 space-y-3 md:space-y-4">
                                            {{-- Lecturer Info --}}
                                            <div class="flex items-center justify-between bg-gray-50 p-3 md:p-4 rounded-2xl border border-gray-100">
                                                <div class="flex items-center">
                                                    @if ($lecturerProfile && $lecturerProfile->profile_picture_url)
                                                        <img class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover mr-3 border-2 border-white shadow-sm" src="{{$lecturerProfile->profile_picture_url }}">
                                                    @else
                                                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-green-200 flex items-center justify-center mr-3 text-green-700">
                                                            <i class="fas fa-user-tie text-lg md:text-xl"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="text-[9px] md:text-[10px] text-gray-400 font-extrabold uppercase tracking-tighter">{{ __('សាស្ត្រាចារ្យ') }}</p>
                                                        <p class="font-bold text-gray-800 text-xs md:text-sm">{{ $lecturer->name ?? 'N/A' }}</p>
                                                    </div>
                                                </div>

                                                {{-- Telegram Icon --}}
                                                @if($lecturerProfile && $lecturerProfile->telegram_user)
                                                    <a href="https://t.me/{{ $lecturerProfile->telegram_user }}" target="_blank" class="text-[#0088cc] hover:text-emerald-600 transition-transform transform hover:scale-110">
                                                        <i class="fab fa-telegram text-2xl md:text-3xl"></i>
                                                    </a>
                                                @endif
                                            </div>

                                            {{-- Status Badge --}}
                                            <div class="flex items-center justify-between px-2">
                                                <span class="text-xs md:text-sm font-semibold text-gray-600 flex items-center">
                                                    <i class="fas fa-info-circle mr-2 text-green-500"></i> {{ __('ស្ថានភាព') }}:
                                                </span>
                                                <span class="px-2 py-0.5 md:px-3 md:py-1 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest {{ $enrollment->status == 'enrolled' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-yellow-100 text-yellow-700 border border-yellow-200' }}">
                                                    {{ $enrollment->status == 'enrolled' ? __('បានចុះឈ្មោះ') : __('រង់ចាំ') }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Class Leader Tools --}}
                                        @if($enrollment->is_class_leader == 1)
                                            <div class="mb-4 md:mb-6 p-3 md:p-4 bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-100 rounded-2xl shadow-sm">
                                                <p class="text-[10px] md:text-[11px] font-black text-yellow-700 mb-2 md:mb-3 flex items-center uppercase">
                                                    <i class="fas fa-star-badge mr-2"></i> {{ __('ឧបករណ៍ប្រធានថ្នាក់') }}
                                                </p>
                                                <div class="grid grid-cols-2 gap-2 md:gap-3">
                                                    <a href="{{ route('student.leader.attendance', $enrollment->course_offering_id) }}" class="flex items-center justify-center bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-xl text-[9px] md:text-[10px] font-bold transition shadow-sm">
                                                        <i class="fas fa-clipboard-list mr-1"></i> {{ __('វត្តមាន') }}
                                                    </a>
                                                    <a href="{{ route('student.leader.report', $enrollment->course_offering_id) }}" class="flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-xl text-[9px] md:text-[10px] font-bold transition shadow-sm">
                                                        <i class="fas fa-file-invoice mr-1"></i> {{ __('របាយការណ៍') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Schedule Section --}}
                                        <div class="pt-4 md:pt-5 border-t border-gray-100">
                                            <p class="text-xs md:text-sm font-bold text-gray-800 mb-3 md:mb-4 flex items-center">
                                                <i class="far fa-calendar-alt mr-2 text-green-500"></i> {{ __('កាលវិភាគសិក្សា') }}
                                            </p>
                                            <div class="space-y-2 md:space-y-3">
                                                @forelse ($enrollment->courseOffering->schedules as $schedule)
                                                    <div class="text-[11px] md:text-[12px] bg-gray-50 p-2 md:p-3 rounded-xl border border-gray-100 hover:bg-white hover:border-green-200 transition-all">
                                                        <div class="flex justify-between items-center">
                                                            <span class="font-black text-green-700">{{ __($schedule->day_of_week) }}</span>
                                                            <span class="text-gray-700 font-bold bg-white px-1.5 py-0.5 rounded-md shadow-sm border border-gray-100">
                                                                {{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }}
                                                            </span>
                                                        </div>
                                                        <div class="text-[9px] md:text-[10px] text-gray-400 mt-1.5 md:mt-2 flex items-center">
                                                            <i class="fas fa-map-marker-alt mr-2 text-red-400"></i> 
                                                            <span class="font-medium">{{ __('បន្ទប់') }}:</span> 
                                                            <span class="ml-1 text-gray-700 font-bold">{{ $schedule->room->room_number ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-center py-2 italic text-gray-400 text-[10px]">
                                                        {{ __('មិនទាន់មានកាលវិភាគនៅឡើយ') }}
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8 md:mt-12 flex justify-center">
                            {{ $enrollments->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>