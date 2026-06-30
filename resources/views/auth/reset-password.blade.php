<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }} - កំណត់ពាក្យសម្ងាត់ថ្មី</title>

<x-guest-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { font-family: 'Battambang', sans-serif; margin: 0; }
        .min-h-screen { padding: 0 !important; justify-content: stretch !important; align-items: stretch !important; max-width: 100% !important; }
    </style>

    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-6 py-12">
        <div class="w-full max-w-md">
            {{-- Logo --}}
            <div class="text-center mb-8">
                <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" class="w-16 h-16 mx-auto mb-3">
                <h2 class="text-2xl font-extrabold text-gray-900">កំណត់ពាក្យសម្ងាត់ថ្មី</h2>
                <p class="text-gray-500 mt-2 text-sm">សូមបញ្ចូលពាក្យសម្ងាត់ថ្មីរបស់អ្នក</p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth_email') }}</label>
                        <input id="email" type="email" name="email" required autofocus autocomplete="username"
                               class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                               placeholder="example@gmail.com" value="{{ old('email', $request->email) }}" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ពាក្យសម្ងាត់ថ្មី</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                               placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">បញ្ជាក់ពាក្យសម្ងាត់ថ្មី</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                               placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 text-sm">
                        កំណត់ពាក្យសម្ងាត់ថ្មី
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                        ← ត្រឡប់ទៅការចូលប្រព័ន្ធ
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>