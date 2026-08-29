<x-app-layout>
    @php
        $profileUrl = $studentProfile?->profile_picture_url ?? $studentProfile?->profile_picture_url;
    @endphp

    <div class="py-12 bg-[#f8fafc] min-h-screen font-['Battambang']">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Form Card --}}
            <div class="bg-white shadow-xl shadow-slate-200/50 rounded-[3rem] overflow-hidden border border-slate-100">
                
                {{-- Header Section --}}
                <div class="relative h-32 bg-gradient-to-r from-emerald-600 to-emerald-500">
                    <div class="absolute -bottom-16 left-0 right-0 flex justify-center">
                        <div class="relative group">
                            {{-- Profile Picture Container --}}
                            <div id="profile-picture-container" class="w-32 h-32 md:w-36 md:h-36 rounded-[2.5rem] bg-white p-1.5 shadow-2xl cursor-pointer overflow-hidden transition-transform active:scale-95">
<div class="w-full h-full rounded-[2rem] overflow-hidden bg-slate-100 flex items-center justify-center">
    @if ($profileUrl)
        {{-- ប្រើ URL ពី ImageKit រួចថែម Parameter សម្រាប់កាត់រូបភាពចំផ្ទៃមុខ (Smart Face Crop) --}}
        <img src="{{ $profileUrl }}?tr=w-400,h-400,fo-face" 
             alt="{{ $user->name }}" 
             class="object-cover w-full h-full" 
             id="profile-picture-preview">
    @else
        <div id="profile-picture-placeholder" class="text-emerald-500 text-4xl font-black">
            {{ Str::upper(Str::substr($user->name, 0, 1)) }}
        </div>
    @endif
</div>
                                {{-- Overlay icon --}}
                                <div class="absolute inset-1.5 bg-black/40 rounded-[2rem] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-camera text-white text-xl"></i>
                                </div>
                            </div>
                            {{-- Badge --}}
                            <div class="absolute bottom-1 right-1 bg-emerald-500 text-white w-8 h-8 rounded-full border-4 border-white flex items-center justify-center shadow-lg">
                                <i class="fas fa-plus text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-20 pb-12 px-8 md:px-16">
                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm font-bold">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm font-bold">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-sm font-bold">
                            <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                            <ul class="list-disc list-inside text-red-700 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="text-center mb-10">
                        <h2 class="text-2xl font-black text-slate-800">{{ __('កែប្រែប្រវត្តិរូប') }}</h2>
                        <p class="text-sm text-slate-400 font-medium mt-1">{{ __('រក្សាទុកព័ត៌មានផ្ទាល់ខ្លួនរបស់អ្នកឱ្យទាន់សម័យ') }}</p>
                    </div>

                    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <input id="profile_picture" name="profile_picture" type="file" class="hidden" accept="image/*" />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Full Name (Khmer) --}}
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('ឈ្មោះពេញ (ខ្មែរ)') }} <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                        <i class="fas fa-user-tag"></i>
                                    </span>
                                    <input type="text" name="full_name_km" id="full_name_km" value="{{ old('full_name_km', $studentProfile->full_name_km ?? '') }}" required 
                                           class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white outline-none transition-all font-bold text-slate-700" 
                                           placeholder="{{ __('បញ្ជាក់ឈ្មោះជាភាសាខ្មែរ') }}">
                                </div>
                                <x-input-error :messages="$errors->get('full_name_km')" class="mt-2" />
                            </div>

                            {{-- Full Name (English) --}}
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('ឈ្មោះពេញ (អង់គ្លេស)') }}</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <input type="text" name="full_name_en" id="full_name_en" value="{{ old('full_name_en', $studentProfile->full_name_en ?? '') }}" 
                                           class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white outline-none transition-all font-bold text-slate-700" 
                                           placeholder="Full Name in English">
                                </div>
                            </div>

                            {{-- Gender --}}
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('ភេទ') }} <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 pointer-events-none">
                                        <i class="fas fa-venus-mars"></i>
                                    </span>
                                    <select id="gender" name="gender" required 
                                            class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white outline-none transition-all font-bold text-slate-700 appearance-none cursor-pointer">
                                        <option value="" disabled selected>{{ __('ជ្រើសរើសភេទ') }}</option>
                                        <option value="male" @if(old('gender', $studentProfile->gender ?? '') == 'male') selected @endif>{{ __('ប្រុស') }}</option>
                                        <option value="female" @if(old('gender', $studentProfile->gender ?? '') == 'female') selected @endif>{{ __('ស្រី') }}</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Date of Birth --}}
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('ថ្ងៃខែឆ្នាំកំណើត') }}</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', isset($studentProfile->date_of_birth) ? \Carbon\Carbon::parse($studentProfile->date_of_birth)->format('Y-m-d') : '') }}" 
                                           class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white outline-none transition-all font-bold text-slate-700">
                                </div>
                            </div>

                            {{-- Phone Number --}}
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('លេខទូរស័ព្ទ') }}</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                        <i class="fas fa-phone-alt"></i>
                                    </span>
                                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $studentProfile->phone_number ?? '') }}" 
                                           class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white outline-none transition-all font-bold text-slate-700" 
                                           placeholder="012 345 678">
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('អាសយដ្ឋាន') }}</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" name="address" id="address" value="{{ old('address', $studentProfile->address ?? '') }}" 
                                           class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white outline-none transition-all font-bold text-slate-700" 
                                           placeholder="{{ __('រាជធានីភ្នំពេញ, កម្ពុជា') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row items-center gap-4 pt-10">
                            <button type="submit" 
                                    class="w-full sm:flex-[2] py-4 bg-emerald-600 text-white rounded-2xl font-black shadow-xl shadow-emerald-100 hover:bg-emerald-700 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i>
                                {{ __('រក្សាទុកការកែប្រែ') }}
                            </button>
                            
                            <a wire:navigate href="{{ route('student.profile.show') }}"
                               class="w-full sm:flex-1 py-4 bg-white border border-slate-200 text-slate-500 rounded-2xl font-black text-center hover:bg-slate-50 transition-all">
                                {{ __('បោះបង់') }}
                            </a>
                        </div>
                    </form>
                </div>
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

        document.getElementById('profile-picture-container').addEventListener('click', function() {
            document.getElementById('profile_picture').click();
        });

        document.getElementById('profile_picture').addEventListener('change', async function(e) {
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
                    img.src = ev.target.result;
                    img.id = 'profile-picture-preview';
                    img.className = 'object-cover w-full h-full';
                    placeholder.replaceWith(img);
                }
            };
            reader.readAsDataURL(file);
        });
    </script>
</x-app-layout>