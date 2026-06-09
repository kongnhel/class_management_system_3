<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }}</title>

<x-guest-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Battambang:wght@400;700&display=swap');
        body { font-family: 'Inter', 'Battambang', sans-serif; margin: 0; }
        .min-h-screen { padding: 0 !important; justify-content: stretch !important; align-items: stretch !important; max-width: 100% !important; }
    </style>

    {{-- Toast --}}
    @if (session('success') || session('error'))
    <div x-data="{ show: true, progress: 100 }" x-init="setInterval(() => { progress -= 1; if (progress <= 0) show = false; }, 50)" x-show="show" x-transition class="fixed top-6 right-6 z-[9999] w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ session('success') ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} flex items-center justify-center shrink-0">
                    @if(session('success'))
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900">{{ session('success') ? __('success') : __('error') }}</p>
                    <p class="text-sm text-gray-500 truncate">{{ session('success') ?? session('error') }}</p>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full {{ session('success') ? 'bg-green-500' : 'bg-red-500' }} transition-all duration-75" :style="`width: ${progress}%`"></div>
            </div>
        </div>
    </div>
    @endif

    <div class="min-h-screen flex">
        {{-- Left: Branding --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center">
            <img src="{{ asset('assets/image/download (5).jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-emerald-900/70"></div>
            <div class="relative z-10 text-center px-12">
                <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" class="w-28 h-28 mx-auto mb-8 drop-shadow-2xl">
                <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">Class Management<br>System</h1>
                <p class="text-emerald-100 text-lg max-w-sm mx-auto leading-relaxed">ប្រព័ន្ធគ្រប់គ្រងសិក្សា សម្រាប់មហាវិទ្យាល័យ</p>
                <div class="mt-10 flex items-center justify-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-emerald-300 animate-pulse"></div>
                    <span class="text-emerald-200 text-sm font-medium">សូមចូលប្រព័ន្ធដើម្បីបន្ត</span>
                </div>
            </div>
        </div>

        {{-- Right: Login Form --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-gray-50">
            <div class="w-full max-w-md">
                {{-- Mobile Logo --}}
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" class="w-16 h-16 mx-auto mb-3">
                    <h2 class="text-xl font-bold text-gray-800">Class Management System</h2>
                </div>

                {{-- Header --}}
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-gray-900">{{ __('auth_login') }}</h2>
                    <p class="text-gray-500 mt-2 text-sm">សូមបញ្ចូលព័ត៌មានគណនីរបស់អ្នកដើម្បីចូល</p>
                </div>

                {{-- Tab Switcher --}}
                <div class="flex bg-gray-100 rounded-xl p-1 mb-8">
                    <button id="emailTabBtn" onclick="switchTab('email')" class="flex-1 py-2.5 text-sm font-bold rounded-lg bg-white text-gray-900 shadow-sm transition-all">
                        អ៊ីមែល
                    </button>
                    <button id="qrTabBtn" onclick="switchTab('qr')" class="flex-1 py-2.5 text-sm font-bold rounded-lg text-gray-500 hover:text-gray-700 transition-all">
                        QR Code
                    </button>
                </div>

                {{-- Email Login --}}
                <div id="emailSection">
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth_email') }}</label>
                            <input id="email" type="email" name="email" required 
                                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none" 
                                   placeholder="example@gmail.com" value="{{ old('email') }}" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth_password') }}</label>
                            <div class="relative">
                                <input id="password" type="password" name="password" required 
                                       class="block w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none" 
                                       placeholder="••••••••" />
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 text-sm">
                            {{ __('auth_login_btn') }}
                        </button>
                    </form>

                    <div class="flex items-center gap-4 my-6">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-xs font-bold text-gray-400 uppercase">ឬ</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    <button onclick="loginWithGoogle()" 
                            class="w-full py-3.5 bg-white border border-gray-200 hover:border-gray-300 text-gray-700 font-bold rounded-xl flex items-center justify-center gap-3 transition-all text-sm shadow-sm">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5">
                        ចូលជាមួយ Google
                    </button>
                </div>

                {{-- QR Code Login --}}
                <div id="qrSection" class="hidden text-center">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('auth_login_qr') }}</h3>
                    <p class="text-sm text-gray-500 mb-6">ស្កេន QR Code ដោយប្រើទូរស័ព្ទដៃរបស់អ្នក</p>
                    
                    <div class="inline-block p-4 bg-white rounded-2xl border-2 border-gray-100 shadow-sm" id="qrContainer">
                        {!! $qrCode ?? '<div class="w-56 h-56 flex items-center justify-center text-gray-400 text-sm">QR Loading...</div>' !!}
                    </div>
                    
                    <p class="text-emerald-600 text-sm font-bold mt-4" id="qr-status">{{ __('auth_qr_waiting') }}</p>
                    
                    <button onclick="refreshQR()" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-emerald-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        {{ __('auth_qr_refresh') }}
                    </button>
                </div>

                {{-- Register Link --}}
                <p class="text-center text-sm text-gray-500 mt-8">
                    {{ __('auth_no_account') }}
                    <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:text-emerald-700 ml-1">
                        {{ __('auth_register_link') }}
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const open = document.getElementById('eyeOpen');
            const closed = document.getElementById('eyeClosed');
            if (input.type === 'password') {
                input.type = 'text';
                open.classList.add('hidden');
                closed.classList.remove('hidden');
            } else {
                input.type = 'password';
                open.classList.remove('hidden');
                closed.classList.add('hidden');
            }
        }

        function switchTab(tab) {
            const emailSection = document.getElementById('emailSection');
            const qrSection = document.getElementById('qrSection');
            const emailBtn = document.getElementById('emailTabBtn');
            const qrBtn = document.getElementById('qrTabBtn');

            if (tab === 'email') {
                emailSection.classList.remove('hidden');
                qrSection.classList.add('hidden');
                emailBtn.className = 'flex-1 py-2.5 text-sm font-bold rounded-lg bg-white text-gray-900 shadow-sm transition-all';
                qrBtn.className = 'flex-1 py-2.5 text-sm font-bold rounded-lg text-gray-500 hover:text-gray-700 transition-all';
            } else {
                qrSection.classList.remove('hidden');
                emailSection.classList.add('hidden');
                qrBtn.className = 'flex-1 py-2.5 text-sm font-bold rounded-lg bg-white text-gray-900 shadow-sm transition-all';
                emailBtn.className = 'flex-1 py-2.5 text-sm font-bold rounded-lg text-gray-500 hover:text-gray-700 transition-all';
            }
        }
    </script>

    {{-- Firebase Google Login --}}
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ env('VITE_FIREBASE_AUTH_DOMAIN') }}",
            projectId: "{{ env('VITE_FIREBASE_PROJECT_ID') }}",
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        window.loginWithGoogle = () => {
            signInWithPopup(auth, provider).then((result) => {
                fetch('{{ route("auth.google.callback") }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ 
                        uid: result.user.uid, 
                        email: result.user.email 
                    })
                }).then(res => res.json()).then(data => {
                    if(data.status === 'success') {
                        window.location.href = "/dashboard";
                    } else {
                        alert(data.message);
                    }
                });
            }).catch(error => console.error(error));
        };
    </script>

    {{-- Pusher + QR Refresh --}}
    @if(isset($token))
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        let currentToken = "{{ $token }}";
        let pusher;

        function initPusher() {
            pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', { 
                cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                forceTLS: true
            });
            subscribeChannel();
        }

        function subscribeChannel() {
            const channel = pusher.subscribe('login-channel-' + currentToken);
            channel.bind('login-success', function() {
                const statusEl = document.getElementById('qr-status');
                if (statusEl) {
                    statusEl.innerHTML = `<span class="text-emerald-600 animate-pulse">{{ __('auth_logging_in') }}</span>`;
                }
                setTimeout(() => {
                    window.location.href = "/qr-login/finalize/" + currentToken;
                }, 1000);
            });
        }

        window.refreshQR = function() {
            fetch('/qr-refresh', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('qrContainer').innerHTML = data.qrCode;
                currentToken = data.token;
                if (pusher) {
                    pusher.unsubscribe('login-channel-' + currentToken);
                    subscribeChannel();
                }
                document.getElementById('qr-status').textContent = "{{ __('auth_qr_waiting') }}";
            })
            .catch(error => {
                console.error('QR Refresh Error:', error);
            });
        };

        setInterval(() => {
            if (!document.getElementById('qrSection').classList.contains('hidden')) {
                refreshQR();
            }
        }, 180000);

        window.onload = initPusher;
    </script>
    @endif
</x-guest-layout>
