<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between px-4 md:px-6 lg:px-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 leading-tight flex items-center gap-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-chart-bar text-green-600 text-xl"></i>
                    </div>
                    {{ __('admin_dashboard_title') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 ml-12">{{ __('admin_dashboard_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ============================================================ --}}
            {{-- HERO BANNER - greeting + key metrics                        --}}
            {{-- ============================================================ --}}
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white shadow-lg shadow-emerald-600/20">
                <div class="absolute -top-24 -right-16 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative px-6 sm:px-10 py-8">
                    <div class="flex items-center gap-5 min-w-0">
                        <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 overflow-hidden flex items-center justify-center text-2xl font-bold">
                            <i class="fas fa-chart-bar text-emerald-200"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-emerald-200/80 text-[11px] font-semibold uppercase tracking-widest">{{ now()->translatedFormat('l, d F Y') }}</p>
                            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 truncate">
                                {{ __('admin_dashboard_title') }}
                            </h1>
                            <p class="text-emerald-100/70 text-sm mt-1">{{ __('admin_dashboard_subtitle') }}</p>
                        </div>
                    </div>

                    {{-- today's attendance metrics row --}}
                    <div class="mt-8 pt-6 border-t border-white/15 grid grid-cols-3 divide-x divide-white/10">
                        <div class="pr-5 sm:pr-8">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-200/80">{{ __('admin_today_total_attendance') }}</p>
                            <p class="text-2xl sm:text-3xl font-bold tracking-tight tabular-nums mt-1 leading-none">{{ $todayAttendanceCount }}</p>
                        </div>
                        <div class="px-5 sm:px-8">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-200/80">{{ __('admin_today_present') }}</p>
                            <p class="text-2xl sm:text-3xl font-bold tracking-tight tabular-nums mt-1 leading-none text-emerald-200">{{ $todayPresentCount }}</p>
                        </div>
                        <div class="pl-5 sm:pl-8">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-200/80">{{ __('admin_today_absent') }}</p>
                            <p class="text-2xl sm:text-3xl font-bold tracking-tight tabular-nums mt-1 leading-none text-amber-200">{{ $todayAbsentCount }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- MAIN GRID: quick actions (2/3) + system info (1/3)          --}}
            {{-- ============================================================ --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

                {{-- Quick Actions --}}
                <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <h4 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-bolt text-teal-500"></i>
                            {{ __('admin_quick_actions') }}
                        </h4>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <a href="{{ route('admin.create-user') }}" class="group flex items-center p-4 bg-gray-50 rounded-xl border border-transparent hover:border-emerald-200 hover:bg-emerald-50 transition-all duration-200">
                            <div class="h-12 w-12 rounded-lg bg-white flex items-center justify-center shadow-sm text-emerald-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-user-plus text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h5 class="font-bold text-gray-800 group-hover:text-emerald-700">{{ __('admin_add_user') }}</h5>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin_create_account') }}</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-emerald-400"></i>
                        </a>

                        <a href="{{ route('admin.create-faculty') }}" class="group flex items-center p-4 bg-gray-50 rounded-xl border border-transparent hover:border-green-200 hover:bg-green-50 transition-all duration-200">
                            <div class="h-12 w-12 rounded-lg bg-white flex items-center justify-center shadow-sm text-green-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-university text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h5 class="font-bold text-gray-800 group-hover:text-green-700">{{ __('admin_add_faculty') }}</h5>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin_create_institution') }}</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-green-400"></i>
                        </a>

                        <a href="{{ route('admin.manage-users') }}" class="group flex items-center p-4 bg-gray-50 rounded-xl border border-transparent hover:border-purple-200 hover:bg-purple-50 transition-all duration-200">
                            <div class="h-12 w-12 rounded-lg bg-white flex items-center justify-center shadow-sm text-purple-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-users-cog text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h5 class="font-bold text-gray-800 group-hover:text-purple-700">{{ __('admin_manage_users') }}</h5>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin_edit_delete') }}</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-purple-400"></i>
                        </a>

                        <a href="{{ route('admin.manage-faculties') }}" class="group flex items-center p-4 bg-gray-50 rounded-xl border border-transparent hover:border-red-200 hover:bg-red-50 transition-all duration-200">
                            <div class="h-12 w-12 rounded-lg bg-white flex items-center justify-center shadow-sm text-red-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-building text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h5 class="font-bold text-gray-800 group-hover:text-red-700">{{ __('admin_manage_faculties') }}</h5>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin_institution_data') }}</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-gray-300 group-hover:text-red-400"></i>
                        </a>

                    </div>
                </div>

                {{-- System Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-gray-50">
                        <h4 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-info-circle text-emerald-500"></i>
                            {{ __('admin_system_info') }}
                        </h4>
                    </div>
                    <div class="p-6 flex-1">
                        <div class="space-y-5">
                            {{-- Departments --}}
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                                        <i class="fas fa-sitemap text-xs"></i>
                                    </div>
                                    <span class="text-gray-600 font-medium">{{ __('admin_total_departments') }}</span>
                                </div>
                                <span class="text-lg font-bold text-gray-900 group-hover:text-emerald-600 transition-colors tabular-nums">{{ $totalDepartments }}</span>
                            </div>
                            <hr class="border-gray-50">

                            {{-- Courses --}}
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                                        <i class="fas fa-book text-xs"></i>
                                    </div>
                                    <span class="text-gray-600 font-medium">{{ __('admin_total_courses') }}</span>
                                </div>
                                <span class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors tabular-nums">{{ $totalCourses }}</span>
                            </div>
                            <hr class="border-gray-50">

                            {{-- Offerings --}}
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                                        <i class="fas fa-layer-group text-xs"></i>
                                    </div>
                                    <span class="text-gray-600 font-medium">{{ __('admin_total_offerings') }}</span>
                                </div>
                                <span class="text-lg font-bold text-gray-900 group-hover:text-emerald-600 transition-colors tabular-nums">{{ $totalCourseOfferings }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ============================================================ --}}
            {{-- KEY METRICS ROW: users + students + professors + faculties  --}}
            {{-- ============================================================ --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">{{ __('admin_total_users') }}</p>
                            <h3 class="text-3xl font-extrabold text-gray-800 tabular-nums">{{ $totalUsers }}</h3>
                        </div>
                        <div class="p-3 bg-green-50 rounded-xl text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-users-cog text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">{{ __('admin_total_students') }}</p>
                            <h3 class="text-3xl font-extrabold text-gray-800 tabular-nums">{{ $totalStudents }}</h3>
                        </div>
                        <div class="p-3 bg-teal-50 rounded-xl text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-user-graduate text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">{{ __('admin_total_professors') }}</p>
                            <h3 class="text-3xl font-extrabold text-gray-800 tabular-nums">{{ $totalProfessors }}</h3>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-xl text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">{{ __('admin_total_faculties') }}</p>
                            <h3 class="text-3xl font-extrabold text-gray-800 tabular-nums">{{ $totalFaculties }}</h3>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-xl text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-building text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">{{ __('admin_active_course_offerings') }}</p>
                            <h3 class="text-3xl font-extrabold text-gray-800 tabular-nums">{{ $activeCourseOfferings }}</h3>
                        </div>
                        <div class="p-3 bg-cyan-50 rounded-xl text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-calendar-check text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- RECENT USERS & ANNOUNCEMENTS                                --}}
            {{-- ============================================================ --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

                {{-- Recent Users (2/3 width) --}}
                <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <h4 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-user-clock text-emerald-500"></i>
                            {{ __('admin_recent_users') }}
                        </h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">{{ __('admin_user_name') }}</th>
                                    <th class="px-6 py-3 font-semibold">{{ __('admin_role') }}</th>
                                    <th class="px-6 py-3 font-semibold">{{ __('admin_joined_date') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentUsers as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-600 font-bold text-sm overflow-hidden">
                                                @php
                                                    $profilePic = $user->profile?->profile_picture_url ?? $user->avatar ?? null;
                                                @endphp
                                                @if(!empty($profilePic) && $profilePic !== 'null')
                                                    <img src="{{ $profilePic }}" alt="{{ $user->name }}" class="h-9 w-9 object-cover"
                                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                                    <span class="h-9 w-9 items-center justify-center" style="display:none;">{{ strtoupper(mb_substr($user->name, 0, 2)) }}</span>
                                                @else
                                                    {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                                                @endif
                                            </div>
                                            <span class="font-medium text-gray-800">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->role === 'admin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ __('admin_role_admin') }}</span>
                                        @elseif($user->role === 'professor')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700">{{ __('admin_role_professor') }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-700">{{ __('admin_role_student') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 tabular-nums">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                                        <i class="fas fa-users-slash text-2xl mb-2 block"></i>
                                        {{ __('admin_no_users') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Latest Announcements (1/3 width) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <h4 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-bullhorn text-amber-500"></i>
                            {{ __('admin_latest_announcements') }}
                        </h4>
                    </div>
                    <div class="p-6 flex-1">
                        @forelse($announcements as $announcement)
                        <div class="flex items-start gap-3 {{ !$loop->last ? 'pb-4 mb-4 border-b border-gray-50' : '' }}">
                            <div class="mt-1 flex-shrink-0">
                                <div class="h-2 w-2 rounded-full bg-amber-400"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 text-sm leading-tight truncate">{{ $announcement->title }}</p>
                                <p class="text-xs text-gray-400 mt-1 tabular-nums">{{ $announcement->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                <i class="fas fa-megaphone text-lg"></i>
                            </div>
                            <p class="text-sm text-gray-400">{{ __('admin_no_announcements') }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
