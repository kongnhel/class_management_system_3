<x-app-layout>
    @php
        // ទាញយក URL រូបភាពពី ImgBB ដោយផ្ទាល់ចេញពី userProfile
        $profileUrl = $user->userProfile?->profile_picture_url;
    @endphp

    <div class="py-12 bg-[#f8fafc] min-h-screen font-['Battambang']">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Form Card --}}
            <div class="bg-white shadow-xl shadow-slate-200/50 rounded-[3rem] overflow-hidden border border-slate-100">
                
                {{-- Header Section --}}
                <div class="relative h-32 bg-gradient-to-r from-indigo-600 to-blue-500">
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
        <div id="profile-picture-placeholder" class="text-indigo-500 text-4xl font-black">
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
                    <div class="text-center mb-10">
                        <h2 class="text-2xl font-black text-slate-800">{{ __('កែប្រែប្រវត្តិរូប') }}</h2>
                        <p class="text-sm text-slate-400 font-medium mt-1">{{ __('រក្សាទុកព័ត៌មានផ្ទាល់ខ្លួនរបស់អ្នកឱ្យទាន់សម័យ') }}</p>
                    </div>

{{-- Technology 2050 "Neural-Matrix" Toast - Khmer Edition --}}
@if (session('success') || session('error'))
<style>
    @import url('https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&display=swap');
    
    .khmer-font {
        font-family: 'Kantumruy Pro', sans-serif;
    }

    /* Quantum Flicker Entry */
    @keyframes quantum-flicker {
        0% { opacity: 0; filter: brightness(2) contrast(2); transform: scaleY(0.005) scaleX(1.1); }
        10% { opacity: 0.8; transform: scaleY(1.1) scaleX(0.9); }
        20% { opacity: 0.3; transform: scaleY(0.9) scaleX(1.1); }
        100% { opacity: 1; filter: brightness(1) contrast(1); transform: scale(1); }
    }

    /* Corner Bracket Tracking */
    @keyframes corner-track {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(-2px, -2px); }
    }

    /* Floating Data Bits */
    @keyframes data-flow {
        0% { transform: translateY(0); opacity: 0; }
        50% { opacity: 0.5; }
        100% { transform: translateY(-20px); opacity: 0; }
    }

    .animate-quantum {
        animation: quantum-flicker 0.4s cubic-bezier(0.19, 1, 0.22, 1) forwards;
    }

    .bit-segment {
        mask-image: linear-gradient(to right, black 70%, transparent 70%);
        mask-size: 8px 100%;
    }
</style>

<div 
    x-data="{ 
        show: false, 
        progress: 100,
        startTimer() {
            this.show = true;
            let interval = setInterval(() => {
                this.progress -= 0.5;
                if (this.progress <= 0) {
                    this.show = false;
                    clearInterval(interval);
                }
            }, 25); 
        }
    }" 
    x-init="startTimer()"
    x-show="show" 
    x-transition:enter="animate-quantum"
    x-transition:leave="transition ease-in duration-300 opacity-0 scale-95 translate-y-5"
    class="fixed top-5 right-10 z-[9999] w-full max-w-[400px] select-none"
>
    {{-- Neural Core Container --}}
    <div class="relative group">
        
        {{-- Floating Corner Accents --}}
        <div class="absolute -top-2 -left-2 w-6 h-6 border-t-2 border-l-2 {{ session('success') ? 'border-emerald-400' : 'border-rose-400' }} animate-[corner-track_2s_infinite]"></div>
        <div class="absolute -bottom-2 -right-2 w-6 h-6 border-b-2 border-r-2 {{ session('success') ? 'border-emerald-400' : 'border-rose-400' }} animate-[corner-track_2s_infinite_reverse]"></div>

        {{-- Main Holographic Card --}}
        <div class="relative overflow-hidden bg-[#020403]/90 backdrop-blur-3xl border {{ session('success') ? 'border-emerald-500/40 shadow-[0_0_50px_-10px_rgba(16,185,129,0.3)]' : 'border-rose-500/40 shadow-[0_0_50px_-10px_rgba(244,63,94,0.3)]' }} rounded-sm">
            
            {{-- Background Noise & Data Layer --}}
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
            
            <div class="relative z-10 p-6">
                <div class="flex items-center gap-5">
                    
                    {{-- Rotating Energy Core Icon --}}
                    <div class="flex-shrink-0 relative">
                        <div class="absolute inset-0 scale-150 blur-xl opacity-30 animate-pulse {{ session('success') ? 'bg-emerald-400' : 'bg-rose-400' }}"></div>
                        
                        <div class="relative h-14 w-14 flex items-center justify-center">
                            {{-- Rotating Outer Ring --}}
                            <div class="absolute inset-0 border-2 border-dashed rounded-full animate-[spin_8s_linear_infinite] {{ session('success') ? 'border-emerald-500/30' : 'border-rose-500/30' }}"></div>
                            
                            {{-- Inner Core --}}
                            <div class="h-10 w-10 flex items-center justify-center rounded-lg rotate-45 border {{ session('success') ? 'border-emerald-400 bg-emerald-950/40' : 'border-rose-400 bg-rose-950/40' }}">
                                <div class="-rotate-45">
                                    @if(session('success'))
                                        <svg class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <svg class="h-6 w-6 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Data Stream Text --}}
                    <div class="flex-1 min-w-0 khmer-font">
                        <div class="flex items-center gap-2 mb-1.5">
                            <div class="h-1 w-1 rounded-full animate-ping {{ session('success') ? 'bg-emerald-400' : 'bg-rose-400' }}"></div>
                            <span class="text-[9px] font-black uppercase tracking-[0.4em] font-mono {{ session('success') ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ session('success') ? 'Protocol: Authenticated' : 'Protocol: Breach' }}
                            </span>
                        </div>
                        
                        <div class="space-y-0.5">
                            <h4 class="text-white font-bold tracking-wider text-lg leading-tight">
                                {{ session('success') ? __('ជោគជ័យ!') : __('បរាជ័យ!') }}
                            </h4>
                            <p class="text-xs text-slate-400 leading-relaxed opacity-80 font-medium">
                                {{ session('success') ?? session('error') }}
                            </p>
                        </div>
                    </div>

                    {{-- Mech Close button --}}
                    <button @click="show = false" class="group/btn relative self-start p-1 border border-white/5 hover:border-white/20 transition-all">
                        <svg class="h-4 w-4 text-slate-500 group-hover/btn:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <div class="absolute -top-0.5 -right-0.5 w-1 h-1 bg-white/20"></div>
                    </button>
                </div>
            </div>

            {{-- Bit-Segmented Progress Bar --}}
            <div class="h-1.5 w-full bg-white/5 flex">
                <div 
                    class="h-full relative bit-segment transition-all duration-300 ease-out {{ session('success') ? 'bg-emerald-400 shadow-[0_0_15px_rgba(52,211,153,0.8)]' : 'bg-rose-500 shadow-[0_0_15px_rgba(244,63,94,0.8)]' }}"
                    :style="`width: ${progress}%`"
                >
                    {{-- Pulse wave --}}
                    <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                </div>
            </div>

            {{-- Hidden Decorative Metadata --}}
            <div class="px-4 py-1 flex justify-between bg-white/[0.02] border-t border-white/5 font-mono text-[8px] text-slate-600 tracking-tighter">
                <span>ENCRYPTION_LEVEL: OMEGA</span>
                <span x-text="'NODE_INDEX: ' + Math.floor(progress)"></span>
            </div>

        </div>
    </div>
</div>
@endif

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
                                    <input type="text" name="full_name_km" id="full_name_km" value="{{ old('full_name_km', $user->userProfile->full_name_km ?? '') }}" required 
                                           class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-bold text-slate-700" 
                                           placeholder="បញ្ជាក់ឈ្មោះជាភាសាខ្មែរ">
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
                                    <input type="text" name="full_name_en" id="full_name_en" value="{{ old('full_name_en', $user->userProfile->full_name_en ?? '') }}" 
                                           class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-bold text-slate-700" 
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
                                            class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-bold text-slate-700 appearance-none cursor-pointer">
                                        <option value="" disabled selected>{{ __('ជ្រើសរើសភេទ') }}</option>
                                        <option value="male" @if(old('gender', $user->userProfile->gender ?? '') == 'male') selected @endif>{{ __('ប្រុស') }}</option>
                                        <option value="female" @if(old('gender', $user->userProfile->gender ?? '') == 'female') selected @endif>{{ __('ស្រី') }}</option>
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
                                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', isset($user->userProfile->date_of_birth) ? \Carbon\Carbon::parse($user->userProfile->date_of_birth)->format('Y-m-d') : '') }}" 
                                           class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-bold text-slate-700">
                                </div>
                            </div>

                            {{-- Phone Number --}}
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('លេខទូរស័ព្ទ') }}</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                        <i class="fas fa-phone-alt"></i>
                                    </span>
                                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->userProfile->phone_number ?? '') }}" 
                                           class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-bold text-slate-700" 
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
                                    <input type="text" name="address" id="address" value="{{ old('address', $user->userProfile->address ?? '') }}" 
                                           class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-bold text-slate-700" 
                                           placeholder="{{ __('រាជធានីភ្នំពេញ, កម្ពុជា') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row items-center gap-4 pt-10">
                            <button type="submit" 
                                    class="w-full sm:flex-[2] py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i>
                                {{ __('រក្សាទុកការកែប្រែ') }}
                            </button>
                            
                            <a href="{{ route('student.profile.show') }}" 
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
        // Trigger file input នៅពេលចុចលើរង្វង់រូបភាព
        document.getElementById('profile-picture-container').addEventListener('click', function() {
            document.getElementById('profile_picture').click();
        });

        // បង្ហាញរូបភាព Preview ភ្លាមៗ
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            let preview = document.getElementById('profile-picture-preview');
            let placeholder = document.getElementById('profile-picture-placeholder');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (preview) {
                        preview.src = e.target.result;
                    } else if (placeholder) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.id = 'profile-picture-preview';
                        img.className = 'object-cover w-full h-full';
                        placeholder.replaceWith(img);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-app-layout>