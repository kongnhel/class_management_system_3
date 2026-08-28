<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl md:text-4xl text-gray-900 leading-tight tracking-wide">
            {{ __('កែប្រែប្រវត្តិរូប') }}
        </h2>
    </x-slot>

    <div class="py-6 md:py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-2 md:px-6 lg:px-10">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl p-4 md:p-12 border border-gray-100">
                
                {{-- Form Header --}}
                <div class="text-center mb-8">
                    <h3 class="text-xl md:text-3xl font-extrabold text-green-700">{{ __('កែប្រែព័ត៌មានផ្ទាល់ខ្លួន') }}</h3>
                    <p class="text-xs md:text-sm text-gray-500 mt-1 italic">{{ __('សូមបំពេញព័ត៌មានខាងក្រោមឱ្យបានត្រឹមត្រូវ') }}</p>
                </div>
            {{-- Alerts --}}
         @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 md:p-5 rounded-xl mb-6 shadow-sm flex items-center animate-bounce" role="alert">
                        <i class="fas fa-check-circle mr-3 text-green-500 text-xl"></i>
                        <span class="font-bold text-sm md:text-lg">{{ session('success') }}</span>
                    </div>
                @endif
                {{-- Display Global Errors --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-sm">
                        <ul class="list-disc list-inside text-xs md:text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('professor.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5 md:gap-y-8">
                        
                        {{-- Profile Picture Section --}}
                        <div class="col-span-1 md:col-span-2 flex flex-col items-center justify-center space-y-3 mb-4">
                            <div class="relative w-28 h-28 md:w-36 md:h-36 rounded-full overflow-hidden border-4 border-green-400 shadow-lg group cursor-pointer" id="profile-picture-container">
    @if ($userProfile->profile_picture_url)
        <img 
            {{-- បន្ថែម ?tr=w-400,h-400,fo-face ដើម្បីឱ្យ ImageKit កាត់រូបភាពចំផ្ទៃមុខអូតូ --}}
            src="{{ $userProfile->profile_picture_url }}?tr=w-400,h-400,fo-face tr=q-auto,f-auto" 
            alt="{{ $user->name }}"  
            class="object-cover w-full h-full transition-all duration-300"
            id="profile-picture-preview"
        >
    @else
        <div id="profile-picture-placeholder"
             class="w-full h-full bg-green-100 flex items-center justify-center text-green-600 text-4xl md:text-6xl font-black">
            {{ Str::upper(Str::substr($user->name, 0, 1)) }}
        </div>
    @endif

    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
        <i class="fas fa-camera text-white text-xl md:text-2xl"></i>
    </div>
</div>
                            {{-- សំខាន់៖ ឈ្មោះ input ត្រូវតែ "profile_picture" ឱ្យត្រូវតាម Controller --}}
                            <input id="profile_picture" name="profile_picture" type="file" class="hidden" accept="image/*" />
                            <p class="text-[10px] md:text-xs text-gray-400 font-medium">{{ __('ចុចលើរូបដើម្បីផ្លាស់ប្តូរ') }}</p>
                        </div>

                        {{-- Input Fields Section --}}
                        @php
                            $fields = [
                                ['id' => 'full_name_km', 'label' => __('ឈ្មោះពេញ (ខ្មែរ)'), 'type' => 'text', 'placeholder' => 'សុវណ្ណ ភី', 'required' => true, 'icon' => 'fas fa-user'],
                                ['id' => 'full_name_en', 'label' => __('ឈ្មោះពេញ (អង់គ្លេស)'), 'type' => 'text', 'placeholder' => 'Sovann P', 'required' => false, 'icon' => 'fas fa-font'],
                                ['id' => 'gender', 'label' => __('ភេទ'), 'type' => 'select', 'required' => true, 'icon' => 'fas fa-venus-mars'],
                                ['id' => 'date_of_birth', 'label' => __('ថ្ងៃខែឆ្នាំកំណើត'), 'type' => 'date', 'required' => false, 'icon' => 'fas fa-calendar-day'],
                                ['id' => 'phone_number', 'label' => __('លេខទូរស័ព្ទ'), 'type' => 'text', 'placeholder' => '012345678', 'required' => false, 'icon' => 'fas fa-phone'],
                                ['id' => 'telegram_user', 'label' => __('Telegram Username'), 'type' => 'text', 'placeholder' => 'sovann_p', 'required' => false, 'icon' => 'fab fa-telegram-plane', 'color' => 'sky'],
                                ['id' => 'address', 'label' => __('អាសយដ្ឋាន'), 'type' => 'text', 'placeholder' => 'ភ្នំពេញ', 'required' => false, 'icon' => 'fas fa-map-marker-alt', 'full' => true],
                            ];
                        @endphp

                        @foreach($fields as $field)
                            <div class="{{ isset($field['full']) ? 'md:col-span-2' : '' }}">
                                <label for="{{ $field['id'] }}" class="block text-xs md:text-sm font-bold text-gray-700 mb-1 ml-1">
                                    {{ __($field['label']) }} @if($field['required']) <span class="text-red-500">*</span> @endif
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-green-500 transition-colors">
                                        <i class="{{ $field['icon'] }} {{ isset($field['color']) ? 'text-'.$field['color'].'-500' : '' }} text-xs md:text-sm"></i>
                                    </div>

                                    @if($field['type'] == 'select')
                                        <select name="{{ $field['id'] }}" id="{{ $field['id'] }}" required class="block w-full pl-10 pr-3 py-2 md:py-3 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm md:text-base transition-all">
                                            <option value="" disabled>{{ __('ជ្រើសរើស') }}</option>
                                            <option value="male" {{ old('gender', $userProfile->gender) == 'male' ? 'selected' : '' }}>{{ __('ប្រុស') }}</option>
                                            <option value="female" {{ old('gender', $userProfile->gender) == 'female' ? 'selected' : '' }}>{{ __('ស្រី') }}</option>
                                        </select>
                                    @else
                                        <input type="{{ $field['type'] }}" name="{{ $field['id'] }}" id="{{ $field['id'] }}" 
                                            value="{{ old($field['id'], $field['id'] == 'date_of_birth' && $userProfile->date_of_birth ? $userProfile->date_of_birth->format('Y-m-d') : $userProfile->{$field['id']}) }}" 
                                            {{ $field['required'] ? 'required' : '' }}
                                            placeholder="{{ __($field['placeholder'] ?? '') }}"
                                            class="block w-full pl-10 pr-3 py-2 md:py-3 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm md:text-base transition-all">
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        {{-- Action Buttons --}}
                        <div class="md:col-span-2 flex flex-col md:flex-row justify-center items-center gap-3 mt-4">
                            <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-12 py-3 md:py-4 bg-green-600 text-white font-bold rounded-full hover:bg-green-700 transition-all transform hover:scale-105 shadow-lg text-sm md:text-lg">
                                <i class="fas fa-save mr-2"></i> {{ __('រក្សាទុក') }}
                            </button>
                            <a wire:navigate href="{{ route('professor.profile.show') }}" class="w-full md:w-auto inline-flex items-center justify-center px-12 py-3 md:py-4 bg-white border border-gray-300 text-gray-700 font-bold rounded-full hover:bg-gray-50 transition-all text-sm md:text-lg shadow-sm">
                                {{ __('បោះបង់') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function compressImage(file) {
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
                                    resolve(new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }));
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

        const container = document.getElementById('profile-picture-container');
        const input = document.getElementById('profile_picture');

        container.addEventListener('click', () => input.click());

        input.addEventListener('change', async function(e) {
            var file = e.target.files[0];
            var preview = document.getElementById('profile-picture-preview');
            var placeholder = document.getElementById('profile-picture-placeholder');

            if (!file) return;

            if (file.size > 1024 * 1024) {
                try {
                    var compressed = await compressImage(file);
                    var dt = new DataTransfer();
                    dt.items.add(compressed);
                    e.target.files = dt.files;
                    file = compressed;
                } catch (err) {
                    console.error('Compression failed:', err);
                }
            }

            var reader = new FileReader();
            reader.onload = function(ev) {
                if (preview) {
                    preview.src = ev.target.result;
                } else if (placeholder) {
                    var img = document.createElement('img');
                    img.id = 'profile-picture-preview';
                    img.src = ev.target.result;
                    img.className = 'object-cover w-full h-full transition-all duration-300';
                    placeholder.replaceWith(img);
                }
            };
            reader.readAsDataURL(file);
        });
    </script>
</x-app-layout>