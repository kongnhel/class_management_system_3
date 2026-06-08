{{-- 
    NOTE: The Font Awesome CDN link has been removed. 
    All necessary icons are now inline SVGs for better performance and consistency.
--}}

{{-- <nav class="fixed top-0 left-0 h-screen w-64 lg:w-72 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 border-r border-gray-200 dark:border-gray-800 shadow-xl transform ... transition-transform duration-300 ease-in-out z-50 font-['Battambang']">
    "
    :class="{ 'translate-x-0': open, '-translate-x-full': !open }"
> --}}
<nav 
    class="
        fixed top-0 left-0 h-screen 
        lg:w-64 
         w-64 
        lg:m-1 
        bg-white dark:bg-gray-900 
        text-gray-800 dark:text-gray-100
        border-r border-gray-200 dark:border-gray-800
        shadow-xl 
        transform -translate-x-full lg:translate-x-0
        transition-all duration-300 ease-in-out
        z-50
        font-['Battambang']
    "
    :class="{ 'translate-x-0': open, '-translate-x-full': !open }"
>
@auth
@php
    $user = Auth::user();
    
    // ១. ទាញយក URL រូបភាព (ប្រើ URL ពី ImgBB ដោយផ្ទាល់)
    // លុប asset('storage/' . ...) ចេញ ដើម្បីកុំឱ្យមាន Error 403
    $profileUrl = $user->userProfile?->profile_picture_url ?? $user->studentProfile?->profile_picture_url;

    // ២. កំណត់អត្ថបទ Role
    $roleText = match ($user->role) {
        'admin' => __('role_admin'),
        'professor' => __('role_professor'),
        'student' => __('role_student'),
        default => ''
    };

    // ៣. បន្ថែម Logic ឆែកមើលថាជាប្រធានថ្នាក់ឬអត់
    $isClassLeader = false;
    if($user->role === 'student') {
        $isClassLeader = \DB::table('student_course_enrollments')
            ->where('student_user_id', $user->id)
            ->where('is_class_leader', 1)
            ->exists();
    }
@endphp
    <div class="flex flex-col h-full p-6 ">
        

{{-- Reactive Notification (Auto-update, No Button) --}}


        {{-- Profile Section (Top) --}}
      <div class="shrink-0 flex flex-col items-center justify-center py-8 border-b border-gray-700/50 mb-8">
    <a href="{{ route('profile.edit') }}" class="flex flex-col items-center space-y-2">
        <div class="mt-4 relative group">
            <div class="h-20 w-20 rounded-full overflow-hidden flex items-center justify-center text-3xl font-bold bg-gradient-to-br from-green-500 to-green-700 ring-4 ring-green-500 shadow-lg transition-transform hover:scale-105">
    @if($profileUrl)
        {{-- បង្ហាញរូបភាពពី ImgBB ដោយផ្ទាល់ --}}
        <img src="{{ $profileUrl }}" 
             alt="{{ __('nav_profile_picture') }}" 
             class="h-full w-full object-cover">
    @else
        {{-- បង្ហាញអក្សរកាត់ក្នុងករណីមិនមានរូបភាព --}}
        <span class="text-3xl font-bold">
            {{ Str::upper(Str::substr($user->name, 0, 1)) }}
        </span>
    @endif
            </div>

            {{-- User Role & Class Leader Badge --}}
            <div class="absolute bottom-0 right-0 transform translate-x-1/4 translate-y-1/4 flex flex-col items-end space-y-1">
                {{-- បង្ហាញពាក្យថា ប្រធានថ្នាក់ បើលក្ខខណ្ឌត្រូវ --}}
                @if($isClassLeader)
                    <span class="text-[10px] font-bold uppercase text-white px-2 py-0.5 bg-yellow-600 rounded-full shadow-md ring-1 ring-white">
                        {{ __('role_class_leader') }}
                    </span>
                @endif

                {{-- បង្ហាញ Role ធម្មតា (និស្សិត/សាស្ត្រាចារ្យ) --}}
                @if($roleText)
                    <span class="text-xs font-semibold uppercase text-green-300 px-3 py-1 bg-gray-800/80 rounded-full shadow-md">
                        {{ $roleText }}
                    </span>
                @endif
            </div>
        </div>
    </a>
</div>
{{-- @auth
    <x-nav-link :href="route('ai.chat')" :active="request()->routeIs('ai.chat')"
        class="flex items-center px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700 hover:text-green-300">
        <svg class="h-6 w-6 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        <span>{{ __('សួរ AI ជំនួយការ') }}</span>
    </x-nav-link>
@endauth --}}

<div 
    x-data="{
    activeTab: $persist('admins').as('user_manage_tab'),

    init() {
        // ២. ឆែកមើលក្នុង URL បើមាន Parameter 'tab' គឺត្រូវយកតម្លៃនោះមកប្រើជំនួស $persist
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            this.activeTab = tabParam;
        }
    },
        show: false,
        msg: '',
        timer: 10,
        max: 10,
        interval: null,
        open(message) {
            this.msg = message;
            this.show = true;
            this.start();
        },
        close() {
            this.show = false;
            if (this.interval) clearInterval(this.interval);
        },
        start() {
            this.timer = this.max;
            if (this.interval) clearInterval(this.interval);
            this.interval = setInterval(() => {
                this.timer--;
                if (this.timer <= 0) {
                    this.refresh();
                }
            }, 1000);
        },
        refresh() {
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('refreshComponent');
            }
            this.close();
            window.sharedPageLoadTime = Math.floor(Date.now() / 1000);
        }
    }"
    x-on:firebase-message.window="open($event.detail.message)"
    x-cloak
    class="fixed top-4 left-4 right-4 md:left-auto md:top-6 md:right-6 md:w-[320px] z-[9999]"
>
    <div 
        x-show="show"
        x-transition:enter="transition transform ease-out duration-300"
        x-transition:enter-start="translate-y-[-20px] md:translate-x-10 opacity-0"
        x-transition:enter-end="translate-y-0 md:translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="bg-white/95 dark:bg-gray-900/95 border border-green-400/30 rounded-2xl shadow-2xl overflow-hidden ring-1 ring-black/5"
    >
        <div class="flex items-center gap-3 p-4">
            <div class="flex-shrink-0 bg-green-500/10 p-2 rounded-full">
                <svg class="w-5 h-5 text-green-600 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800 dark:text-gray-100 leading-tight truncate" x-text="msg"></p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ __('refresh_in') }} <span class="text-green-600 font-bold" x-text="timer"></span>s
                </p>
            </div>

            <button @click="close()" class="flex-shrink-0 ml-2 text-gray-400 hover:text-red-500 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="h-1 bg-gray-100 dark:bg-gray-800">
            <div 
                class="h-full bg-green-500 transition-all duration-1000 ease-linear"
                :style="'width: ' + ((timer / max) * 100) + '%'"
            ></div>
        </div>
    </div>
</div>

{{-- Theme Toggle Switch --}}
    {{-- <div class="mb-6 px-2">
        <button @click="toggleTheme()" 
            class="flex items-center justify-between w-full p-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300">
            <div class="flex items-center">
                <template x-if="!darkMode">
                    <svg class="w-5 h-5 text-yellow-500" ...></svg>
                </template>
                <template x-if="darkMode">
                    <svg class="w-5 h-5 text-blue-400" ...></svg>
                </template>
                <span class="ml-3 text-sm font-medium" x-text="darkMode ? 'ពន្លឺ' : 'ងងឹត'"></span>
            </div>
            
            <div class="w-10 h-5 bg-gray-300 dark:bg-gray-600 rounded-full relative">
                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform duration-300"
                    :class="darkMode ? 'translate-x-5' : 'translate-x-0'"></div>
            </div>
        </button>
    </div> --}}
        {{-- Navigation Links Section --}}
        <div class="flex-1 space-y-4 overflow-y-auto custom-scrollbar ">
            
            
            {{-- Dashboard Link --}}
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('dashboard') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>{{ __('nav_dashboard') }}</span>
            </x-nav-link>

            {{-- --- ADMIN LINKS --- --}}
            @if($user->role === 'admin')
                <p class="text-xs font-semibold uppercase text-gray-400 px-5 pt-6 pb-3 tracking-wide">{{ __('nav_management') }}</p>

                {{-- Logic for Admin User Dropdown State --}}
                @php
                    $adminUserRoutes = ['admin.manage-users', 'admin.create-user'];
                    $isAdminUserActive = request()->routeIs($adminUserRoutes);
                @endphp

                {{-- Dropdown for Admin Users --}}
<div x-data="{ adminUserDropdownOpen: {{ $isAdminUserActive ? 'true' : 'false' }} }" class="relative">
    <button @click="adminUserDropdownOpen = !adminUserDropdownOpen"
            aria-controls="admin-user-submenu"
            :aria-expanded="adminUserDropdownOpen"
            class="flex items-center justify-between w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ $isAdminUserActive ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
        <div class="flex items-center">
            {{-- Users Cog SVG --}}
            <svg class="h-6 w-6 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span>{{ __('nav_user_management') }}</span>
        </div>
        <svg class="h-5 w-5 transform transition-transform duration-200" :class="{ 'rotate-180': adminUserDropdownOpen, 'rotate-0': !adminUserDropdownOpen }" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>
    
    <div x-show="adminUserDropdownOpen" 
         id="admin-user-submenu" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95"
         class="mt-2 space-y-1 ps-10">
        
        {{-- Admin Tab Link --}}
        <a href="{{ route('admin.manage-users', ['tab' => 'admins']) }}" 
           class="flex items-center w-full px-4 py-2.5 text-sm font-medium rounded-lg transition duration-200 {{ request()->query('tab') === 'admins' ? 'text-green-400 bg-gray-700/50' : 'text-gray-400 hover:text-green-300 hover:bg-gray-700/30' }}">
            <svg class="h-4 w-4 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.001 12.001 0 002.944 12c.045 4.02 1.325 7.625 3.844 10.323l.115.115a.997.997 0 001.414 0l.115-.115c2.519-2.698 3.799-6.303 3.844-10.323a12.001 12.001 0 00-2.67-8.984z"></path></svg>
            {{ __('role_admin') }}
        </a>
        
        {{-- Professor Tab Link --}}
        <a href="{{ route('admin.manage-users', ['tab' => 'professors']) }}" 
           class="flex items-center w-full px-4 py-2.5 text-sm font-medium rounded-lg transition duration-200 {{ request()->query('tab') === 'professors' ? 'text-green-400 bg-gray-700/50' : 'text-gray-400 hover:text-green-300 hover:bg-gray-700/30' }}">
            <svg class="h-4 w-4 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H14"></path></svg>
            {{ __('nav_professors') }}
        </a>
        
        {{-- Student Tab Link --}}
        <a href="{{ route('admin.manage-users', ['tab' => 'students']) }}" 
           class="flex items-center w-full px-4 py-2.5 text-sm font-medium rounded-lg transition duration-200 {{ request()->query('tab') === 'students' ? 'text-green-400 bg-gray-700/50' : 'text-gray-400 hover:text-green-300 hover:bg-gray-700/30' }}">
            <svg class="h-4 w-4 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
            {{ __('role_student') }}
        </a>
        
        <div class="h-px bg-gray-700/50 my-2 mx-4"></div>

        {{-- Create User Link --}}
        <a href="{{ route('admin.create-user') }}" 
           class="flex items-center w-full px-4 py-2.5 text-sm font-medium rounded-lg transition duration-200 {{ request()->routeIs('admin.create-user') ? 'text-green-400 bg-gray-700/50' : 'text-gray-400 hover:text-green-300 hover:bg-gray-700/30' }}">
            <svg class="h-4 w-4 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ __('nav_create_user') }}
        </a>
    </div>
</div>

                {{-- Admin - Other Links --}}
                <x-nav-link :href="route('admin.academic-years.index')" :active="request()->routeIs('admin.academic-years.*')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.academic-years.*') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>{{ __('ឆ្នាំសិក្សា') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.settings.*') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>{{ __('ការកំណត់') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.grades.index')" :active="request()->routeIs('admin.grades.*')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.grades.*') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>{{ __('ពិន្ទុសិស្ស') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.attendance.index')" :active="request()->routeIs('admin.attendance.*')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.attendance.*') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span>{{ __('វត្តមានសិស្ស') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.import.index')" :active="request()->routeIs('admin.import.*')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.import.*') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span>{{ __('នាំចូលទិន្នន័យ') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.audit-logs.index')" :active="request()->routeIs('admin.audit-logs.*')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.audit-logs.*') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span>{{ __('កំណត់ត្រាសកម្មភាព') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.announcements.index')" :active="request()->routeIs('admin.announcements.index')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.announcements.index') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    {{-- Announcement/Megaphone SVG --}}
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.899a9 9 0 010 12.728M5.88 15.828l2.585-2.585M13.414 7.05l-2.585 2.585M12 12h.01M3 3l.707.707M20.293 3.707l-.707.707M3 21l.707-.707M20.293 20.293l-.707-.707"></path></svg>
                    <span>{{ __('nav_announcement') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.rooms.index')" :active="request()->routeIs('admin.rooms.index')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.rooms.index') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    {{-- Rooms/Building Icon (Simplified) --}}
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                    <span>{{ __('nav_room_management') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.manage-faculties')" :active="request()->routeIs('admin.manage-faculties')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.manage-faculties') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 8h6M9 12h6M9 16h6"></path></svg>
                    <span>{{ __('nav_faculty_management') }}</span>
                </x-nav-link>
                
                <x-nav-link :href="route('admin.manage-departments')" :active="request()->routeIs('admin.manage-departments')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.manage-departments') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>{{ __('nav_department_management') }}</span>
                </x-nav-link>
                
                <x-nav-link :href="route('admin.manage-programs')" :active="request()->routeIs('admin.manage-programs')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.manage-programs') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    <span>{{ __('nav_program_management') }}</span>
                </x-nav-link>
                
                <x-nav-link :href="route('admin.manage-courses')" :active="request()->routeIs('admin.manage-courses')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.manage-courses') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.206 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.794 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.794 5 16.5 5c1.706 0 3.332.477 4.5 1.253v13C19.832 18.477 18.206 18 16.5 18c-1.706 0-3.332.477-4.5 1.253"></path></svg>
                    <span>{{ __('nav_course_management') }}</span>
                </x-nav-link>
                
                <x-nav-link :href="route('admin.manage-course-offerings')" :active="request()->routeIs('admin.manage-course-offerings')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.manage-course-offerings') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>{{ __('nav_course_offering_management') }}</span>
                </x-nav-link>
                
                {{-- <x-nav-link :href="route('admin.enroll_student_form')" :active="request()->routeIs('admin.enroll_student_form')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('admin.enroll_student_form') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 14c-1.49 0-3-.64-3-1.5S10.51 11 12 11s3 .64 3 1.5-1.51 1.5-3 1.5z"></path></svg>
                    <span>{{ __('ចុះឈ្មោះសិស្ស') }}</span>
                </x-nav-link> --}}
            @endif

            {{-- --- PROFESSOR LINKS --- --}}
            @if($user->role === 'professor')
                <p class="text-xs font-semibold uppercase text-gray-400 px-5 pt-6 pb-3 tracking-wide">{{ __('nav_for_professors') }}</p>
                
                <x-nav-link :href="route('professor.profile.show')" :active="request()->routeIs('professor.profile.index', 'professor.profile.edit')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('profile.show', 'profile.edit') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    
                    <svg class="h-6 w-6 me-3 text-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.93 1.327 6.379 3.804M15 9a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>{{ __('nav_profile') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('professor.my-course-offerings')" :active="request()->routeIs('professor.my-course-offerings')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('professor.my-course-offerings') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.206 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.794 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.794 5 16.5 5c1.706 0 3.332.477 4.5 1.253v13C19.832 18.477 18.206 18 16.5 18c-1.706 0-3.332.477-4.5 1.253"></path></svg>
                    <span>{{ __('nav_my_teaching') }}</span>
                </x-nav-link>
                    
                <x-nav-link :href="route('professor.notifications.index')" 
                    :active="request()->routeIs('professor.notifications.index')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('professor.notifications.index') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    {{-- Bell SVG --}}
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C8.67 6.165 8 7.388 8 8.75V14.158c0 .53-.211 1.039-.595 1.437L6 17h5m4 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>{{ __('nav_notifications') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('professor.my-schedule')" :active="request()->routeIs('professor.my-schedule')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('professor.my-schedule') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    {{-- Calendar SVG --}}
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ __('nav_my_schedule') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('professor.attendance.history')" 
                            :active="request()->routeIs('professor.attendance.history')"
                            class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out 
                            {{ request()->routeIs('professor.attendance.history') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">

                    {{-- History / Attendance Icon --}}
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 8v4l3 3m6-3a9 9 0 01-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M19 19l-4-4m0 0l-4 4m4-4v-7" />
                    </svg>
                    
                    <span>{{ __('nav_attendance_history') }}</span>
                </x-nav-link>
                
            @endif

            {{-- --- STUDENT LINKS --- --}}
            @if($user->role === 'student')
                <p class="text-xs font-semibold uppercase text-gray-400 px-5 pt-6 pb-3 tracking-wide">{{ __('nav_for_students') }}</p>
                              @if($isClassLeader)
                    <a href="{{ route('student.my-enrolled-courses') }}" 
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl border border-yellow-500/30 bg-yellow-500/5 hover:bg-yellow-500/20 hover:text-yellow-300 transition duration-200 ease-in-out mb-2 cursor-pointer {{ request()->routeIs('student.leader.*') ? 'bg-yellow-600 text-white shadow-lg border-none' : 'text-yellow-500' }}">
                        
                        <svg class="h-6 w-6 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="font-bold">{{ __('nav_student_attendance') }}</span>
                    </a>
                @endif

                <x-nav-link :href="route('student.profile.show')" :active="request()->routeIs('student.profile.show', 'student.profile.edit')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('student.profile.show', 'student.profile.edit') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    {{-- User Circle SVG (Replaces fas fa-user-circle) --}}
                    <svg class="h-6 w-6 me-3 text-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.93 1.327 6.379 3.804M15 9a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>{{ __('nav_profile') }}</span>
                </x-nav-link>
      {{-- ប្តូរពី <x-nav-link> មកជា <a> ធម្មតាវិញ --}}
  
                <x-nav-link :href="route('student.my-enrolled-courses')" :active="request()->routeIs('student.my-enrolled-courses')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('student.my-enrolled-courses') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    {{-- Clock/Enrolled Courses SVG --}}
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path></svg>
                    <span>{{ __('nav_enrolled_courses') }}</span>
                </x-nav-link>
    
                <x-nav-link :href="route('student.rooms.index')" 
                    :active="request()->routeIs('student.rooms.index')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('student.rooms.index') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    {{-- Home/Building SVG --}}
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75V20a1 1 0 001 1h16a1 1 0 001-1V9.75M12 3l8.485 6.364a1 1 0 01-1.414 1.414L12 5.828 4.929 11.778a1 1 0 01-1.414-1.414L12 3z" />
                    </svg>
                    <span>{{ __('nav_rooms') }}</span>
                </x-nav-link>
                
                <x-nav-link :href="route('student.my-schedule')" :active="request()->routeIs('student.my-schedule')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('student.my-schedule') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>{{ __('nav_my_schedule') }}</span>
                </x-nav-link>
                
                <x-nav-link :href="route('student.my-grades')" :active="request()->routeIs('student.my-grades')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('student.my-grades') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    {{-- Grades/Document SVG --}}
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>{{ __('nav_my_grades') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('student.my-attendance')" :active="request()->routeIs('student.my-attendance')"
                    class="flex items-center w-full px-5 py-3 text-base font-medium rounded-xl hover:bg-gray-700/80 hover:text-green-300 transition duration-200 ease-in-out {{ request()->routeIs('student.my-attendance') ? 'bg-green-700 text-white shadow-lg' : 'text-gray-200' }}">
                    {{-- Clock/Attendance SVG --}}
                    <svg class="h-6 w-6 me-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ __('nav_my_attendance') }}</span>
                </x-nav-link>
            @endif
        </div>

        {{-- Language Toggle --}}
        <div class="px-5 py-3 border-t border-gray-700/50">
            <div class="flex items-center justify-center space-x-2">
                <a href="{{ route('lang.switch', 'km') }}" 
                   class="px-3 py-1.5 text-sm font-bold rounded-lg transition-all duration-200 {{ app()->getLocale() === 'km' ? 'bg-green-600 text-white shadow-lg' : 'text-gray-400 hover:text-green-300 hover:bg-gray-700/50' }}">
                    KM
                </a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('lang.switch', 'en') }}" 
                   class="px-3 py-1.5 text-sm font-bold rounded-lg transition-all duration-200 {{ app()->getLocale() === 'en' ? 'bg-green-600 text-white shadow-lg' : 'text-gray-400 hover:text-green-300 hover:bg-gray-700/50' }}">
                    EN
                </a>
            </div>
        </div>

        {{-- Footer Dropdown (User Account Management) --}}
        <div class="mt-auto pt-8 border-t border-gray-700/50">
            <x-dropdown align="bottom-right" width="56">
                <x-slot name="trigger">
                    {{-- Added focus:ring for accessibility --}}
                    <button class="flex items-center justify-between w-full px-5 py-3 text-base font-medium rounded-xl text-white bg-gray-800/80 hover:bg-gray-700/80 focus:bg-gray-700/80 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-200 ease-in-out shadow-lg">
                        <div class="flex items-center min-w-0 flex-1">
                            {{-- Profile Picture or First Letter Display (using $profileUrl from top) --}}
                            <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center text-sm font-bold me-3 bg-gradient-to-br from-green-500 to-green-700 shrink-0">
                                @if($profileUrl)
                                    <img src="{{ $profileUrl }}" alt="{{ __('nav_profile_picture') }}" class="object-cover w-full h-full">
                                @else
                                    {{ Str::substr($user->name, 0, 1) }}
                                @endif
                            </div>
                            <span class="truncate">{{ $user->name }}</span>
                        </div>
                        <svg class="ms-2 h-5 w-5 shrink-0 fill-current transition-transform" x-bind:class="{ 'rotate-180': open }" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="absolute right-0 bottom-full mb-3 w-56 rounded-xl shadow-xl bg-white ring-1 ring-gray-200/50">
                        <x-dropdown-link :href="route('profile.edit')" class="block px-5 py-3 text-sm text-gray-800 hover:bg-green-50 hover:text-green-900 rounded-xl transition duration-150">
                            {{ __('nav_my_account') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="block px-5 py-3 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 rounded-xl transition duration-150">
                                {{ __('nav_logout') }}
                            </x-dropdown-link>
                        </form>
                        
                    </div>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
@endauth
</nav>
{{-- profile --}}
<style>
    /* Scrollbar styles remain the same but use the default white color */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 12px;
    }

    /* Adjusted thumb color to green for theme consistency */
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgb(52, 211, 153); /* A shade of green */
        border-radius: 12px;
        transition: background 0.2s ease;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgb(16, 185, 129); /* Darker green on hover */
    }

    /* Prevent flicker on page load */
    [x-cloak] { display: none !important; }

    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { 
        background: #10b981; 
        border-radius: 10px; 
    }
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
    
    // កំណត់ម៉ោងដែល Page ចាប់ផ្ដើម Load
    if (!window.sharedPageLoadTime) {
        window.sharedPageLoadTime = Math.floor(Date.now() / 1000);
    }

    // --- Faculty Sync ---
    database.ref('faculties_sync').on('value', (snapshot) => {
        const data = snapshot.val();
        // ឆែកមើលថា តើទិន្នន័យថ្មីនេះ កើតឡើងក្រោយពេលយើង Load Page ឬអត់?
        if (data && data.updated_at > window.sharedPageLoadTime) {
            window.dispatchEvent(new CustomEvent('firebase-message', {
                detail: { message: data.message || 'មានការកែប្រែទិន្នន័យមហាវិទ្យាល័យ' }
            }));
        }
    });

    // --- Rooms Sync ---
    database.ref('rooms_sync').on('value', (snapshot) => {
        const data = snapshot.val();
        // បន្ថែមលក្ខខណ្ឌ updated_at ដូច Faculty ដែរ ដើម្បីកុំឱ្យវា Alert ផ្ដេសផ្ដាស
        if (data && data.updated_at > window.sharedPageLoadTime) {
            window.dispatchEvent(new CustomEvent('firebase-message', {
                detail: { message: data.message || 'មានការកែប្រែទិន្នន័យបន្ទប់ថ្មី' }
            }));
        }
    });

    // Departmenrt Sync
    database.ref('departments_sync').on('value', (snapshot) => {
        const data = snapshot.val();
        if (data && data.updated_at > window.sharedPageLoadTime) {
            window.dispatchEvent(new CustomEvent('firebase-message', {
                detail: { message: data.message || 'មានការកែប្រែទិន្នន័យដេប៉ាតឺម៉ង់ថ្មី' }
            }));
        }
    }); 

// database.ref('rooms_sync').on('value', (snapshot) => {
//     const data = snapshot.val();
//     if (data) {
//         // បាញ់ Event ទៅឱ្យ Livewire
//         Livewire.dispatch('refreshComponent'); 
        
//         // បង្ហាញ Alert ប្រាប់អ្នកប្រើប្រាស់
//         showNotification(data.message || 'ទិន្នន័យបន្ទប់ត្រូវបានធ្វើបច្ចុប្បន្នភាព');
//     }
</script>