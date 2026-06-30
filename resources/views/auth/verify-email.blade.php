<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }} - ផ្ទៀងផ្ទាត់អ៊ីមែល</title>

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
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900">ផ្ទៀងផ្ទាត់អ៊ីមែល</h2>
                <p class="text-gray-500 mt-2 text-sm leading-relaxed">សូមផ្ទៀងផ្ទាត់អ៊ីមែលរបស់អ្នកដោยចុចលើបន្ទាត់សារដែលយើងបានផ្ញើទៅកាន់អ្នក។ ប្រសិនបើអ្នកមិនទទួលបាន សូមផ្ញើសារឡើងវិញ។</p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-sm text-green-700 font-medium">បន្ទាត់សារផ្ទៀងផ្ទាត់ថ្មីត្រូវបានផ្ញើទៅកាន់អ៊ីមែលរបស់អ្នកហើយ។</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                    @csrf
                    <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 text-sm">
                        ផ្ញើសារផ្ទៀងផ្ទាត់អ៊ីមែលឡើងវិញ
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                        ចាកចេញពីប្រព័ន្ធ
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>