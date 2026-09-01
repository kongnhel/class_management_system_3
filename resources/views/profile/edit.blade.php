<x-app-layout>
    @php
        $user = Auth::user()->loadMissing('userProfile');
        $profileUrl = $user->userProfile?->profile_picture_url;
    @endphp

    <div class="min-h-screen bg-gray-50">
        {{-- Dark Gradient Header --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-16 pt-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <a href="{{ url()->previous() }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="fas fa-arrow-left text-white"></i>
                    </a>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                            <i class="fas fa-user-cog text-emerald-300 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight">{{ __('កែប្រែប្រវត្តិរូប') }}</h2>
                            <p class="text-slate-400 mt-1 text-sm">{{ __('គ្រប់គ្រងព័ត៌មានផ្ទាល់ខ្លួន និងការកំណត់សុវត្ថិភាពរបស់អ្នក') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 pb-12 relative z-10">

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

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- Left Side: Profile Summary Card --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="h-32 bg-gradient-to-br from-slate-800 to-slate-900"></div>
                        <div class="px-6 pb-8">
                            <div class="relative flex justify-center -mt-16 mb-6">
                                <form method="post" action="{{ route('profile.update-picture') }}" enctype="multipart/form-data" id="picture-form">
                                    @csrf
                                    <div class="relative group cursor-pointer" id="profile-picture-container">
                                        <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-gray-100">
                                            @if ($profileUrl)
                                                <img src="{{ $profileUrl }}" id="profile-picture-preview" class="w-full h-full object-cover">
                                            @else
                                                <div id="profile-picture-placeholder" class="w-full h-full flex items-center justify-center text-3xl font-bold text-gray-400">
                                                    {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <i class="fas fa-camera text-white text-xl"></i>
                                        </div>
                                        <input id="profile_picture" name="" type="file" class="hidden" accept="image/*" />
                                        <input type="hidden" id="profile_picture_base64" name="profile_picture_base64" value="" />
                                    </div>
                                </form>
                            </div>
                            <div class="text-center">
                                <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                                    {{ $user->role }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Settings Forms --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- Section 1: Profile Information --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <span class="text-emerald-600 font-bold text-sm">1</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ __('ព័ត៌មានមូលដ្ឋាន') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('កែប្រែឈ្មោះ និងអ៊ីម៉ែលរបស់អ្នក') }}</p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                            @csrf @method('patch')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="name" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ឈ្មោះអ្នកប្រើប្រាស់') }} <span class="text-red-500">*</span></label>
                                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                                        class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('អ៊ីម៉ែល') }} <span class="text-red-500">*</span></label>
                                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required placeholder="example@gmail.com"
                                        class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('លេខសម្គាល់') }}</label>
                                <input type="text" value="{{ $user->student_id_code }}" readonly disabled
                                    class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-500 px-4 py-2.5 cursor-not-allowed text-sm" />
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-600 rounded-xl font-bold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all shadow-lg shadow-emerald-200 text-sm">
                                    <i class="fas fa-save"></i> {{ __('រក្សាទុក') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Section 2: Password Security --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                                <span class="text-purple-600 font-bold text-sm">2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ __('ពាក្យសម្ងាត់ និងសុវត្ថិភាព') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('កំណត់ពាក្យសម្ងាត់ថ្មីសម្រាប់គណនីរបស់អ្នក') }}</p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                            @csrf @method('put')
                            <div>
                                <label for="current_password" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ពាក្យសម្ងាត់បច្ចុប្បន្ន') }} <span class="text-red-500">*</span></label>
                                <input id="current_password" name="current_password" type="password"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ពាក្យសម្ងាត់ថ្មី') }} <span class="text-red-500">*</span></label>
                                    <input id="password" name="password" type="password"
                                        class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('បញ្ជាក់ពាក្យសម្ងាត់ថ្មី') }} <span class="text-red-500">*</span></label>
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-purple-600 rounded-xl font-bold text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all shadow-lg shadow-purple-200 text-sm">
                                    <i class="fas fa-key"></i> {{ __('កែប្រែពាក្យសម្ងាត់') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Section 3: Danger Zone --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-red-200 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                                <span class="text-red-600 font-bold text-sm">3</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-red-700">{{ __('តំបន់គ្រោះថ្នាក់') }}</h3>
                                <p class="text-xs text-red-500">{{ __('ការលុបគណនីមិនអាចដកហូតបានទេ') }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <p class="text-sm text-gray-600">{{ __('ប្រសិនបើអ្នកលុបគណនី ទិន្នន័យទាំងអស់នឹងត្រូវបាត់បង់ជាអច្ឆិត្រ។') }}</p>
                            </div>
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-red-200 text-red-600 rounded-xl font-bold hover:bg-red-50 transition-all shadow-sm text-sm flex-shrink-0">
                                <i class="fas fa-trash-alt"></i> {{ __('លុបគណនី') }}
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Delete Account Modal --}}
    <x-modal name="confirm-user-deletion" :show="false">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf @method('delete')
            <h2 class="text-lg font-bold text-gray-900">{{ __('តើអ្នកប្រាកដទេ?') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('សូមបញ្ចូលពាក្យសម្ងាត់ដើម្បីបញ្ជាក់ថាអ្នកពិតជាចង់លុបគណនីនេះ។') }}</p>
            <div class="mt-4">
                <x-input-label for="password" value="{{ __('ពាក្យសម្ងាត់') }}" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" placeholder="{{ __('បញ្ចូលពាក្យសម្ងាត់') }}" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close-modal')">{{ __('បោះបង់') }}</x-secondary-button>
                <x-danger-button>{{ __('លុបគណនី') }}</x-danger-button>
            </div>
        </form>
    </x-modal>

    <script>
        document.getElementById('profile-picture-container').onclick = function() { document.getElementById('profile_picture').click(); };

        document.getElementById('profile_picture').addEventListener('change', async function(e) {
            var file = e.target.files[0];
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
            this.form.submit();
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
