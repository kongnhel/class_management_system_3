<x-app-layout>
<x-slot name="header">
    <div class="flex items-center justify-between gap-4">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ __('edit_profile') }}</h2>
        <a wire:navigate href="{{ route('professor.profile.show') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">
            <i class="fas fa-arrow-left text-xs"></i>
            {{ __('back_2') }}
        </a>
    </div>
</x-slot>

<div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
<div class="p-6 max-w-7xl mx-auto">

    @php
        $user = $user ?? auth()->user();
        $userProfile = $userProfile ?? $user->userProfile;
        $profilePic = $userProfile?->profile_picture_url;
    @endphp

    {{-- flashes / validation errors --}}
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center gap-3 text-sm font-semibold">
            <i class="fas fa-check-circle text-emerald-500"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center gap-3 text-sm font-semibold">
            <i class="fas fa-exclamation-triangle text-red-500"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="{ tab: 'info' }">

        {{-- ============================================================ --}}
        {{-- LEFT COLUMN - NAVIGATION / AVATAR CARD                      --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm p-6 text-center h-fit">

                {{-- circular avatar with "Change Photo" hover overlay --}}
                <div id="profile-picture-container"
                     class="relative mx-auto w-28 h-28 rounded-full overflow-hidden cursor-pointer group ring-4 ring-slate-100">
                    @if($profilePic)
                        <img id="profile-picture-preview"
                             src="{{ $profilePic }}?tr=w-400,h-400,fo-face,q-auto,f-auto"
                             alt="{{ $user->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div id="profile-picture-placeholder"
                             class="w-full h-full bg-slate-50 flex items-center justify-center text-emerald-600 text-4xl font-semibold">
                            {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    {{-- hover overlay --}}
                    <div class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white">
                        <i class="fas fa-camera text-lg"></i>
                        <span class="text-[10px] font-semibold mt-1">{{ __('change_photo') }}</span>
                    </div>
                </div>

                <p class="text-xs text-slate-400 font-medium mt-3">{{ __('click_the_photo_to_change_it') }}</p>

                {{-- name --}}
                <h3 class="text-base font-semibold text-slate-800 tracking-tight mt-3 break-words leading-relaxed">
                    {{ $userProfile?->full_name_km ?? $user->name }}
                </h3>
                <p class="text-xs text-slate-400 font-medium mt-1 break-all leading-relaxed">{{ $user->email }}</p>

                {{-- settings nav --}}
                <nav class="mt-6 space-y-1 text-left" role="tablist">
                    <button type="button" @click="tab = 'info'"
                            :class="tab === 'info' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700 border border-transparent'"
                            class="w-full flex items-center gap-3 px-4 h-11 rounded-xl text-sm font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                        <i class="fas fa-user text-xs"></i>
                        {{ __('personal_information') }}
                    </button>
                    <button type="button" @click="tab = 'password'"
                            :class="tab === 'password' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700 border border-transparent'"
                            class="w-full flex items-center gap-3 px-4 h-11 rounded-xl text-sm font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40">
                        <i class="fas fa-shield-halved text-xs"></i>
                        {{ __('change_password') }}
                    </button>
                </nav>
        </div>

        {{-- ============================================================ --}}
        {{-- RIGHT COLUMN - FORM WORKSPACE (lg:col-span-3)               --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm p-8">

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-sm font-semibold text-red-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <ul class="list-disc list-inside mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ===== TAB: PERSONAL INFORMATION ===== --}}
                <section x-show="tab === 'info'">
                    <div class="mb-6">
                        <h3 class="text-base font-semibold text-slate-800">{{ __('personal_information') }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ __('update_your_personal_information') }}</p>
                    </div>

                    <form action="{{ route('professor.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <input id="profile_picture" name="" type="file" class="hidden" accept="image/*" />
                        <input type="hidden" id="profile_picture_base64" name="profile_picture_base64" value="" />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col">
                                <label for="full_name_km" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                                    {{ __('full_name_khmer') }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="full_name_km" name="full_name_km" required
                                       value="{{ old('full_name_km', $userProfile?->full_name_km) }}"
                                       placeholder="{{ __('example_full_name_km') }}"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 placeholder:text-slate-400 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            </div>

                            <div class="flex flex-col">
                                <label for="full_name_en" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                                    {{ __('full_name_english') }}
                                </label>
                                <input type="text" id="full_name_en" name="full_name_en"
                                       value="{{ old('full_name_en', $userProfile?->full_name_en) }}"
                                       placeholder="Sovann P"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 placeholder:text-slate-400 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            </div>

                            <div class="flex flex-col">
                                <label for="gender" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                                    {{ __('gender') }} <span class="text-rose-500">*</span>
                                </label>
                                <select id="gender" name="gender" required
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 cursor-pointer">
                                    <option value="" disabled {{ old('gender', $userProfile?->gender) ? '' : 'selected' }}>{{ __('select_2') }}</option>
                                    <option value="male" {{ old('gender', $userProfile?->gender) == 'male' ? 'selected' : '' }}>{{ __('male') }}</option>
                                    <option value="female" {{ old('gender', $userProfile?->gender) == 'female' ? 'selected' : '' }}>{{ __('female') }}</option>
                                    <option value="other" {{ old('gender', $userProfile?->gender) == 'other' ? 'selected' : '' }}>{{ __('other') }}</option>
                                </select>
                            </div>

                            <div class="flex flex-col">
                                <label for="date_of_birth" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                                    {{ __('date_of_birth_2') }}
                                </label>
                                <input type="date" id="date_of_birth" name="date_of_birth"
                                       value="{{ old('date_of_birth', $userProfile?->date_of_birth ? $userProfile->date_of_birth->format('Y-m-d') : '') }}"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            </div>

                            <div class="flex flex-col">
                                <label for="phone_number" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                                    {{ __('phone') }}
                                </label>
                                <input type="text" id="phone_number" name="phone_number"
                                       value="{{ old('phone_number', $userProfile?->phone_number) }}"
                                       placeholder="012 345 678"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 placeholder:text-slate-400 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            </div>

                            <div class="flex flex-col">
                                <label for="telegram_user" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                                    {{ __('Telegram Username') }}
                                </label>
                                <input type="text" id="telegram_user" name="telegram_user"
                                       value="{{ old('telegram_user', $userProfile?->telegram_user) }}"
                                       placeholder="sovann_p"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 placeholder:text-slate-400 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            </div>

                            <div class="flex flex-col md:col-span-2">
                                <label for="address" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                                    {{ __('address') }}
                                </label>
                                <input type="text" id="address" name="address"
                                       value="{{ old('address', $userProfile?->address) }}"
                                       placeholder="{{ __('phnom_penh') }}"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 placeholder:text-slate-400 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        {{-- actions: right-aligned --}}
                        <div class="flex items-center justify-end gap-5 mt-8 pt-6 border-t border-slate-100">
                            <a wire:navigate href="{{ route('professor.profile.show') }}"
                               class="text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
                                {{ __('cancel_2') }}
                            </a>
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-xl transition-all shadow-sm shadow-emerald-500/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 active:scale-[0.98]">
                                {{ __('save_changes') }}
                            </button>
                        </div>
                    </form>
                </section>

                {{-- ===== TAB: CHANGE PASSWORD ===== --}}
                <section x-show="tab === 'password'" x-cloak>
                    <div class="mb-6">
                        <h3 class="text-base font-semibold text-slate-800">{{ __('change_password') }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ __('profile_security') }}</p>
                    </div>

                    <form action="{{ route('password.update') }}" method="POST" class="max-w-md">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div class="flex flex-col">
                                <label for="current_password" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                                    {{ __('profile_current_password') }}
                                </label>
                                <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            </div>

                            <div class="flex flex-col">
                                <label for="password" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                                    {{ __('profile_new_password') }}
                                </label>
                                <input type="password" id="password" name="password" required autocomplete="new-password"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            </div>

                            <div class="flex flex-col">
                                <label for="password_confirmation" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                                    {{ __('profile_confirm_password') }}
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        {{-- actions: right-aligned --}}
                        <div class="flex items-center justify-end gap-5 mt-8 pt-6 border-t border-slate-100">
                            <a wire:navigate href="{{ route('professor.profile.show') }}"
                               class="text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
                                {{ __('cancel_2') }}
                            </a>
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-xl transition-all shadow-sm shadow-emerald-500/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 active:scale-[0.98]">
                                {{ __('save_changes') }}
                            </button>
                        </div>
                    </form>
                </section>

        </div>

    </div>
</div>
</div>

<script>
    var container = document.getElementById('profile-picture-container');
    var input = document.getElementById('profile_picture');

    container.addEventListener('click', function() { input.click(); });

    input.addEventListener('change', async function(e) {
        var file = e.target.files[0];
        var preview = document.getElementById('profile-picture-preview');
        var placeholder = document.getElementById('profile-picture-placeholder');
        var base64Input = document.getElementById('profile_picture_base64');

        if (!file) return;

        var dataUrl;
        try {
            dataUrl = await compressToBase64(file);
        } catch (err) {
            console.error('Compression failed:', err);
            dataUrl = await readFileAsBase64(file);
        }

        base64Input.value = dataUrl;

        if (preview) {
            preview.src = dataUrl;
        } else if (placeholder) {
            var img = document.createElement('img');
            img.id = 'profile-picture-preview';
            img.src = dataUrl;
            img.className = 'object-cover w-full h-full';
            placeholder.replaceWith(img);
        }
    });

    function readFileAsBase64(file) {
        return new Promise(function(resolve, reject) {
            var reader = new FileReader();
            reader.onload = function(ev) { resolve(ev.target.result); };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    function compressToBase64(file) {
        return new Promise(function(resolve, reject) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                var img = new Image();
                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');
                    var w = img.width, h = img.height;
                    if (w > 1920 || h > 1920) {
                        var r = Math.min(1920 / w, 1920 / h);
                        w = Math.round(w * r);
                        h = Math.round(h * r);
                    }
                    canvas.width = w;
                    canvas.height = h;
                    ctx.drawImage(img, 0, 0, w, h);
                    var quality = 0.8;
                    function tryCompress() {
                        canvas.toBlob(function(blob) {
                            if (!blob) return reject('Canvas failed');
                            if (blob.size <= 1024 * 1024 || quality <= 0.3) {
                                var fr = new FileReader();
                                fr.onload = function(ev) { resolve(ev.target.result); };
                                fr.onerror = reject;
                                fr.readAsDataURL(blob);
                                return;
                            }
                            quality -= 0.05;
                            tryCompress();
                        }, 'image/jpeg', quality);
                    }
                    tryCompress();
                };
                img.onerror = reject;
                img.src = ev.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }
</script>
</x-app-layout>
