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
    /* Scrollbar សម្រាប់ Chat Box */
    #chat-box::-webkit-scrollbar { width: 4px; }
    #chat-box::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }

    /* Markdown Styling ឱ្យស្អាតក្នុង Sidebar */
    .prose pre { background: #1e293b !important; color: #f8fafc; padding: 1rem; border-radius: 8px; font-size: 12px; overflow-x: auto; }
    .prose code { color: #059669; font-weight: 600; background: #f0fdf4; padding: 0.1rem 0.2rem; border-radius: 4px; }
    
    /* Animation សម្រាប់ Sidebar */
    #ai-sidebar { transition: transform 0.3s ease-in-out; }
    #chat-overlay { transition: opacity 0.3s ease; }

    @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeInUp 0.4s ease-out forwards;
}
</style>
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
@auth
<div class="fixed bottom-6 right-6 flex flex-col items-end group z-[100]">
    
    <div class="mb-3 bg-white text-gray-800 px-4 py-2 rounded-2xl shadow-xl border border-gray-100 text-xs font-medium opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 pointer-events-none relative mr-2">
        សួរអ្វីមួយទៅកាន់ AI...
        <div class="absolute -bottom-1 right-4 w-2 h-2 bg-white border-r border-b border-gray-100 rotate-45"></div>
    </div>

    <button onclick="toggleAIChat()" class="relative bg-[#0084FF] text-white p-4 rounded-full shadow-[0_8px_25px_-5px_rgba(0,132,255,0.5)] hover:scale-110 active:scale-90 transition-all duration-300 flex items-center justify-center">
        
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.453 5.503 3.735 7.153V22l3.418-1.875c.915.254 1.883.391 2.847.391 5.523 0 10-4.145 10-9.258C22 6.145 17.523 2 12 2zm1.142 12.358l-2.571-2.742-5.014 2.742 5.513-5.858 2.657 2.742 4.928-2.742-5.513 5.858z"/>
        </svg>

        <div class="absolute bottom-0 right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>

        <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 border-2 border-white rounded-full flex items-center justify-center">
            <span class="text-[9px] font-bold text-white">AI</span>
        </div>
    </button>
</div>
<div id="chat-overlay" onclick="toggleAIChat()" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[90] hidden opacity-0 transition-opacity duration-300 ease-in-out"></div>

<!-- កែប្រែទទឹងត្រង់ sm:w-[600px] -->
<div id="ai-sidebar" class="fixed top-0 right-0 h-full w-full sm:w-[600px] bg-white shadow-[0_0_50px_-12px_rgba(0,0,0,0.3)] z-[100] transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col font-['Kantumruy_Pro','Inter']">
    
    <!-- Header ធំជាងមុន -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-500 p-6 text-white flex items-center justify-between shadow-md">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl leading-none">NMU Smart Assistant</h2>
                <span class="text-xs text-white/80">ប្រព័ន្ធគ្រប់គ្រងសាលា (កំពុងដំណើរការ)</span>
            </div>
        </div>
        <button onclick="toggleAIChat()" class="p-2 hover:bg-white/10 rounded-full transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Tabs ប្តូរទៅ text-sm ឬ text-base -->
    <div class="flex border-b border-gray-50 bg-gray-50/50 p-3 space-x-3">
        <button type="button" onclick="setOption('info')" id="btn-info" class="flex-1 py-3 rounded-xl text-sm bg-white shadow-sm text-green-600 border border-green-200 font-bold transition-all">ព័ត៌មានទូទៅ</button>
        <button type="button" onclick="setOption('process')" id="btn-process" class="flex-1 py-3 rounded-xl text-sm text-gray-500 hover:bg-white font-bold transition-all">របៀបប្រើប្រាស់</button>
    </div>

    <!-- Chat Box: ប្តូរអក្សរទៅ text-base (ធំជាងមុន) -->
    <div id="chat-box" class="flex-grow overflow-y-auto p-6 space-y-6 bg-[#f8fafc]">
        <div class="flex justify-start">
            <div class="bg-white border border-gray-100 text-gray-700 p-5 rounded-2xl rounded-tl-none shadow-sm text-base leading-relaxed">
                សួស្តីលោកគ្រូ/អ្នកគ្រូ **{{ Auth::user()->name }}**! <br>តើថ្ងៃនេះមានអ្វីឱ្យខ្ញុំជួយដែរទេ? 😊
            </div>
        </div>
    </div>

    <div id="thinking-indicator" class="hidden px-6 py-3 bg-gray-50 border-t border-gray-100">
        <span class="text-sm italic text-gray-500 animate-pulse">កំពុងគិត...</span>
    </div>

    <!-- Input Area: ធំជាងមុនងាយស្រួលវាយ -->
    <div class="p-6 bg-white border-t border-gray-100">
        <form id="chat-form" class="relative flex items-center gap-3">
            @csrf
            <input type="hidden" id="chat-option" value="info">
            <input type="text" id="user-input" autocomplete="off" 
                class="flex-grow bg-gray-50 border border-gray-200 rounded-2xl px-6 py-4 text-base outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all" 
                placeholder="សរសេរសំណួរនៅទីនេះ..." required>
            <button type="submit" class="bg-green-600 text-white p-4 rounded-xl hover:bg-green-700 active:scale-95 shadow-lg transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </form>
    </div>
</div>
@endauth
    </body>
</html>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        // ១. មុខងារបិទ/បើក Sidebar ជាមួយ Animation
        function toggleAIChat() {
            const sidebar = document.getElementById('ai-sidebar');
            const overlay = document.getElementById('chat-overlay');
            if (!sidebar || !overlay) return;

            if (sidebar.classList.contains('translate-x-full')) {
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    sidebar.classList.remove('translate-x-full');
                    overlay.classList.add('opacity-100');
                }, 10);
            } else {
                sidebar.classList.add('translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        // ២. មុខងារប្តូរ Option
        function setOption(option) {
            const chatOptionInput = document.getElementById('chat-option');
            const userInput = document.getElementById('user-input');
            const btnInfo = document.getElementById('btn-info');
            const btnProcess = document.getElementById('btn-process');
            if(!chatOptionInput || !userInput) return;

            chatOptionInput.value = option;
            if(option === 'info') {
                btnInfo.className = "flex-1 py-2 rounded-xl text-xs bg-white shadow-sm text-green-600 border border-green-200 font-semibold transition-all";
                btnProcess.className = "flex-1 py-2 rounded-xl text-xs text-gray-500 hover:bg-white font-medium transition-all";
                userInput.placeholder = "សួរអំពីព័ត៌មានវត្តមាន...";
            } else {
                btnProcess.className = "flex-1 py-2 rounded-xl text-xs bg-white shadow-sm text-green-600 border border-green-200 font-semibold transition-all";
                btnInfo.className = "flex-1 py-2 rounded-xl text-xs text-gray-500 hover:bg-white font-medium transition-all";
                userInput.placeholder = "សួរអំពីរបៀបប្រើប្រាស់...";
            }
        }

        // ៣. Logic ផ្ញើសារ (AJAX)
        document.addEventListener('DOMContentLoaded', function() {
            const chatForm = document.getElementById('chat-form');
            const chatBox = document.getElementById('chat-box');
            const userInput = document.getElementById('user-input');
            const thinkingIndicator = document.getElementById('thinking-indicator');

            if (chatForm) {
                chatForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const message = userInput.value.trim();
                    const option = document.getElementById('chat-option').value;
                    if (!message) return;

                    appendMessage('user', message);
                    userInput.value = '';
                    thinkingIndicator.classList.remove('hidden');
                    chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });

                    try {
                        const response = await fetch("{{ route('ai.send') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ message: message, option: option })
                        });

                        const data = await response.json();
                        thinkingIndicator.classList.add('hidden');
                        
                        if (data.message) {
                            appendMessage('ai', data.message);
                        } else {
                            appendMessage('ai', 'សុំទោសបង! មានបញ្ហាបន្តិចបន្តួច។');
                        }
                    } catch (error) {
                        thinkingIndicator.classList.add('hidden');
                        appendMessage('ai', 'Error: មិនអាចទាក់ទងម៉ាស៊ីនមេបានទេ!');
                    }
                });
            }

function appendMessage(sender, text) {
    if (!chatBox) return;
    const div = document.createElement('div');
    // បន្ថែម Animation ពេល Message លេចចេញមក
    div.className = sender === 'user' ? 'flex justify-end mb-4' : 'flex justify-start mb-4';
    
    const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    const content = sender === 'user' 
        ? `<div class="flex flex-col items-end max-w-[85%]">
             <div class="bg-blue-600 text-white p-3 rounded-2xl rounded-tr-none shadow-sm text-sm">
                ${text}
             </div>
             <span class="text-[9px] text-gray-400 mt-1">${time}</span>
           </div>`
        : `<div class="flex items-start space-x-2 max-w-[90%] group">
             <div class="w-8 h-8 rounded-full bg-green-100 flex-shrink-0 flex items-center justify-center border border-green-200">
                <span class="text-[10px] font-bold text-green-600">NMU</span>
             </div>
             <div class="flex flex-col">
                <div class="bg-white border border-gray-100 text-gray-800 p-4 rounded-2xl rounded-tl-none shadow-sm prose prose-sm prose-blue max-w-full text-sm leading-relaxed">
                    ${marked.parse(text)}
                </div>
                <div class="flex items-center space-x-2 mt-1 ml-1">
                    <span class="text-[9px] text-gray-400 italic">NMU Smart Assistant</span>
                    <span class="text-[9px] text-gray-400">•</span>
                    <span class="text-[9px] text-gray-400">${time}</span>
                </div>
             </div>
           </div>`;
    
    div.innerHTML = content;
    chatBox.appendChild(div);
    chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
}
        });
    </script>