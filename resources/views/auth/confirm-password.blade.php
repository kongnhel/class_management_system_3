<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }} - បញ្ជាក់ពាក្យសម្ងាត់</title>

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
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900">បញ្ជាក់ពាក្យសម្ងាត់</h2>
                <p class="text-gray-500 mt-2 text-sm leading-relaxed">នេះគឺជាតំបន់សុវត្ថិភាពនៃប្រព័ន្ធ។ សូមបញ្ចូលពាក្យសម្ងាត់របស់អ្នកដើម្បីបន្ត។</p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ពាក្យសម្ងាត់</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password" autofocus
                               class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                               placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 text-sm">
                        បញ្ជាក់
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>