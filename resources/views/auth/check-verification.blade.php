<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }} - ពិនិត្យស្ថានភាពផ្ទៀងផ្ទាត់</title>

<x-guest-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { font-family: 'Battambang', sans-serif; margin: 0; }
        .min-h-screen { padding: 0 !important; justify-content: stretch !important; align-items: stretch !important; max-width: 100% !important; }
    </style>

    {{-- Flash toasts are handled by <x-toast /> in the guest layout --}}

    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-6 py-12">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900">ពិនិត្យស្ថានភាពផ្ទៀងផ្ទាត់</h2>
                <p class="text-gray-500 mt-2 text-sm leading-relaxed">
                    បញ្ចូលអ៊ីមែល ឬលេខទូរស័ព្ទរបស់អ្នកដើម្បីពិនិត្យមើលស្ថានភាពគណនី
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <form method="POST" action="{{ route('check-verification.lookup') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">អ៊ីមែល / លេខទូរស័ព្ទ / លេខសម្គាល់</label>
                        <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                               class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                               placeholder="example@gmail.com / 012345678 / ID-0000X" />
                        <x-input-error :messages="$errors->get('identifier')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 text-sm">
                        ពិនិត្យមើល
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