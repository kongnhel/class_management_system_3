<x-app-layout>
<x-slot name="header">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ __('edit_student_profile') }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ __('edit_your_profile_information') }}</p>
        </div>
        <a wire:navigate href="{{ route('student.profile.show') }}"
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
        $studentProfile = $studentProfile ?? $user->studentProfile;
        $profileUrl = $studentProfile?->profile_picture_url ?? $user->userProfile?->profile_picture_url;
    @endphp

    {{-- Error Banner --}}
    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-red-700 text-sm">{{ __('there_is_a_problem') }}</p>
                    <ul class="text-red-600 text-xs mt-1 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-red-400 hover:text-red-600"><i class="fas fa-times text-xs"></i></button>
            </div>
        </div>
    @endif

    {{-- Success Message --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-emerald-700 text-sm">{{ __('success_2') }}</p>
                    <p class="text-emerald-600 text-xs mt-1">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600"><i class="fas fa-times text-xs"></i></button>
            </div>
        </div>
    @endif

    {{-- MAIN SPLIT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- ============================================================ --}}
        {{-- LEFT COLUMN - AVATAR / IDENTITY CARD                         --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm p-6 text-center h-fit">

            {{-- circular avatar with change-photo hover overlay --}}
            <div id="profile-picture-container"
                 class="relative mx-auto w-28 h-28 rounded-full overflow-hidden cursor-pointer group ring-4 ring-slate-100">
                @if($profileUrl)
                    <img id="profile-picture-preview" src="{{ $profileUrl }}?tr=w-400,h-400,fo-face,q-auto,f-auto"
                         alt="{{ $user->name }}" class="w-full h-full object-cover">
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

            {{-- remove current picture --}}
            @if($profileUrl)
                <div class="mt-3">
                    <label for="remove_profile_picture" class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remove_profile_picture" id="remove_profile_picture" value="1"
                               class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                        <span class="text-xs text-red-500 font-medium">{{ __('remove_current_profile_picture') }}</span>
                    </label>
                </div>
            @endif

            {{-- name + email --}}
            <h3 class="text-base font-semibold text-slate-800 tracking-tight mt-4 break-words leading-relaxed">
                {{ $studentProfile?->full_name_km ?? $user->name }}
            </h3>
            <p class="text-xs text-slate-400 font-medium mt-1 break-all leading-relaxed">{{ $user->email }}</p>
        </div>

        {{-- ============================================================ --}}
        {{-- RIGHT COLUMN - FORM WORKSPACE                                --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm p-8">

            <div class="mb-6">
                <h3 class="text-base font-semibold text-slate-800">{{ __('personal_information') }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ __('update_your_personal_information') }}</p>
            </div>

            <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="file" id="profile_picture" name="" class="hidden" accept="image/*" />
                <input type="hidden" id="profile_picture_base64" name="profile_picture_base64" value="" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col">
                        <label for="full_name_km" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                            {{ __('full_name_khmer') }} <span class="text-rose-500">*</span>
                        </label>
                        <input id="full_name_km" type="text" name="full_name_km"
                               value="{{ old('full_name_km', $studentProfile->full_name_km ?? '') }}"
                               placeholder="{{ __('confirm_your_name_in_khmer') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 placeholder:text-slate-400 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        <x-input-error :messages="$errors->get('full_name_km')" class="mt-2" />
                    </div>

                    <div class="flex flex-col">
                        <label for="full_name_en" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                            {{ __('full_name_english') }}
                        </label>
                        <input id="full_name_en" type="text" name="full_name_en"
                               value="{{ old('full_name_en', $studentProfile->full_name_en ?? '') }}"
                               placeholder="{{ __('full_name_in_english') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 placeholder:text-slate-400 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        <x-input-error :messages="$errors->get('full_name_en')" class="mt-2" />
                    </div>

                    <div class="flex flex-col">
                        <label for="gender" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                            {{ __('gender') }} <span class="text-rose-500">*</span>
                        </label>
                        <select id="gender" name="gender"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 cursor-pointer">
                            <option value="">{{ __('select_gender') }}</option>
                            <option value="male" {{ old('gender', $studentProfile->gender ?? '') == 'male' ? 'selected' : '' }}>{{ __('male') }}</option>
                            <option value="female" {{ old('gender', $studentProfile->gender ?? '') == 'female' ? 'selected' : '' }}>{{ __('female') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <div class="flex flex-col">
                        <label for="date_of_birth" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                            {{ __('date_of_birth') }}
                        </label>
                        <input id="date_of_birth" type="date" name="date_of_birth"
                               value="{{ old('date_of_birth', optional($studentProfile->date_of_birth)->format('Y-m-d') ?? '') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>

                    <div class="flex flex-col">
                        <label for="phone_number" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                            {{ __('phone_number') }}
                        </label>
                        <input id="phone_number" type="text" name="phone_number"
                               value="{{ old('phone_number', $studentProfile->phone_number ?? '') }}"
                               placeholder="012 345 678"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 placeholder:text-slate-400 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                    </div>

                    <div class="flex flex-col">
                        <label for="address" class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">
                            {{ __('address') }}
                        </label>
                        <input id="address" type="text" name="address"
                               value="{{ old('address', $studentProfile->address ?? '') }}"
                               placeholder="{{ __('phnom_penh_cambodia') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-800 placeholder:text-slate-400 transition-all focus:outline-none focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                </div>

                {{-- actions: right-aligned --}}
                <div class="flex items-center justify-end gap-5 mt-8 pt-6 border-t border-slate-100">
                    <a wire:navigate href="{{ route('student.profile.show') }}"
                       class="text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
                        {{ __('cancel_2') }}
                    </a>
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-xl transition-all shadow-sm shadow-emerald-500/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 active:scale-[0.98]">
                        {{ __('save_changes') }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
</div>

<script>
    document.getElementById('profile-picture-container').addEventListener('click', function() {
        document.getElementById('profile_picture').click();
    });

    document.getElementById('profile_picture').addEventListener('change', async function(event) {
        var file = event.target.files[0];
        var previewElement = document.getElementById('profile-picture-preview');
        var placeholderElement = document.getElementById('profile-picture-placeholder');
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

        if (previewElement) {
            previewElement.src = dataUrl;
        } else if (placeholderElement) {
            var img = document.createElement('img');
            img.id = 'profile-picture-preview';
            img.src = dataUrl;
            img.alt = '{{ __("profile_picture") }}';
            img.className = 'w-full h-full object-cover';
            placeholderElement.replaceWith(img);
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
