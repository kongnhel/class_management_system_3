<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }} - ពាក្យសម្ងាត់ 2FA</title>

<x-guest-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { font-family: 'Battambang', sans-serif; margin: 0; }
        .min-h-screen { padding: 0 !important; justify-content: stretch !important; align-items: stretch !important; max-width: 100% !important; }
    </style>

    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-6 py-12">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900">ពាក្យសម្ងាត់ 2FA</h2>
                <p class="text-gray-500 mt-2 text-sm leading-relaxed">
                    គណនី Telegram របស់អ្នកមានពាក្យសម្ងាត់ 2FA ។<br>
                    សូមបញ្ចូលពាក្យសម្ងាត់ Telegram របស់អ្នក។
                </p>
                @if($hint)
                    <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-amber-50 rounded-xl border border-amber-200">
                        <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <span class="text-sm text-amber-700">សំណួរ: {{ $hint }}</span>
                    </div>
                @endif
                <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 rounded-xl border border-emerald-200">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span class="text-sm font-bold text-emerald-700">{{ $maskedPhone }}</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <form method="POST" action="{{ route('phone-otp.2fa.verify') }}">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ពាក្យសម្ងាត់ Telegram</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                   class="block w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                                   placeholder="បញ្ចូលពាក្យសម្ងាត់ 2FA" autofocus />
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(session('error'))
                        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-center">
                            <p class="text-sm text-red-600 font-medium">{{ session('error') }}</p>
                        </div>
                    @endif

                    <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 text-sm">
                        ផ្ទៀងផ្ទាត់ និងចូលប្រព័ន្ធ
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                        ← ត្រឡប់ទៅការចូលប្រព័ន្ធ
                    </a>
                </div>
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
</x-guest-layout>