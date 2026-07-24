<nav 
    class="fixed top-0 left-0 h-screen lg:w-72 w-72 bg-slate-900 text-gray-300 border-r border-slate-700/50 shadow-xl transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out z-50 font-['Battambang']"
    :class="{ 'translate-x-0': open, '-translate-x-full': !open }"
>
@auth
@php
    $user = Auth::user();
    $profileUrl = $user->userProfile?->profile_picture_url ?? $user->studentProfile?->profile_picture_url;
    $roleText = match ($user->role) {
        'admin' => __('role_admin'),
        'professor' => __('role_professor'),
        'student' => __('role_student'),
        default => ''
    };
    $isClassLeader = false;
    if($user->role === 'student') {
        $isClassLeader = \DB::table('student_course_enrollments')
            ->where('student_user_id', $user->id)
            ->where('is_class_leader', 1)
            ->exists();
    }
    $unreadCount = $user->unreadNotifications()->count();
    $unreadAnnouncements = \App\Models\Announcement::where(function ($q) use ($user) {
        $q->where('target_role', 'all')
          ->orWhere('target_role', $user->role);
    })->whereDoesntHave('reads', fn($q) => $q->where('user_id', $user->id))
      ->count();
    $totalUnread = $unreadCount + $unreadAnnouncements;
@endphp

    <div class="flex flex-col h-full">
        {{-- Profile Section --}}
        <div class="shrink-0 px-5 py-6 border-b border-slate-700/50">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 group">
                <div class="relative shrink-0">
                    <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center text-lg font-bold bg-gradient-to-br from-green-500 to-emerald-600 ring-2 ring-green-500/30 transition-transform group-hover:scale-105">
                        @if($profileUrl)
                            <img src="{{ $profileUrl }}" alt="" class="h-full w-full object-cover">
                        @else
                            {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    @if($isClassLeader)
                        <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-yellow-500 rounded-full border-2 border-slate-900 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1.01 0 00.951-.69l1.07-3.292z"/></svg>
                        </span>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-base font-bold text-white truncate">{{ $user->name }}</p>
                    <p class="text-xs text-green-400 font-semibold uppercase tracking-wide">{{ $roleText }}</p>
                </div>
            </a>
        </div>

        {{-- Navigation Links --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar px-4 py-4 space-y-1">
            
            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-green-600 text-white shadow-lg shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>{{ __('nav_dashboard') }}</span>
            </a>

            {{-- ==================== ADMIN LINKS ==================== --}}
            @if($user->role === 'admin')
                
                {{-- Academic Section --}}
                @php
                    $academicRoutes = ['admin.academic-years.*', 'admin.manage-courses', 'admin.manage-course-offerings', 'admin.manage-programs', 'admin.manage-faculties', 'admin.manage-departments', 'admin.rooms.*', 'admin.generations.*'];
                    $isAcademicOpen = in_array(request()->route()->getName() ?? '', $academicRoutes);
                @endphp
                <div x-data="{
                    open: $persist(false).as('nav_academic_open'),
                    init() {
                        if (localStorage.getItem('nav_academic_open') === null) {
                            this.open = @json($isAcademicOpen);
                        }
                    }
                }" class="mt-4">
                    <button @click="open = !open" 
                            class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-200 {{ $isAcademicOpen ? 'bg-green-500/10 text-green-400' : 'text-slate-400 hover:bg-slate-700/40 hover:text-slate-300' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span class="flex-1 text-left">{{ __('nav_management') }}</span>
                        <svg class="w-4 h-4 shrink-0 transition-transform duration-300 ease-out" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="mt-1 ml-2 border-l-2 border-slate-700/60 pl-2 space-y-0.5">
                        <a href="{{ route('admin.academic-years.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.academic-years.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>{{ __('ឆ្នាំសិក្សា') }}</span>
                        </a>
                        <a href="{{ route('admin.manage-faculties') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.manage-faculties') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 8h6M9 12h6M9 16h6"/></svg>
                            <span>{{ __('nav_faculty_management') }}</span>
                        </a>
                        <a href="{{ route('admin.manage-departments') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.manage-departments') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span>{{ __('nav_department_management') }}</span>
                        </a>
                        <a href="{{ route('admin.manage-programs') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.manage-programs') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                            <span>{{ __('nav_program_management') }}</span>
                        </a>
                        <a href="{{ route('admin.generations.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.generations.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>គ្រប់គ្រងជំនាន់</span>
                        </a>
                        <a href="{{ route('admin.manage-courses') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.manage-courses') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.206 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.794 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.794 5 16.5 5c1.706 0 3.332.477 4.5 1.253v13C19.832 18.477 18.206 18 16.5 18c-1.706 0-3.332.477-4.5 1.253"/></svg>
                            <span>{{ __('nav_course_management') }}</span>
                        </a>
                        <a href="{{ route('admin.rooms.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.rooms.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                            <span>{{ __('nav_room_management') }}</span>
                        </a>
                        <a href="{{ route('admin.manage-course-offerings') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.manage-course-offerings') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>{{ __('nav_course_offering_management') }}</span>
                        </a>
                    </div>
                </div>

                {{-- User Management Section --}}
                @php
                    $userRoutes = ['admin.manage-users', 'admin.create-user', 'admin.show-user', 'admin.edit-user', 'admin.import.*', 'admin.users.export'];
                    $isUserMgmtOpen = in_array(request()->route()->getName() ?? '', $userRoutes);
                @endphp
                <div x-data="{
                    open: $persist(false).as('nav_user_mgmt_open'),
                    init() {
                        if (localStorage.getItem('nav_user_mgmt_open') === null) {
                            this.open = @json($isUserMgmtOpen);
                        }
                    }
                }" class="mt-2">
                    <button @click="open = !open" 
                            class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all duration-200 {{ $isUserMgmtOpen ? 'bg-green-500/10 text-green-400' : 'text-slate-400 hover:bg-slate-700/40 hover:text-slate-300' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="flex-1 text-left">គ្រប់គ្រងអ្នកប្រើប្រាស់</span>
                        <svg class="w-4 h-4 shrink-0 transition-transform duration-300 ease-out" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="mt-1 ml-2 border-l-2 border-slate-700/60 pl-2 space-y-0.5">
                        @php
                            $tabRoutes = ['admin.manage-users'];
                            $isTabActive = request()->routeIs($tabRoutes);
                        @endphp
                        <a href="{{ route('admin.manage-users', ['tab' => 'admins']) }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->query('tab') === 'admins' ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.001 12.001 0 002.944 12c.045 4.02 1.325 7.625 3.844 10.323l.115.115a.997.997 0 001.414 0l.115-.115c2.519-2.698 3.799-6.303 3.844-10.323a12.001 12.001 0 00-2.67-8.984z"/></svg>
                            <span>{{ __('role_admin') }}</span>
                        </a>
                        <a href="{{ route('admin.manage-users', ['tab' => 'professors']) }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->query('tab') === 'professors' ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14"/></svg>
                            <span>{{ __('nav_professors') }}</span>
                        </a>
                        <a href="{{ route('admin.manage-users', ['tab' => 'students']) }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->query('tab') === 'students' ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            <span>{{ __('role_student') }}</span>
                        </a>
                        <div class="h-px bg-slate-700/50 my-1"></div>
                        <a href="{{ route('admin.create-user') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.create-user') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ __('nav_create_user') }}</span>
                        </a>
                        <a href="{{ route('admin.import.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.import.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>នាំចូលអ្នកប្រើប្រាស់</span>
                        </a>
                    </div>
                </div>

                {{-- Grades & Attendance --}}
                <div class="mt-3 space-y-0.5">
                    <div class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>វិនិច្ឆ័យ</span>
                    </div>
                    <a href="{{ route('admin.grades.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.grades.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>{{ __('ពិន្ទុសិស្ស') }}</span>
                    </a>
                    <a href="{{ route('admin.attendance.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.attendance.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>{{ __('វត្តមានសិស្ស') }}</span>
                    </a>
                    {{--
                    <a href="{{ route('admin.progression.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.progression.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>ជំនាន់និស្សិត</span>
                    </a>
                    --}}
                </div>

                {{-- Announcements --}}
                <div class="mt-3 space-y-0.5">
                    <div class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.899a9 9 0 010 12.728M5.88 15.828l2.585-2.585M13.414 7.05l-2.585 2.585M12 12h.01M3 3l.707.707M20.293 3.707l-.707.707M3 21l.707-.707M20.293 20.293l-.707-.707"/></svg>
                        <span>ផ្សេងៗ</span>
                    </div>
                    <a href="{{ route('admin.announcements.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('admin.announcements.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.899a9 9 0 010 12.728M5.88 15.828l2.585-2.585M13.414 7.05l-2.585 2.585M12 12h.01M3 3l.707.707M20.293 3.707l-.707.707M3 21l.707-.707M20.293 20.293l-.707-.707"/></svg>
                        <span>{{ __('nav_announcement') }}</span>
                    </a>
                </div>
            @endif

            {{-- ==================== PROFESSOR LINKS ==================== --}}
            @if($user->role === 'professor')
                <div class="mt-3 space-y-0.5">
                    <div class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>{{ __('nav_for_professors') }}</span>
                    </div>
                    <a href="{{ route('professor.profile.show') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('professor.profile.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.93 1.327 6.379 3.804M15 9a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ __('nav_profile') }}</span>
                    </a>
                    <a href="{{ route('professor.my-course-offerings') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('professor.my-course-offerings') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.206 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.794 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.794 5 16.5 5c1.706 0 3.332.477 4.5 1.253v13C19.832 18.477 18.206 18 16.5 18c-1.706 0-3.332.477-4.5 1.253"/></svg>
                        <span>{{ __('nav_my_teaching') }}</span>
                    </a>
                    <a href="{{ route('professor.notifications.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('professor.notifications.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <div class="relative">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C8.67 6.165 8 7.388 8 8.75V14.158c0 .53-.211 1.039-.595 1.437L6 17h5m4 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if($totalUnread > 0)
                                <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-1 shadow-lg">{{ $totalUnread > 99 ? '99+' : $totalUnread }}</span>
                            @endif
                        </div>
                        <span>{{ __('nav_notifications') }}</span>
                    </a>
                    <a href="{{ route('professor.my-schedule') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('professor.my-schedule') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ __('nav_my_schedule') }}</span>
                    </a>
                    <a href="{{ route('professor.attendance.history') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('professor.attendance.history') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ __('nav_attendance_history') }}</span>
                    </a>
                </div>
            @endif

            {{-- ==================== STUDENT LINKS ==================== --}}
            @if($user->role === 'student')
                <div class="mt-3 space-y-0.5">
                    <div class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        <span>{{ __('nav_for_students') }}</span>
                    </div>

                    <a href="{{ route('student.profile.show') }}"
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('student.profile.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.93 1.327 6.379 3.804M15 9a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ __('nav_profile') }}</span>
                    </a>
                    <a href="{{ route('student.my-grades') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('student.my-grades') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>{{ __('nav_my_grades') }}</span>
                    </a>
                    <a href="{{ route('student.my-assessments') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('student.my-assessments') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>{{ __('ពិន្ទុការវាយតម្លៃ') }}</span>
                    </a>
                    <a href="{{ route('student.my-attendance') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('student.my-attendance') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ __('nav_my_attendance') }}</span>
                    </a>
                    <a href="{{ route('student.my-schedule') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('student.my-schedule') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ __('nav_my_schedule') }}</span>
                    </a>
                    <a href="{{ route('student.my-enrolled-courses') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('student.my-enrolled-courses') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>{{ __('nav_enrolled_courses') }}</span>
                    </a>
                    <a href="{{ route('student.rooms.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('student.rooms.*') ? 'bg-green-600 text-white shadow-md shadow-green-600/20' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75V20a1 1 0 001 1h16a1 1 0 001-1V9.75M12 3l8.485 6.364a1 1 0 01-1.414 1.414L12 5.828 4.929 11.778a1 1 0 01-1.414-1.414L12 3z"/></svg>
                        <span>{{ __('nav_rooms') }}</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="shrink-0 px-3 py-3 border-t border-slate-700/50">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-slate-700/60 hover:text-white transition-all">
                    <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center text-xs font-bold bg-gradient-to-br from-green-500 to-emerald-600 shrink-0">
                        @if($profileUrl)
                            <img src="{{ $profileUrl }}" alt="" class="object-cover w-full h-full">
                        @else
                            {{ Str::substr($user->name, 0, 1) }}
                        @endif
                    </div>
                    <span class="truncate flex-1 text-left">{{ $user->name }}</span>
                    <svg class="w-4 h-4 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-transition x-cloak 
                     class="absolute bottom-full left-0 right-0 mb-2 bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-300 hover:bg-slate-700 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ __('nav_my_account') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            {{ __('nav_logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endauth
</nav>

<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #059669; }
</style>

<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-database-compat.js"></script>
<script>
    if (typeof firebaseConfig === 'undefined') {
        var firebaseConfig = {
            apiKey: "AIzaSyC5QgFzC-Kuudj7mWxLPf58xmoe_feXF3o",
            authDomain: "classmanagementsystem-cd57f.firebaseapp.com",
            databaseURL: "https://classmanagementsystem-cd57f-default-rtdb.firebaseio.com/",
            projectId: "classmanagementsystem-cd57f",
        };
        firebase.initializeApp(firebaseConfig);
    }
    var database = firebase.database();
    if (!window.sharedPageLoadTime) {
        window.sharedPageLoadTime = Math.floor(Date.now() / 1000);
    }

    ['faculties_sync', 'rooms_sync', 'departments_sync'].forEach(function(ref) {
        database.ref(ref).on('value', function(snapshot) {
            var data = snapshot.val();
            if (data && data.updated_at > window.sharedPageLoadTime) {
                window.dispatchEvent(new CustomEvent('firebase-message', {
                    detail: { message: data.message || 'ទិន្នន័យត្រូវបានធ្វើបច្ចុប្បន្នភាព' }
                }));
            }
        });
    });
</script>

<div 
    x-data="{
        show: false, msg: '', timer: 10, max: 10, interval: null,
        open(message) { this.msg = message; this.show = true; this.start(); },
        close() { this.show = false; if (this.interval) clearInterval(this.interval); },
        start() {
            this.timer = this.max;
            if (this.interval) clearInterval(this.interval);
            this.interval = setInterval(() => { this.timer--; if (this.timer <= 0) this.refresh(); }, 1000);
        },
        refresh() { if (typeof Livewire !== 'undefined') Livewire.dispatch('refreshComponent'); this.close(); window.sharedPageLoadTime = Math.floor(Date.now() / 1000); }
    }"
    x-on:firebase-message.window="open($event.detail.message)"
    x-cloak
    class="fixed top-4 left-4 right-4 md:left-auto md:top-6 md:right-6 md:w-[320px] z-[9999]"
>
    <div x-show="show" x-transition:enter="transition transform ease-out duration-300" x-transition:enter-start="translate-y-[-20px] opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="bg-white/95 border border-green-400/30 rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center gap-3 p-4">
            <div class="flex-shrink-0 bg-green-500/10 p-2 rounded-full">
                <svg class="w-5 h-5 text-green-600 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800 leading-tight truncate" x-text="msg"></p>
                <p class="text-[10px] text-gray-500 mt-0.5">សូមរង់ចាំ <span class="text-green-600 font-bold" x-text="timer"></span>វិនាទី</p>
            </div>
            <button @click="close()" class="flex-shrink-0 text-gray-400 hover:text-red-500 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="h-1 bg-gray-100">
            <div class="h-full bg-green-500 transition-all duration-1000 ease-linear" :style="'width: ' + ((timer / max) * 100) + '%'"></div>
        </div>
    </div>
</div>
