<x-app-layout>
@php
    $user       = Auth::user()->loadMissing('userProfile');
    $profileUrl = $user->userProfile?->profile_picture_url;
    $role       = $user->role; // 'student' | 'professor' | 'admin'

    // ── Hero gradient ──────────────────────────────────────────────
    $heroGradient = match($role) {
        'student'   => 'from-emerald-600 via-emerald-700 to-teal-700',
        'professor' => 'from-emerald-600 via-emerald-700 to-purple-700',
        'admin'     => 'from-indigo-600 via-indigo-700 to-purple-700',
        default     => 'from-emerald-600 via-emerald-700 to-purple-700',
    };

    // ── Role badge ─────────────────────────────────────────────────
    $roleIcon  = match($role) {
        'student'   => 'fas fa-user-graduate',
        'professor' => 'fas fa-chalkboard-teacher',
        'admin'     => 'fas fa-shield-halved',
        default     => 'fas fa-user',
    };
    $roleLabel = match($role) {
        'student'   => __('student'),
        'professor' => __('professor_2'),
        'admin'     => __('admin'),
        default     => ucfirst($role),
    };

    // ── Accent colours (inputs, buttons, badges) ───────────────────
    $accentBg        = $role === 'admin' ? 'bg-indigo-50'      : 'bg-emerald-50';
    $accentText      = $role === 'admin' ? 'text-indigo-600'   : 'text-emerald-600';
    $accentBadge     = $role === 'admin' ? 'bg-indigo-50 text-indigo-700'  : 'bg-emerald-50 text-emerald-700';
    $accentRing      = $role === 'admin' ? 'focus:ring-indigo-500 focus:border-indigo-500'  : 'focus:ring-emerald-500 focus:border-emerald-500';
    $saveBtnClass    = $role === 'admin' ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200'   : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200';
    $heroSubtext     = $role === 'admin' ? 'text-indigo-200'   : 'text-emerald-200';
    $section1Badge   = $role === 'admin' ? 'bg-indigo-50 text-indigo-600'  : 'bg-emerald-50 text-emerald-600';
    $avatarPlaceholder = $role === 'admin' ? 'bg-indigo-50 text-indigo-400' : 'bg-emerald-50 text-emerald-400';
@endphp

<div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
<div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">

    {{-- ============================================================ --}}
    {{-- FLASH MESSAGES                                               --}}
    {{-- ============================================================ --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl flex items-center justify-between gap-3 text-sm font-bold">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500"></i>
                {{ session('success') }}
            </div>
            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show"
            class="mb-6 bg-rose-50 border border-rose-200 px-5 py-4 rounded-2xl flex items-start justify-between gap-3">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-rose-500 mt-0.5"></i>
                <div>
                    <p class="text-sm font-bold text-rose-700">{{ __('there_is_a_problem') }}</p>
                    <ul class="mt-1 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li class="text-xs text-rose-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button @click="show = false" class="text-rose-400 hover:text-rose-600 flex-shrink-0">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MAIN SPLIT GRID                                              --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== LEFT: CORE IDENTITY CARD ===== --}}
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 text-center h-fit">

            {{-- avatar with soft ring + change-photo upload --}}
            <form method="post" action="{{ route('profile.update-picture') }}"
                enctype="multipart/form-data" id="picture-form" class="flex justify-center">
                @csrf
                <div class="relative group cursor-pointer" id="profile-picture-container">
                    <div class="w-24 h-24 rounded-full ring-4 ring-slate-50 shadow-sm overflow-hidden {{ $avatarPlaceholder }}">
                        @if($profileUrl)
                            <img src="{{ $profileUrl }}"
                                id="profile-picture-preview"
                                class="w-full h-full object-cover"
                                alt="{{ $user->name }}">
                        @else
                            <div id="profile-picture-placeholder"
                                class="w-full h-full flex items-center justify-center text-2xl font-black">
                                {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="absolute inset-0 rounded-full bg-slate-900/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-camera text-white text-lg"></i>
                    </div>
                    <input id="profile_picture" name="" type="file" class="hidden" accept="image/*" />
                    <input type="hidden" id="profile_picture_base64" name="profile_picture_base64" value="" />
                </div>
            </form>

            <p class="text-[11px] text-slate-400 font-medium mt-3">{{ __('click_on_photo_to_change') }}</p>

            {{-- name + email badge --}}
            <h3 class="text-base font-semibold text-slate-800 tracking-tight mt-2 break-words leading-relaxed">{{ $user->name }}</h3>
            <span class="inline-flex items-center gap-1.5 mt-2 bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-medium px-3 py-1 rounded-full break-all">
                <i class="fas fa-envelope text-[10px] text-slate-400"></i>
                {{ $user->email }}
            </span>
            <div class="mt-2">
                <span class="inline-flex items-center gap-1.5 {{ $accentBadge }} text-[11px] font-bold px-3 py-1 rounded-full">
                    <i class="{{ $roleIcon }} text-[10px]"></i> {{ $roleLabel }}
                </span>
            </div>

            {{-- vertical quick-nav stack --}}
            <nav class="mt-6 space-y-1 text-left">
                <a href="#overview"
                   class="flex items-center gap-3 px-4 h-11 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors">
                    <i class="{{ $roleIcon }} text-xs {{ $accentText }}"></i>
                    {{ __('basic_information') }}
                </a>
                <a href="#security"
                   class="flex items-center gap-3 px-4 h-11 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors">
                    <i class="fas fa-shield-halved text-xs text-purple-500"></i>
                    {{ __('account_security') }}
                </a>
                @if($role === 'professor')
                    <a wire:navigate href="{{ route('professor.notifications.index') }}"
                       class="flex items-center gap-3 px-4 h-11 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors">
                        <i class="fas fa-bell text-xs text-amber-500"></i>
                        {{ __('nav_notifications') }}
                    </a>
                @elseif($role === 'student')
                    <a wire:navigate href="{{ route('student.notifications.index') }}"
                       class="flex items-center gap-3 px-4 h-11 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors">
                        <i class="fas fa-bell text-xs text-amber-500"></i>
                        {{ __('nav_notifications') }}
                    </a>
                @else
                    <a wire:navigate href="{{ route('admin.announcements.index') }}"
                       class="flex items-center gap-3 px-4 h-11 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors">
                        <i class="fas fa-bell text-xs text-amber-500"></i>
                        {{ __('nav_notifications') }}
                    </a>
                @endif
            </nav>
        </div>

        {{-- ===== RIGHT: DETAILED INFORMATION HUB ===== --}}
        <div class="col-span-1 lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8">

            {{-- Actions header banner --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-lg font-bold text-slate-800 tracking-tight leading-relaxed">{{ __('edit_profile') }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">{{ __('manage_personal_info_and_account_security') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ url()->previous() }}"
                       class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors"
                       title="{{ __('back_2') }}">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <button type="button" onclick="document.getElementById('profile_picture').click()"
                            class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:border-emerald-300 hover:text-emerald-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                        <i class="fas fa-camera text-xs"></i>
                        {{ __('change_photo') }}
                    </button>
                </div>
            </div>

            {{-- ===== SECTION: BASIC INFORMATION ===== --}}
            <section id="overview" class="mt-8 pt-6 border-t border-slate-100 scroll-mt-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-xl {{ $section1Badge }} flex items-center justify-center flex-shrink-0">
                        <i class="{{ $roleIcon }} text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-800">{{ __('basic_information') }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ __('manage_your_personal_information') }}</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div>
                            <label for="name" class="text-xs font-semibold tracking-wider text-slate-400 uppercase mb-1 block">
                                {{ __('user_name') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="name" name="name" type="text"
                                value="{{ old('name', $user->name) }}"
                                required autofocus
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-800 transition-all focus:bg-white focus:ring-2 {{ $accentRing }} focus:outline-none" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>
                        <div>
                            <label for="email" class="text-xs font-semibold tracking-wider text-slate-400 uppercase mb-1 block">
                                {{ __('email_2') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="email" name="email" type="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-800 transition-all focus:bg-white focus:ring-2 {{ $accentRing }} focus:outline-none" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        {{-- Student ID - read-only attribute block (students only) --}}
                        @if($role === 'student')
                            <div class="md:col-span-2 bg-slate-50/60 hover:bg-slate-50 border border-slate-100 rounded-xl p-4 transition-all">
                                <label class="text-xs font-semibold tracking-wider text-slate-400 uppercase mb-1 block">{{ __('student_id') }}</label>
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-id-badge text-slate-400 text-sm"></i>
                                    <p class="text-sm font-semibold text-slate-800 tabular-nums">{{ $user->student_id_code ?? '-' }}</p>
                                    <span class="ml-auto text-[10px] font-bold bg-slate-100 text-slate-400 px-2 py-0.5 rounded-md">{{ __('read_only') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 {{ $saveBtnClass }} text-white rounded-xl font-bold text-sm transition-colors shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 active:scale-[0.98]">
                            <i class="fas fa-save text-xs"></i> {{ __('save_2') }}
                        </button>
                    </div>
                </form>
            </section>

            {{-- ===== SECTION: ACCOUNT SECURITY ===== --}}
            <section id="security" class="mt-10 pt-6 border-t border-slate-100 scroll-mt-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shield-halved text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-800">{{ __('account_security') }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ __('keep_your_account_secure_by_changing_password_regularly') }}</p>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf @method('put')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div class="md:col-span-2">
                            <label for="current_password" class="text-xs font-semibold tracking-wider text-slate-400 uppercase mb-1 block">
                                {{ __('current_password') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-800 transition-all focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none" />
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
                        </div>
                        <div>
                            <label for="password" class="text-xs font-semibold tracking-wider text-slate-400 uppercase mb-1 block">
                                {{ __('new_password_2') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-800 transition-all focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none" />
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
                        </div>
                        <div>
                            <label for="password_confirmation" class="text-xs font-semibold tracking-wider text-slate-400 uppercase mb-1 block">
                                {{ __('confirm_new_password_2') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-800 transition-all focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-sm transition-colors shadow-sm shadow-purple-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple-500/40 active:scale-[0.98]">
                            <i class="fas fa-key text-xs"></i> {{ __('update_password') }}
                        </button>
                    </div>
                </form>
            </section>

            {{-- ===== SECTION 3: DANGER ZONE (admin only) ===== --}}
            @if($role === 'admin')
            {{-- Uncomment the block below to activate the danger zone for admins
            <section class="mt-10 pt-6 border-t border-rose-100">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-triangle-exclamation text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-rose-700">{{ __('danger_zone') }}</h3>
                        <p class="text-xs text-rose-400 mt-0.5">{{ __('permanently_delete_account_and_all_data') }}</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">{{ __('delete_your_account_and_all_data_permanently') }}</p>
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-rose-200 text-rose-600 rounded-xl font-bold text-sm hover:bg-rose-50 transition-colors flex-shrink-0">
                        <i class="fas fa-trash-alt text-xs"></i> {{ __('delete_account') }}
                    </button>
                </div>
            </section>
            --}}
            @endif

        </div>{{-- /right hub --}}
    </div>{{-- /grid --}}
</div>
</div>

{{-- ============================================================ --}}
{{-- DELETE ACCOUNT MODAL                                         --}}
{{-- ============================================================ --}}
<x-modal name="confirm-user-deletion" :show="false">
    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
        @csrf @method('delete')
        <h2 class="text-base font-bold text-gray-900">{{ __('are_you_sure') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('please_enter_password_to_confirm_account_deletion') }}</p>
        <div class="mt-4">
            <x-input-label for="password" value="{{ __('password') }}" class="sr-only" />
            <x-text-input id="password" name="password" type="password"
                class="mt-1 block w-full rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:bg-white text-sm px-4 py-2.5 outline-none"
                placeholder="{{ __('enter_password') }}" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close-modal')">{{ __('cancel_2') }}</x-secondary-button>
            <x-danger-button>{{ __('delete_account') }}</x-danger-button>
        </div>
    </form>
</x-modal>

{{-- ============================================================ --}}
{{-- PROFILE PICTURE UPLOAD (shared across all roles)            --}}
{{-- ============================================================ --}}
<script>
    document.getElementById('profile-picture-container').onclick = function () {
        document.getElementById('profile_picture').click();
    };

    document.getElementById('profile_picture').addEventListener('change', async function (e) {
        const file = e.target.files[0];
        const base64Input = document.getElementById('profile_picture_base64');
        if (!file) return;

        try {
            base64Input.value = await compressToBase64(file);
        } catch (err) {
            console.warn('Compression failed, falling back to raw base64:', err);
            base64Input.value = await readFileAsBase64(file);
        }

        this.form.submit();
    });

    function readFileAsBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload  = ev => resolve(ev.target.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    function compressToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onerror = reject;
            reader.onload = ev => {
                const img = new Image();
                img.onerror = reject;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let w = img.width, h = img.height;
                    const MAX = 1920;
                    if (w > MAX || h > MAX) {
                        const r = Math.min(MAX / w, MAX / h);
                        w = Math.round(w * r);
                        h = Math.round(h * r);
                    }
                    canvas.width  = w;
                    canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);

                    let quality = 0.82;
                    (function tryCompress() {
                        canvas.toBlob(blob => {
                            if (!blob) { reject(new Error('Canvas toBlob failed')); return; }
                            if (blob.size <= 1024 * 1024 || quality <= 0.3) {
                                const fr = new FileReader();
                                fr.onload  = ev2 => resolve(ev2.target.result);
                                fr.onerror = reject;
                                fr.readAsDataURL(blob);
                                return;
                            }
                            quality -= 0.05;
                            tryCompress();
                        }, 'image/jpeg', quality);
                    })();
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
</script>
</x-app-layout>
