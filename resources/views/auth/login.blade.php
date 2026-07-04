<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }}</title>

<x-guest-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Battambang:wght@400;700&display=swap');
        body { font-family: 'Inter', 'Battambang', sans-serif; margin: 0; }
        .min-h-screen { padding: 0 !important; justify-content: stretch !important; align-items: stretch !important; max-width: 100% !important; }
    </style>

    <div class="min-h-screen flex">
        {{-- Left: Branding --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center">
            <img src="{{ asset('assets/image/download (5).jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-emerald-900/70"></div>
            <div class="relative z-10 text-center px-12">
                <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" class="w-28 h-28 mx-auto mb-8 drop-shadow-2xl">
                <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">Class Management<br>System</h1>
                <p class="text-emerald-100 text-lg max-w-sm mx-auto leading-relaxed">ប្រព័ន្ធគ្រប់គ្រងថ្នាក់រៀន សម្រាប់សកលវិទ្យាល័យ</p>
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

                {{-- Login Form --}}
                <div>
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">អ៊ីមែល / លេខទូរស័ព្ទ / លេខសម្គាល់</label>
                            <input id="login_identifier" type="text" name="login_identifier" required 
                                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none" 
                                   placeholder="អ៊ីមែល / លេខទូរស័ព្ទ / លេខសម្គាល់" value="{{ old('login_identifier') }}" />
                            <x-input-error :messages="$errors->get('login_identifier')" class="mt-1.5 text-xs text-red-500" />
                        </div>

                        <div id="passwordSection">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth_password') }}</label>
                            <div class="relative">
                                <input id="password" type="password" name="password" 
                                       class="block w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none" 
                                       placeholder="••••••••" />
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
                        </div>

                        <button type="submit" id="loginBtn" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 text-sm">
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
            signInWithPopup(auth, provider).then(async (result) => {
                const idToken = await result.user.getIdToken();
                fetch('{{ route("auth.google.callback") }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ 
                        id_token: idToken
                    })
                }).then(res => res.json()).then(data => {
                    if(data.status === 'success') {
                        window.location.href = "/dashboard";
                    } else {
                        showToast(data.message, 'error');
                    }
                });
            }).catch(error => console.error(error));
        };
    </script>
</x-guest-layout>
