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
      @close-sidebar.window="open = false"
      :class="{ 'dark': darkMode }">
    <head>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        

        <link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
        <title>{{ config('', 'Class Management System') }}</title>
        @livewireStyles
        <script src="https://unpkg.com/html5-qrcode@2.3.8" type="text/javascript"></script>
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
            
            /* Global hand cursor for all buttons and clickable elements */
            button, a, [role="button"], input[type="submit"], input[type="button"], 
            select, [onclick], [x-on\\:click], [\\@click] {
                cursor: pointer !important;
            }
            body {
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
                nav, header, footer, .lg\:hidden, .no-print, .theme-toggle-btn,
                #draggableChat, #ai-sidebar, #chat-overlay, #confirm-modal { display: none !important; }
                main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
                @page { margin: 15mm; size: A4 portrait; }
            }
        </style>
    </head>

    <body class="font-sans antialiased text-gray-900">
        <script>
            if (window.history.scrollRestoration) {
                window.history.scrollRestoration = 'manual';
            }
            window.scrollTo(0, 0);
        </script>
        <script data-navigate-once>
            const sidebarScrollStorageKey = 'class-management-sidebar-scroll-top';

            const getSidebarLinks = () => document.querySelector('.sidebar-links');

            const saveSidebarScrollPosition = () => {
                const sidebarLinks = getSidebarLinks();

                if (sidebarLinks) {
                    sessionStorage.setItem(sidebarScrollStorageKey, String(sidebarLinks.scrollTop));
                }
            };

            const restoreSidebarScrollPosition = () => {
                const sidebarLinks = getSidebarLinks();

                if (!sidebarLinks) {
                    return;
                }

                const savedScrollTop = Number(sessionStorage.getItem(sidebarScrollStorageKey));

                if (Number.isFinite(savedScrollTop)) {
                    requestAnimationFrame(() => {
                        sidebarLinks.scrollTop = savedScrollTop;
                    });
                }
            };

            const syncSidebarTabState = () => {
                const currentUrl = new URL(window.location.href);
                const currentPath = currentUrl.pathname.replace(/\/+$/, '');
                const currentTab = currentUrl.searchParams.get('tab');

                document.querySelectorAll('[data-sidebar-tab]').forEach((link) => {
                    const linkUrl = new URL(link.href, window.location.href);
                    const isCurrent = linkUrl.pathname.replace(/\/+$/, '') === currentPath
                        && link.dataset.sidebarTab === currentTab;

                    link.classList.toggle('sidebar-tab-current', isCurrent);
                });
            };

            document.addEventListener('alpine:navigating', saveSidebarScrollPosition);
            document.addEventListener('livewire:navigated', () => {
                window.dispatchEvent(new CustomEvent('close-sidebar'));
                syncSidebarTabState();
                restoreSidebarScrollPosition();
                window.scrollTo(0, 0);
            });

            const bindSidebarScrollListener = () => {
                const sidebarLinks = getSidebarLinks();

                if (sidebarLinks && !sidebarLinks.dataset.scrollPersistenceBound) {
                    sidebarLinks.addEventListener('scroll', saveSidebarScrollPosition, { passive: true });
                    sidebarLinks.dataset.scrollPersistenceBound = 'true';
                }
            };

            document.addEventListener('DOMContentLoaded', () => {
                bindSidebarScrollListener();
                syncSidebarTabState();
                restoreSidebarScrollPosition();
            });

            bindSidebarScrollListener();
            syncSidebarTabState();
            restoreSidebarScrollPosition();
        </script>
    <x-toast />

    @auth
        @php
            $user = Auth::user()->loadMissing('userProfile');
            $profilePath = $user->userProfile?->profile_picture_url;
            $profileUrl = $profilePath ? asset('storage/' . $profilePath) : null;
            $roleText = match ($user->role) {
                'admin' => __('role_admin'),
                'professor' => __('role_professor'),
                'student' => __('role_student'),
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
        <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
            
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
            <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center text-sm font-bold bg-white-600 text-white shadow-md shadow-emerald-200 border border-white">
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
        <div class="flex flex-col min-h-screen lg:ml-72 pt-16 lg:pt-0">
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
<x-ai-chat />

<script>
    var CURRENT_USER_NAME = "{{ Auth::user()->name }}";
    var CURRENT_USER_ROLE = "{{ Auth::user()->role }}";
    var CSRF_TOKEN = "{{ csrf_token() }}";
    var AI_ROUTES = {
        send: "{{ route('ai.send') }}",
        history: "{{ route('ai.history') }}",
        'clear-history': "{{ route('ai.clear-history') }}",
        feedback: "{{ route('ai.feedback') }}"
    };
</script>
@endauth

    @livewireScripts

    <script>
        (function () {
            if (window.__adminRealtimeFiltersInitialized) return;
            window.__adminRealtimeFiltersInitialized = true;

            var activeRequest = null;
            var debounceTimer = null;
            var composingInputs = new WeakSet();
            var loadingMessage = @json(__('realtime_search_loading'));

            function getLoadingIndicator() {
                var indicator = document.getElementById('admin-realtime-filter-loading');
                if (indicator) return indicator;

                indicator = document.createElement('div');
                indicator.id = 'admin-realtime-filter-loading';
                indicator.className = 'hidden fixed top-4 right-4 z-[9999] items-center gap-2 rounded-xl bg-white px-4 py-3 text-sm font-semibold text-emerald-700 shadow-xl ring-1 ring-emerald-100';
                indicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span></span>';
                indicator.querySelector('span').textContent = loadingMessage;
                document.body.appendChild(indicator);

                return indicator;
            }

            function setLoading(isLoading, form) {
                var indicator = getLoadingIndicator();
                indicator.classList.toggle('hidden', !isLoading);
                indicator.classList.toggle('flex', isLoading);

                // Do NOT gray out or disable the form. The search input must stay
                // fully active/typeable while the request is in flight.
                if (form) {
                    form.setAttribute('aria-busy', isLoading ? 'true' : 'false');
                }
            }

            function mergeUrlParams(url) {
                var currentParams = new URLSearchParams(window.location.search);
                currentParams.forEach(function (value, key) {
                    if (!url.searchParams.has(key)) url.searchParams.set(key, value);
                });
                url.searchParams.delete('page');
                url.searchParams.delete('adminsPage');
                return url;
            }

            function buildUrl(form) {
                var url = mergeUrlParams(new URL(form.action, window.location.href));

                new FormData(form).forEach(function (value, key) {
                    url.searchParams.set(key, value);
                });

                return url;
            }

            function fetchAdminResults(url, form) {
                var container = document.querySelector('[data-admin-results]');
                if (!container) return;

                if (activeRequest) activeRequest.abort();
                var requestController = new AbortController();
                activeRequest = requestController;
                setLoading(true, form);

                fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    signal: requestController.signal
                })
                .then(function (response) {
                    if (!response.ok) throw new Error('Unable to fetch admin results');
                    return response.text();
                })
                .then(function (html) {
                    var parsed = new DOMParser().parseFromString(html, 'text/html');
                    var nextContainer = parsed.querySelector('[data-admin-results]')
                                    || parsed.querySelector('main');
                    if (!nextContainer) throw new Error('Admin results were not returned');

                    // Replace ONLY the results container. The search input lives
                    // outside it, so its value, focus and caret are never touched.
                    if (window.Alpine && Alpine.destroyTree) Alpine.destroyTree(container);
                    container.innerHTML = nextContainer.innerHTML;
                    if (window.Alpine && Alpine.initTree) Alpine.initTree(container);
                    window.history.replaceState({}, '', url.toString());
                })
                .catch(function (error) {
                    if (error.name !== 'AbortError') window.location.assign(url.toString());
                })
                .finally(function () {
                    if (activeRequest === requestController) {
                        activeRequest = null;
                        setLoading(false, form);
                    }
                });
            }

            document.addEventListener('compositionstart', function (event) {
                var input = event.target;
                if (input && input.name === 'search' && input.closest('form[data-admin-realtime-filter]')) {
                    composingInputs.add(input);
                    clearTimeout(debounceTimer);
                }
            });

            document.addEventListener('compositionend', function (event) {
                var input = event.target;
                var form = input && input.closest('form[data-admin-realtime-filter]');
                if (!form || input.name !== 'search') return;

                composingInputs.delete(input);
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    fetchAdminResults(buildUrl(form), form);
                }, 350);
            });

            document.addEventListener('input', function (event) {
                var input = event.target;
                var form = input && input.closest('form[data-admin-realtime-filter]');
                if (!form || input.name !== 'search') return;
                if (event.isComposing || composingInputs.has(input)) return;

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    fetchAdminResults(buildUrl(form), form);
                }, 350);
            });

            document.addEventListener('change', function (event) {
                var control = event.target;
                var form = control && control.closest('form[data-admin-realtime-filter]');
                if (!form || control.tagName !== 'SELECT') return;

                fetchAdminResults(buildUrl(form), form);
            });

            document.addEventListener('submit', function (event) {
                var form = event.target;
                if (!form || !form.matches('form[data-admin-realtime-filter]')) return;

                event.preventDefault();
                clearTimeout(debounceTimer);
                fetchAdminResults(buildUrl(form), form);
            });

            document.addEventListener('click', function (event) {
                var clearButton = event.target.closest('[data-admin-clear-search]');
                if (clearButton) {
                    event.preventDefault();
                    var clearForm = clearButton.closest('form[data-admin-realtime-filter]');
                    var clearInput = clearForm && clearForm.querySelector('input[name="search"]');
                    if (clearForm && clearInput) {
                        clearInput.value = '';
                        fetchAdminResults(buildUrl(clearForm), clearForm);
                    }
                    return;
                }

                var link = event.target.closest('main a[href]');
                if (!link || link.hasAttribute('wire:navigate') || link.hasAttribute('wire:click') || link.closest('[wire\\:id]')) return;

                var url = new URL(link.href, window.location.href);
                if (url.pathname !== window.location.pathname || !url.searchParams.has('page')) return;

                event.preventDefault();
                fetchAdminResults(mergeUrlParams(url), null);
            });
        })();
    </script>
</body>
</html>
