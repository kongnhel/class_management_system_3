<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
          open: false, 
          darkMode: localStorage.getItem('theme') === 'dark',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
          }
      }" 
      :class="{ 'dark': darkMode }">
    <head>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        

        <link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
        <title>{{ config('', 'Class Management System') }}</title>
        @livewireStyles
        
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@100;300;400;700;900&display=swap" rel="stylesheet">
        

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body, div {
                font-family: 'Battambang', sans-serif;
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            /* Dark Mode Styles */
            .dark body { background-color: #111827; color: #f3f4f6; }
            .dark .bg-white { background-color: #1f2937 !important; color: #ffffff; }
            .dark .bg-light-100 { background-color: #111827 !important; }
            .dark .text-light-800 { color: #f3f4f6 !important; }
            .dark .border-light-200 { border-color: #374151 !important; }

            .custom-scrollbar::-webkit-scrollbar { width: 8px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.3); border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.5); }

            @media print {
                nav, header, footer, .lg\:hidden, .no-print, .theme-toggle-btn { display: none !important; }
                main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
                @page { margin: 15mm; size: A4 portrait; }
            }
        </style>
    </head>
    @livewireScripts

    <body class="font-sans antialiased text-gray-900">

    @auth
        @php
            $user = Auth::user()->loadMissing('userProfile');
            $profilePath = $user->userProfile?->profile_picture_url;
            $profileUrl = $profilePath ? asset('storage/' . $profilePath) : null;
            $roleText = match ($user->role) {
                'admin' => __('អ្នកគ្រប់គ្រង'),
                'professor' => __('សាស្ត្រាចារ្យ'),
                'student' => __('និស្សិត'),
                default => ''
            };
        @endphp
    @endauth

    {{-- ប្តូរ bg មកជាពណ៌ Light ទាំងស្រុង និងដក Dark Mode ចេញ --}}
    <div class="min-h-screen bg-[#f8fafc]">

        {{-- Sidebar --}}
        @include('layouts.navigation')

        {{-- Mobile Top Bar - កែឱ្យមកជាពណ៌សស្អាត --}}
        <div class="lg:hidden fixed top-0 left-0 w-full bg-white border-b border-gray-100 shadow-sm z-40 p-3 flex justify-between items-center font-['Battambang']">
            
            {{-- ប៊ូតុង Hamburger --}}
            <button @click.stop="open = true" x-cloak class="inline-flex items-center justify-center p-2 rounded-xl text-gray-500 hover:bg-gray-50 focus:outline-none transition">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            <div class="flex items-center space-x-3 ml-auto">
    @auth
        {{-- បន្ថែមស្លាក <a> ដើម្បីឱ្យចុចបានទាំងឈ្មោះ និងរូបភាព --}}
        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
            
            {{-- ឈ្មោះអ្នកប្រើប្រាស់ (បង្ហាញតែលើ Desktop) --}}
            <div class="flex flex-col items-end leading-tight me-2 sm:block">
                <span class="text-sm font-bold text-gray-800">{{ $user->name }}</span>
                @if($roleText) 
                    <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">{{ $roleText }}</span> 
                @endif
            </div>
            @php
    $profileUrl = $user->userProfile?->profile_picture_url ?? $user->studentProfile?->profile_picture_url;
@endphp


            {{-- រូបភាព Profile --}}
            <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center text-sm font-bold bg-white-600 text-white shadow-md shadow-blue-200 border border-white">
                @if($profileUrl) 
                    <img src="{{ $profileUrl }}" class="h-full w-full object-cover" alt="{{ $user->name }}">
                @else 
                    {{ Str::substr($user->name, 0, 1) }} 
                @endif
            </div>
        </a>
    @endauth
</div>
        </div>

        {{-- Overlay --}}
        <div x-show="open" 
             x-cloak
             x-transition.opacity 
             class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-40 lg:hidden" 
             @click="open = false"></div>

        {{-- Main Content Wrapper --}}
        {{-- កែ lg:ml-64 (Sidebar Width) និងដក max-w-7xl ចេញពី Header --}}
        <div class="flex flex-col min-h-screen lg:ml-64 pt-16 lg:pt-0">
            @isset($header)
                <header class="bg-white border-b border-gray-100">
                    {{-- ប្តូរ max-w-7xl ទៅ max-w-full ដើម្បីឱ្យ Header រត់ពេញអេក្រង់ដែរ --}}
                    <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Main Slot - ប្រើ w-full និងដក Dark Mode --}}
            {{-- <main class="flex-grow bg-[#f8fafc]">
                {{ $slot }}
            </main> --}}
            <main>
                {{-- បន្ថែម @yield ត្រង់នេះ ដើម្បីកុំឱ្យ Error $slot ទៀត --}}
                @yield('content') 

                {{-- រក្សាទុក $slot សម្រាប់ Component ផ្សេងទៀត --}}
                @if(isset($slot))
                    {{ $slot }}
                @endif
            </main>
        </div>
    </div>

    {{-- @livewireScripts --}}
</body>
</html>
