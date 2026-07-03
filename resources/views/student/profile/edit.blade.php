<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-4xl text-gray-900 leading-tight tracking-wide">
            {{ __('កែប្រែប្រវត្តិរូបនិស្សិត') }}
        </h2>
    </x-slot>

    @php
        // ទាញយក URL រូបភាពពី ImgBB ដោយផ្ទាល់ចេញពី userProfile
        $profileUrl = $user->userProfile?->profile_picture_url;
    @endphp

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-10">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 sm:p-12 border border-gray-100">
                <h3 class="text-4xl font-extrabold text-emerald-700 mb-8 text-center">{{ __('កែប្រែព័ត៌មាន Profile របស់អ្នក') }}</h3>

 {{-- Modern Floating Toast --}}
@if (session('success') || session('error'))
<div 
    x-data="{ 
        show: false, 
        progress: 100,
        startTimer() {
            this.show = true;
            let interval = setInterval(() => {
                this.progress -= 1;
                if (this.progress <= 0) {
                    this.show = false;
                    clearInterval(interval);
                }
            }, 50); // 5 seconds total (50ms * 100)
        }
    }" 
    x-init="startTimer()"
    x-show="show" 
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="translate-y-12 opacity-0 sm:translate-y-0 sm:translate-x-12"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed top-6 right-6 z-[9999] w-full max-w-sm"
>
    <div class="relative overflow-hidden bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-2xl p-4 ring-1 ring-black/5">
        <div class="flex items-start gap-4">
            
            {{-- Modern Icon Logic --}}
            <div class="flex-shrink-0">
                @if(session('success'))
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-500/10 text-green-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                @else
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-500/10 text-red-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Text Content --}}
            <div class="flex-1 pt-0.5">
                <p class="text-sm font-bold text-gray-900 leading-tight">
                    {{ session('success') ? __('ជោគជ័យ!') : __('បរាជ័យ!') }}
                </p>
                <p class="mt-1 text-sm text-gray-600 leading-relaxed">
                    {{ session('success') ?? session('error') }}
                </p>
            </div>

            {{-- Manual Close --}}
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Progress Bar (The "Modern" Touch) --}}
        <div class="absolute bottom-0 left-0 h-1 bg-gray-100 w-full">
            <div 
                class="h-full transition-all duration-75 ease-linear {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}"
                :style="`width: ${progress}%`"
            ></div>
        </div>
    </div>
</div>
@endif

                <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="space-y-8">
                        <div class="bg-gray-50 p-6 rounded-3xl border border-gray-200 shadow-inner">
                            <h4 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4 border-gray-200">{{ __('ព័ត៌មាន Profile របស់ខ្ញុំ') }}</h4>

                            {{-- ផ្នែកបង្ហោះរូបភាព Profile --}}
                            <div class="mb-8 flex items-center flex-col sm:flex-row">
                                <div class="relative mb-6 sm:mb-0 sm:mr-8 group">
                                    {{-- បង្ហាញរូបភាពពី URL ImgBB ដោយផ្ទាល់ --}}
                                    @if($profileUrl)
                                        <img id="profile-picture-preview" src="{{ $profileUrl }}?tr=q-auto,f-auto" alt="{{ $user->name }}" class="w-32 h-32 rounded-full object-cover border-4 border-emerald-400 shadow-xl transition-transform duration-300 transform group-hover:scale-105">
                                    @else
                                        <div id="profile-picture-placeholder" class="w-32 h-32 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-5xl font-bold border-4 border-emerald-400 shadow-xl">
                                            {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 text-center sm:text-left">
                                    <x-input-label for="profile_picture" class="flex items-center justify-center sm:justify-start text-lg text-gray-700 font-semibold mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg> {{ __('រូបភាព Profile') }}
                                    </x-input-label>
                                    <input type="file" id="profile_picture" name="profile_picture" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-3 file:px-6
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-emerald-100 file:text-emerald-700
                                        hover:file:bg-emerald-200 transition-colors duration-200"/>
                                    <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                                    
                                    @if($profileUrl)
                                        <div class="mt-4">
                                            <label for="remove_profile_picture" class="inline-flex items-center text-sm text-red-600 cursor-pointer">
                                                <input type="checkbox" name="remove_profile_picture" id="remove_profile_picture" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                                <span class="ml-2 font-medium">{{ __('លុបរូបភាព Profile បច្ចុប្បន្ន') }}</span>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                {{-- ឈ្មោះពេញ (ខ្មែរ) --}}
                                <div>
                                    <x-input-label for="full_name_km" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 2l10 5-10 5-10-5 10-5z"></path>
                                            <path d="M2 17l10 5 10-5"></path>
                                            <path d="M2 12l10 5 10-5"></path>
                                        </svg> {{ __('ឈ្មោះពេញ (ខ្មែរ)') }}
                                    </x-input-label>
                                    <x-text-input id="full_name_km" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 py-3 px-4 transition duration-150 ease-in-out" type="text" name="full_name_km" :value="old('full_name_km', $userProfile->full_name_km ?? '')" />
                                    <x-input-error :messages="$errors->get('full_name_km')" class="mt-2" />
                                </div>
                                {{-- ឈ្មោះពេញ (អង់គ្លេស) --}}
                                <div>
                                    <x-input-label for="full_name_en" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 2l10 5-10 5-10-5 10-5z"></path>
                                            <path d="M2 17l10 5 10-5"></path>
                                            <path d="M2 12l10 5 10-5"></path>
                                        </svg> {{ __('ឈ្មោះពេញ (អង់គ្លេស)') }}
                                    </x-input-label>
                                    <x-text-input id="full_name_en" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 py-3 px-4 transition duration-150 ease-in-out" type="text" name="full_name_en" :value="old('full_name_en', $userProfile->full_name_en ?? '')" />
                                    <x-input-error :messages="$errors->get('full_name_en')" class="mt-2" />
                                </div>
                                {{-- ភេទ --}}
                                <div>
                                    <x-input-label for="gender" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12" y2="8"></line>
                                        </svg> {{ __('ភេទ') }}
                                    </x-input-label>
                                    <select id="gender" name="gender" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 py-3 px-4 transition duration-150 ease-in-out">
                                        <option value="">{{ __('ជ្រើសរើសភេទ') }}</option>
                                        <option value="male" {{ old('gender', $userProfile->gender ?? '') == 'male' ? 'selected' : '' }}>{{ __('ប្រុស') }}</option>
                                        <option value="female" {{ old('gender', $userProfile->gender ?? '') == 'female' ? 'selected' : '' }}>{{ __('ស្រី') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                </div>
                                {{-- ថ្ងៃខែឆ្នាំកំណើត --}}
                                <div>
                                    <x-input-label for="date_of_birth" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg> {{ __('ថ្ងៃខែឆ្នាំកំណើត') }}
                                    </x-input-label>
                                    <x-text-input id="date_of_birth" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 py-3 px-4 transition duration-150 ease-in-out" type="date" name="date_of_birth" :value="old('date_of_birth', optional($userProfile->date_of_birth)->format('Y-m-d') ?? '')" />
                                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                                </div>
                                {{-- លេខទូរស័ព្ទ --}}
                                <div>
                                    <x-input-label for="phone_number" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 16.92v3a2 2 0 0 1-2 2h-1c-1.85 0-3.66-.45-5.3-1.28a19.49 19.49 0 0 1-8.58-8.58C4.45 6.66 4 4.85 4 3V2c0-1.1.9-2 2-2h3a2 2 0 0 1 2 2.18c-.35 1.05-.55 2.16-.58 3.3a2 2 0 0 1 2.22 2.22c1.13-.03 2.25-.23 3.3-.58a2 2 0 0 1 2.18 2.18z"></path>
                                        </svg> {{ __('លេខទូរស័ព្ទ') }}
                                    </x-input-label>
                                    <x-text-input id="phone_number" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 py-3 px-4 transition duration-150 ease-in-out" type="text" name="phone_number" :value="old('phone_number', $userProfile->phone_number ?? '')" />
                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                                </div>
                                {{-- អាសយដ្ឋាន --}}
                                <div>
                                    <x-input-label for="address" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg> {{ __('អាសយដ្ឋាន') }}
                                    </x-input-label>
                                    <x-text-input id="address" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 py-3 px-4 transition duration-150 ease-in-out" type="text" name="address" :value="old('address', $userProfile->address ?? '')" />
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-12">
                        <x-primary-button class="inline-flex items-center px-8 py-4 bg-emerald-600 border border-transparent rounded-full font-bold text-lg text-white hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-4 focus:ring-emerald-300 transition ease-in-out duration-150 shadow-lg transform hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            <span>{{ __('រក្សាទុកការកែប្រែ') }}</span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // JavaScript សម្រាប់បង្ហាញរូបភាពបណ្តោះអាសន្ន (Preview)
    document.getElementById('profile_picture').addEventListener('change', function(event) {
        const [file] = event.target.files;
        const previewElement = document.getElementById('profile-picture-preview');
        const placeholderElement = document.getElementById('profile-picture-placeholder');
        const parentContainer = document.querySelector('.relative.group');

        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (previewElement) {
                    previewElement.src = e.target.result;
                } else if (placeholderElement) {
                    const img = document.createElement('img');
                    img.id = 'profile-picture-preview';
                    img.src = e.target.result;
                    img.alt = 'Profile Picture';
                    img.className = 'w-32 h-32 rounded-full object-cover border-4 border-emerald-400 shadow-xl transition-transform duration-300 transform group-hover:scale-105';
                    placeholderElement.replaceWith(img);
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>