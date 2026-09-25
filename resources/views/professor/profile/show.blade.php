<x-app-layout>
<x-slot name="header">
    <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ __('profile_title') }}</h2>
    <p class="text-sm text-slate-500 mt-0.5">{{ __('profile_subtitle') }}</p>
</x-slot>

<div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    @php
        $user = $user ?? auth()->user();
        $userProfile = $userProfile ?? $user->userProfile;
        $departmentName = \App\Models\CourseOffering::where('lecturer_user_id', $user->id)
            ->with('department')
            ->orderByDesc('academic_year')
            ->first()?->department?->name_km;
    @endphp

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center gap-3 text-sm font-semibold">
            <i class="fas fa-check-circle text-emerald-500"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MAIN TWO-COLUMN SPLIT GRID                                   --}}
    {{-- ============================================================ --}}
    <div class="w-full grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">

        {{-- ===== LEFT CARD PANEL ===== --}}
        <div class="col-span-1 bg-white rounded-2xl shadow-sm p-6 text-center">

            {{-- avatar wrapper + profile picture --}}
            <div class="mx-auto w-fit p-2 rounded-[2rem] bg-gradient-to-br from-emerald-500 via-teal-500 to-emerald-600 shadow-lg shadow-emerald-500/25">
                <div class="w-24 h-24 rounded-3xl overflow-hidden bg-white flex items-center justify-center">
                    @if($userProfile?->profile_picture_url)
                        <img src="{{ $userProfile->profile_picture_url }}?tr=w-400,h-400,fo-face,q-auto,f-auto"
                             alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-emerald-600 text-3xl font-semibold">{{ Str::upper(Str::substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
            </div>

            {{-- role + name + email --}}
            <span class="inline-flex items-center gap-1.5 mt-5 bg-emerald-50 border border-emerald-100 text-emerald-700 text-[11px] font-bold px-3 py-1 rounded-full">
                <i class="fas fa-chalkboard-teacher text-[10px]"></i>
                {{ __('professor_2') }}
            </span>
            <h1 class="text-lg font-semibold text-slate-800 tracking-tight mt-3 break-words leading-relaxed">
                {{ $userProfile?->full_name_km ?? $user->name }}
            </h1>
            <p class="text-sm text-slate-400 font-medium mt-1 break-all leading-relaxed">{{ $user->email }}</p>

            {{-- department --}}
            @if($departmentName)
                <p class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 mt-3 leading-relaxed">
                    <i class="fas fa-building-columns text-slate-400 text-xs"></i>
                    {{ $departmentName }}
                </p>
            @endif

            {{-- edit profile: full-width secondary button --}}
            <a wire:navigate href="{{ route('professor.profile.edit') }}"
               class="mt-6 inline-flex items-center justify-center gap-2 w-full h-11 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-semibold hover:bg-emerald-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 active:scale-[0.98]">
                <i class="fas fa-user-edit text-xs"></i>
                {{ __('edit_profile') }}
            </a>
        </div>

        {{-- ===== RIGHT DETAIL PANEL ===== --}}
        <div class="col-span-1 lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">

            {{-- panel heading --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-id-card text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-800">{{ __('details_2') }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ __('profile_details_desc') }}</p>
                </div>
            </div>

            @php
                $details = [
                    ['label' => __('khmer_name'),      'icon' => 'fas fa-user',          'value' => $userProfile?->full_name_km],
                    ['label' => __('english_name'),    'icon' => 'fas fa-font',          'value' => $userProfile?->full_name_en],
                    ['label' => __('gender'),          'icon' => 'fas fa-venus-mars',     'value' => $userProfile?->gender ? ($userProfile->gender == 'male' ? __('male') : ($userProfile->gender == 'female' ? __('female') : __('other'))) : null],
                    ['label' => __('date_of_birth_2'), 'icon' => 'fas fa-calendar',       'value' => $userProfile?->date_of_birth ? \Carbon\Carbon::parse($userProfile->date_of_birth)->format('d M Y') : null],
                    ['label' => __('phone'),           'icon' => 'fas fa-phone',         'value' => $userProfile?->phone_number],
                    ['label' => __('Telegram'),         'icon' => 'fab fa-telegram-plane', 'value' => $userProfile?->telegram_user ? '@' . $userProfile->telegram_user : null],
                    ['label' => __('address'),         'icon' => 'fas fa-map-marker-alt', 'value' => $userProfile?->address, 'full' => true],
                ];
            @endphp

            {{-- minimal 2-column data grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                @foreach($details as $detail)
                    <div class="{{ isset($detail['full']) ? 'md:col-span-2' : '' }} bg-slate-50 border border-slate-100 rounded-xl p-4 transition-colors hover:bg-slate-100/60">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white text-emerald-600 shadow-sm flex items-center justify-center flex-shrink-0 text-xs">
                                <i class="{{ $detail['icon'] }}"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">{{ $detail['label'] }}</p>
                                <p class="text-sm font-medium text-slate-800 mt-0.5 break-words leading-relaxed">{{ $detail['value'] ?? '-' }}</p>
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
