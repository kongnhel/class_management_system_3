<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.manage-users') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 leading-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600">
                        <i class="fas fa-user-circle"></i>
                    </span>
                    {{ __('user_details') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $profile = $user->role === 'student' ? $user->studentProfile : $user->profile;
            @endphp

            {{-- Card: Profile Overview --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="flex-shrink-0">
                        @if ($profile && $profile->profile_picture_url)
                            <img src="{{ $profile->profile_picture_url }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-2xl object-cover border-4 border-emerald-100 shadow-lg">
                        @else
                            <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-emerald-400 to-purple-500 flex items-center justify-center text-white text-5xl font-bold shadow-lg">
                                {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="text-center md:text-left flex-1">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-gray-500 text-base mt-1">{{ $user->email ?? $user->student_id_code }}</p>
                        <div class="mt-3 flex flex-wrap gap-2 justify-center md:justify-start">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold
                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : ($user->role === 'professor' ? 'bg-green-100 text-green-700' : 'bg-emerald-100 text-emerald-700') }}">
                                <i class="fas {{ $user->role === 'admin' ? 'fa-shield-alt' : ($user->role === 'professor' ? 'fa-chalkboard-teacher' : 'fa-graduation-cap') }}"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.edit-user', $user->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl font-bold text-white hover:from-amber-600 hover:to-orange-600 transition shadow-md">
                            <i class="fas fa-pen"></i> {{ __('edit_2') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card: Profile Information --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-orange-100 text-orange-600">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <h3 class="text-xl font-bold text-gray-900">{{ __('personal_information') }}</h3>
                </div>

                @if ($profile)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">{{ __('full_name_khmer') }}</p>
                            <p class="text-gray-800 font-semibold">{{ $profile->full_name_km ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">{{ __('full_name_english') }}</p>
                            <p class="text-gray-800 font-semibold">{{ $profile->full_name_en ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">{{ __('gender') }}</p>
                            <p class="text-gray-800 font-semibold">{{ ucfirst($profile->gender ?? 'N/A') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">{{ __('date_of_birth') }}</p>
                            <p class="text-gray-800 font-semibold">{{ $profile->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">{{ __('phone_number') }}</p>
                            <p class="text-gray-800 font-semibold">{{ $profile->phone_number ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">{{ __('address') }}</p>
                            <p class="text-gray-800 font-semibold">{{ $profile->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <i class="fas fa-user-slash text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-400 italic">{{ __('no_profile_picture') }}</p>
                    </div>
                @endif
            </div>

            {{-- Role-Specific Information --}}
            @if ($user->role === 'professor')
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('courses_teaching') }}</h3>
                    </div>
                    <div class="space-y-3">
                        @forelse ($user->taughtCourseOfferings as $offering)
                            <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-emerald-200 transition">
                                <div class="flex items-center gap-4">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                                        <i class="fas fa-book"></i>
                                    </span>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $offering->course?->title_km ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-500">{{ $offering->department->name_km ?? 'N/A' }} ({{ $offering->academic_year }})</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.show-course-offering', $offering->id) }}" class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm">{{ __('view_details_2') }} &rarr;</a>
                            </div>
                        @empty
                            <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <i class="fas fa-chalkboard text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-400 italic">{{ __('this_professor_has_not_been_assigned_any_courses_yet') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            @elseif ($user->role === 'student')
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fas fa-book-open"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('enrolled_courses') }}</h3>
                    </div>
                    <div class="space-y-3">
                        @forelse ($user->studentCourseEnrollments as $enrollment)
                            <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-emerald-200 transition">
                                <div class="flex items-center gap-4">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                                        <i class="fas fa-book"></i>
                                    </span>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $enrollment->courseOffering->course->title_km ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-500">{{ $enrollment->courseOffering->department->name_km ?? 'N/A' }} ({{ $enrollment->courseOffering->academic_year }})</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.show-course-offering', $enrollment->courseOffering->id) }}" class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm">{{ __('view_details_2') }} &rarr;</a>
                            </div>
                        @empty
                            <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <i class="fas fa-book-open text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-400 italic">{{ __('this_student_has_not_enrolled_in_any_courses_yet') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Academic History --}}
                @if($user->studentDepartmentEnrollments->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-100 text-purple-600">
                                <i class="fas fa-history"></i>
                            </span>
                            <h3 class="text-xl font-bold text-gray-900">{{ __('academic_history') }}</h3>
                        </div>
                        <div class="space-y-3">
                            @foreach($user->studentDepartmentEnrollments as $enrollment)
                                <div class="flex items-center gap-4 p-4 rounded-xl {{ $enrollment->status === 'active' ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $enrollment->status === 'active' ? 'bg-green-500 text-white' : 'bg-gray-400 text-white' }}">
                                        @if($enrollment->status === 'graduated')
                                            <i class="fas fa-graduation-cap"></i>
                                        @else
                                            <i class="fas fa-book-open"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800">{{ $enrollment->department->name_km ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ __('level') }} {{ $enrollment->department->degree_level ?? 'N/A' }}
                                            @if($enrollment->starting_year_level > 1)
                                                · {{ __('starting_year') }} {{ $enrollment->starting_year_level }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-700' : ($enrollment->status === 'graduated' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700') }}">
                                            {{ $enrollment->status === 'active' ? __('studying') : ($enrollment->status === 'graduated' ? __('completed') : __('suspended')) }}
                                        </span>
                                        @if($enrollment->graduation_date)
                                            <p class="text-xs text-gray-400 mt-1">{{ $enrollment->graduation_date->format('d M Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Transition Button --}}
                @if($isEligibleForTransition && $transitionDepartments->count() > 0)
                    <div class="bg-gradient-to-r from-emerald-50 to-emerald-50 border border-emerald-200 rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-emerald-800">{{ __('transfer_to_bachelor_s_degree') }}</h4>
                                <p class="text-sm text-emerald-600 mt-1">{{ __('this_student_has_completed_the_final_year_of_the_associate_program_do_you_want_to_transfer_them_to_the_bachelor_program') }}</p>
                                <a href="{{ route('admin.students.transition', $user->id) }}" class="mt-3 inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition-all shadow-md hover:shadow-lg">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>{{ __('transfer_now') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Action Button --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.manage-users') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    <i class="fas fa-arrow-left"></i> {{ __('back_to_list') }}
                </a>
                <a href="{{ route('admin.edit-user', $user->id) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl font-bold text-white hover:from-amber-600 hover:to-orange-600 transition shadow-md">
                    <i class="fas fa-pen"></i> {{ __('edit_user') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
