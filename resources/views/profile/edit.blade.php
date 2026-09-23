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
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-5">

    {{-- ============================================================ --}}
    {{-- FLASH MESSAGES                                               --}}
    {{-- ============================================================ --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl flex items-center justify-between gap-3 text-sm font-bold">
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
            class="bg-rose-50 border border-rose-200 px-5 py-4 rounded-2xl flex items-start justify-between gap-3">
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
    {{-- HERO                                                         --}}
    {{-- ============================================================ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $heroGradient }} shadow-xl shadow-black/10">
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-10 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative px-6 py-6 sm:px-8 flex items-center gap-4">
            <a href="{{ url()->previous() }}"
                class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 flex items-center justify-center transition-colors flex-shrink-0">
                <i class="fas fa-arrow-left text-white text-sm"></i>
            </a>
            <div>
                <div class="inline-flex items-center gap-1.5 bg-white/10 border border-white/15 text-white/80 text-[11px] font-bold px-3 py-1 rounded-full mb-1.5">
                    <i class="{{ $roleIcon }} text-[10px]"></i>
                    {{ $roleLabel }}
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white leading-tight">{{ __('edit_profile') }}</h2>
                <p class="{{ $heroSubtext }} text-xs mt-0.5">{{ __('manage_personal_info_and_account_security') }}</p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MAIN GRID                                                    --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        {{-- ===== LEFT: Profile Picture Card ===== --}}
        <div class="lg:col-span-4">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">

                {{-- mini hero stripe matches the page hero --}}
                <div class="h-20 bg-gradient-to-br {{ $heroGradient }}"></div>

                <div class="px-6 pb-8">
                    <div class="flex justify-center -mt-12 mb-5">
                        <form method="post" action="{{ route('profile.update-picture') }}"
                            enctype="multipart/form-data" id="picture-form">
                            @csrf
                            <div class="relative group cursor-pointer" id="profile-picture-container">
                                <div class="w-24 h-24 rounded-2xl border-4 border-white shadow-lg overflow-hidden {{ $avatarPlaceholder }}">
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
                                <div class="absolute inset-0 bg-black/40 rounded-2xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-camera text-white text-lg"></i>
                                </div>
                                <input id="profile_picture" name="" type="file" class="hidden" accept="image/*" />
                                <input type="hidden" id="profile_picture_base64" name="profile_picture_base64" value="" />
                            </div>
                        </form>
                    </div>

                    <div class="text-center">
                        <h3 class="text-base font-black text-gray-900">{{ $user->name }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $user->email }}</p>

                        {{-- student ID shown only for students --}}
                        @if($role === 'student' && $user->student_id_code)
                            <p class="text-[11px] font-bold text-emerald-600 mt-1">{{ $user->student_id_code }}</p>
                        @endif

                        <span class="mt-3 inline-flex items-center gap-1.5 {{ $accentBadge }} text-[11px] font-bold px-3 py-1 rounded-full">
                            <i class="{{ $roleIcon }} text-[10px]"></i> {{ $roleLabel }}
                        </span>
                    </div>

                    <p class="text-center text-[11px] text-gray-400 mt-4">{{ __('click_on_photo_to_change') }}</p>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT: Form Sections ===== --}}
        <div class="lg:col-span-8 space-y-5">

            {{-- Section 1: Basic Information --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-50">
                    <div class="w-8 h-8 rounded-xl {{ $section1Badge }} flex items-center justify-center text-sm font-black">1</div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">{{ __('basic_information') }}</h3>
                        <p class="text-[11px] text-gray-400">{{ __('manage_your_personal_information') }}</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="p-6 space-y-4">
                    @csrf @method('patch')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-600 mb-1.5">
                                {{ __('user_name') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="name" name="name" type="text"
                                value="{{ old('name', $user->name) }}"
                                required autofocus
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 text-gray-900 {{ $accentRing }} focus:bg-white transition text-sm px-4 py-2.5 outline-none" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-600 mb-1.5">
                                {{ __('email_2') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="email" name="email" type="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 text-gray-900 {{ $accentRing }} focus:bg-white transition text-sm px-4 py-2.5 outline-none" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>
                    </div>

                    {{-- Student ID — only visible for students ──────── --}}
                    @if($role === 'student')
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">{{ __('student_id') }}</label>
                            <div class="flex items-center gap-3 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 cursor-not-allowed">
                                <i class="fas fa-id-badge text-slate-400 text-sm"></i>
                                <span class="text-sm text-slate-500 font-semibold">{{ $user->student_id_code ?? '—' }}</span>
                                <span class="ml-auto text-[10px] font-bold bg-slate-100 text-slate-400 px-2 py-0.5 rounded-md">{{ __('read_only') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end pt-1">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 {{ $saveBtnClass }} text-white rounded-xl font-bold text-sm transition-colors shadow-sm">
                            <i class="fas fa-save text-xs"></i> {{ __('save_2') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Section 2: Password ─────────────────────────────────── --}}
            {{-- Always purple — same across all roles                  --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-50">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-black">2</div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">{{ __('account_security') }}</h3>
                        <p class="text-[11px] text-gray-400">{{ __('keep_your_account_secure_by_changing_password_regularly') }}</p>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="p-6 space-y-4">
                    @csrf @method('put')

                    <div>
                        <label for="current_password" class="block text-xs font-bold text-gray-600 mb-1.5">
                            {{ __('current_password') }} <span class="text-rose-500">*</span>
                        </label>
                        <input id="current_password" name="current_password" type="password"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 text-gray-900 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:bg-white transition text-sm px-4 py-2.5 outline-none" />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-bold text-gray-600 mb-1.5">
                                {{ __('new_password_2') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="password" name="password" type="password"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 text-gray-900 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:bg-white transition text-sm px-4 py-2.5 outline-none" />
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-gray-600 mb-1.5">
                                {{ __('confirm_new_password_2') }} <span class="text-rose-500">*</span>
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 text-gray-900 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:bg-white transition text-sm px-4 py-2.5 outline-none" />
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-sm transition-colors shadow-sm shadow-purple-200">
                            <i class="fas fa-key text-xs"></i> {{ __('update_password') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Section 3: Danger Zone — admin only ─────────────────── --}}
            @if($role === 'admin')
            {{-- Uncomment the block below to activate the danger zone for admins
            <div class="bg-white rounded-3xl border border-rose-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 px-6 py-4 border-b border-rose-50">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-black">3</div>
                    <div>
                        <h3 class="text-sm font-bold text-rose-700">{{ __('danger_zone') }}</h3>
                        <p class="text-[11px] text-rose-400">{{ __('permanently_delete_account_and_all_data') }}</p>
                    </div>
                </div>
                <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">{{ __('delete_your_account_and_all_data_permanently') }}</p>
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-rose-200 text-rose-600 rounded-xl font-bold text-sm hover:bg-rose-50 transition-colors flex-shrink-0">
                        <i class="fas fa-trash-alt text-xs"></i> {{ __('delete_account') }}
                    </button>
                </div>
            </div>
            --}}
            @endif

        </div>{{-- /right --}}
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