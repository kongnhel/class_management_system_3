<x-app-layout>
    @php
        $profileUrl = $studentProfile?->profile_picture_url ?? $user->userProfile?->profile_picture_url;
    @endphp

    <div class="min-h-screen bg-gray-50">
        {{-- Dark Gradient Header --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-16 pt-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <a href="{{ url()->previous() }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="fas fa-arrow-left text-white"></i>
                    </a>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                            <i class="fas fa-user-edit text-emerald-300 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight">{{ __('កែប្រែប្រវត្តិរូបនិស្សិត') }}</h2>
                            <p class="text-slate-400 mt-1 text-sm">{{ __('កែប្រែព័ត៌មាន Profile របស់អ្នក') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 pb-12 relative z-10">

            {{-- Error Banner --}}
            @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 text-sm">{{ __('មានបញ្ហា!') }}</p>
                        <ul class="text-red-600 text-xs mt-1 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xs"></i></button>
                </div>
            </div>
            @endif

            {{-- Success Message --}}
            @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 text-sm">{{ __('ជោគជ័យ!') }}</p>
                        <p class="text-emerald-600 text-xs mt-1">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xs"></i></button>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Section 1: Profile Picture --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <span class="text-emerald-600 font-bold text-sm">1</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('រូបភាព Profile') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('ចុចលើរូបភាពដើម្បីផ្លាស់ប្តូរ') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="relative group cursor-pointer" id="profile-picture-container">
                            <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-gray-100">
                                @if($profileUrl)
                                    <img id="profile-picture-preview" src="{{ $profileUrl }}?tr=q-auto,f-auto" alt="{{ $user->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div id="profile-picture-placeholder"
                                        class="w-full h-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-4xl font-bold">
                                        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-full">
                                    <i class="fas fa-camera text-white text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <input type="file" id="profile_picture" name="" class="hidden" accept="image/*" />
                        <input type="hidden" id="profile_picture_base64" name="profile_picture_base64" value="" />
                        <p class="text-xs text-gray-400 font-medium mt-3">{{ __('ចុចលើរូបដើម្បីផ្លាស់ប្តូរ') }}</p>

                        @if($profileUrl)
                        <div class="mt-3">
                            <label for="remove_profile_picture" class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remove_profile_picture" id="remove_profile_picture" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                <span class="text-sm text-red-600 font-medium">{{ __('លុបរូបភាព Profile បច្ចុប្បន្ន') }}</span>
                            </label>
                        </div>
                        @endif
                        <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                    </div>
                </div>

                {{-- Section 2: Profile Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                            <span class="text-purple-600 font-bold text-sm">2</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('ព័ត៌មានផ្ទាល់ខ្លួន') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('កែប្រែព័ត៌មានផ្ទាល់ខ្លួនរបស់អ្នក') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="full_name_km" class="block text-sm font-bold text-gray-700 mb-1.5">
                                <i class="fas fa-user mr-1.5 text-purple-500"></i> {{ __('ឈ្មោះពេញ (ខ្មែរ)') }} <span class="text-red-500">*</span>
                            </label>
                            <input id="full_name_km" type="text" name="full_name_km" value="{{ old('full_name_km', $studentProfile->full_name_km ?? '') }}"
                                class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                            <x-input-error :messages="$errors->get('full_name_km')" class="mt-2" />
                        </div>
                        <div>
                            <label for="full_name_en" class="block text-sm font-bold text-gray-700 mb-1.5">
                                <i class="fas fa-font mr-1.5 text-purple-500"></i> {{ __('ឈ្មោះពេញ (អង់គ្លេស)') }}
                            </label>
                            <input id="full_name_en" type="text" name="full_name_en" value="{{ old('full_name_en', $studentProfile->full_name_en ?? '') }}"
                                class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                            <x-input-error :messages="$errors->get('full_name_en')" class="mt-2" />
                        </div>
                        <div>
                            <label for="gender" class="block text-sm font-bold text-gray-700 mb-1.5">
                                <i class="fas fa-venus-mars mr-1.5 text-purple-500"></i> {{ __('ភេទ') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="gender" name="gender"
                                class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5">
                                <option value="">{{ __('ជ្រើសរើសភេទ') }}</option>
                                <option value="male" {{ old('gender', $studentProfile->gender ?? '') == 'male' ? 'selected' : '' }}>{{ __('ប្រុស') }}</option>
                                <option value="female" {{ old('gender', $studentProfile->gender ?? '') == 'female' ? 'selected' : '' }}>{{ __('ស្រី') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                        </div>
                        <div>
                            <label for="date_of_birth" class="block text-sm font-bold text-gray-700 mb-1.5">
                                <i class="fas fa-calendar-day mr-1.5 text-purple-500"></i> {{ __('ថ្ងៃខែឆ្នាំកំណើត') }}
                            </label>
                            <input id="date_of_birth" type="date" name="date_of_birth"
                                value="{{ old('date_of_birth', optional($studentProfile->date_of_birth)->format('Y-m-d') ?? '') }}"
                                class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                        </div>
                        <div>
                            <label for="phone_number" class="block text-sm font-bold text-gray-700 mb-1.5">
                                <i class="fas fa-phone mr-1.5 text-purple-500"></i> {{ __('លេខទូរស័ព្ទ') }}
                            </label>
                            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number', $studentProfile->phone_number ?? '') }}" placeholder="012 345 678"
                                class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-bold text-gray-700 mb-1.5">
                                <i class="fas fa-map-marker-alt mr-1.5 text-purple-500"></i> {{ __('អាសយដ្ឋាន') }}
                            </label>
                            <input id="address" type="text" name="address" value="{{ old('address', $studentProfile->address ?? '') }}"
                                class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition text-sm">
                            <i class="fas fa-times"></i> {{ __('បោះបង់') }}
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-600 rounded-xl font-bold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all shadow-lg shadow-emerald-200 text-sm">
                            <i class="fas fa-save"></i> {{ __('រក្សាទុកការកែប្រែ') }}
                        </button>
                    </div>
                </div>
            </form>
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
                img.alt = 'Profile Picture';
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
