<x-app-layout>
<div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-5">

    {{-- ============================================================ --}}
    {{-- SUCCESS FLASH                                                --}}
    {{-- ============================================================ --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl flex items-center gap-3 text-sm font-bold">
            <i class="fas fa-check-circle text-emerald-500"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- PROFILE HERO                                                 --}}
    {{-- ============================================================ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-purple-700 shadow-xl shadow-emerald-200/50">
        {{-- decorative blobs --}}
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-10 w-64 h-64 bg-purple-400/10 rounded-full blur-3xl pointer-events-none"></div>

        {{-- edit button — top right --}}
        <div class="absolute top-5 right-5 z-10">
            <a wire:navigate href="{{ route('professor.profile.edit') }}"
                class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 text-white px-4 py-2 rounded-xl font-bold text-xs transition-all">
                <i class="fas fa-user-edit"></i>
                {{ __('edit_profile') }}
            </a>
        </div>

        {{-- content --}}
        <div class="relative px-8 pt-10 pb-8 flex flex-col sm:flex-row items-center sm:items-end gap-6">

            {{-- avatar --}}
            <div class="flex-shrink-0 w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden border-4 border-white/20 shadow-2xl bg-white/10">
                @if($userProfile->profile_picture_url)
                    <img
                        src="{{ $userProfile->profile_picture_url }}?tr=w-600,h-600,fo-face,q-auto,f-auto"
                        alt="{{ $user->name }}"
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white text-3xl font-black">
                        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- name + role --}}
            <div class="text-center sm:text-left pb-1">
                <div class="inline-flex items-center gap-1.5 bg-white/10 border border-white/15 text-emerald-100 text-[11px] font-bold px-3 py-1 rounded-full mb-2">
                    <i class="fas fa-chalkboard-teacher text-[10px]"></i>
                    {{ __('professor_2') }}
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-white leading-tight">
                    {{ $userProfile->full_name_km ?? $user->name }}
                </h2>
                <p class="text-emerald-200 text-sm mt-1">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- DETAILS CARD                                                 --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">

        {{-- card header --}}
        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-50">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                <i class="fas fa-id-card"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-800">{{ __('details_2') }}</h4>
        </div>

        {{-- details grid --}}
        <div class="p-6">
            @php
                $details = [
                    ['label' => __('khmer_name'),      'value' => $userProfile->full_name_km,    'icon' => 'fas fa-user',          'color' => 'emerald'],
                    ['label' => __('english_name'),    'value' => $userProfile->full_name_en,    'icon' => 'fas fa-font',          'color' => 'emerald'],
                    ['label' => __('gender'),          'value' => $userProfile->gender == 'male' ? __('male') : ($userProfile->gender == 'female' ? __('female') : null), 'icon' => 'fas fa-venus-mars', 'color' => 'violet'],
                    ['label' => __('date_of_birth_2'), 'value' => $userProfile->date_of_birth ? \Carbon\Carbon::parse($userProfile->date_of_birth)->format('d M Y') : null, 'icon' => 'fas fa-calendar', 'color' => 'violet'],
                    ['label' => 'Telegram',            'value' => $userProfile->telegram_user ? '@' . $userProfile->telegram_user : null, 'icon' => 'fab fa-telegram-plane', 'color' => 'sky'],
                    ['label' => __('phone'),           'value' => $userProfile->phone_number,    'icon' => 'fas fa-phone',         'color' => 'sky'],
                    ['label' => __('address'),         'value' => $userProfile->address,         'icon' => 'fas fa-map-marker-alt','color' => 'amber', 'fullWidth' => true],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($details as $detail)
                    @php
                        $colorMap = [
                            'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                            'violet'  => ['bg' => 'bg-violet-50',  'text' => 'text-violet-600'],
                            'sky'     => ['bg' => 'bg-sky-50',     'text' => 'text-sky-600'],
                            'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-600'],
                        ];
                        $c = $colorMap[$detail['color']] ?? $colorMap['emerald'];
                    @endphp
                    <div class="{{ isset($detail['fullWidth']) ? 'sm:col-span-2' : '' }}">
                        <div class="flex items-center gap-4 p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-slate-200 hover:shadow-sm transition-all">

                            {{-- icon --}}
                            <div class="w-9 h-9 rounded-xl {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center flex-shrink-0 text-sm">
                                <i class="{{ $detail['icon'] }}"></i>
                            </div>

                            {{-- label + value --}}
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] text-gray-400 font-semibold mb-0.5">{{ $detail['label'] }}</p>
                                <p class="text-sm font-bold text-gray-800 {{ isset($detail['fullWidth']) ? '' : 'truncate' }}">
                                    {{ $detail['value'] ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
</div>
</x-app-layout>