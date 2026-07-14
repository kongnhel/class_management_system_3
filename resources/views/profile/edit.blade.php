<x-app-layout>
    @php
        $user = Auth::user()->loadMissing('userProfile');
        $profileUrl = $user->userProfile?->profile_picture_url;
    @endphp

    <div class="min-h-screen bg-[#f8fafc] py-12 font-sans antialiased">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="mb-10 text-center md:text-left">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ __('profile_title') }}
                </h1>
                <p class="mt-2 text-slate-500">
                    {{ __('profile_subtitle') }}
                </p>
            </div>

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     class="mb-6 flex items-center p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-sm animate-fade-in-down">
                    <svg class="h-5 w-5 text-emerald-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-emerald-800 font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Left Side: Profile Summary Card --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                        <div class="h-32 bg-gradient-to-r from-slate-800 to-slate-900"></div>
                        <div class="px-6 pb-8">
                            <div class="relative flex justify-center -mt-16 mb-6">
                                <form method="post" action="{{ route('profile.update-picture') }}" enctype="multipart/form-data" id="picture-form">
                                    @csrf
                                    <div class="relative group cursor-pointer" id="profile-picture-container">
                                        <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-slate-100">
                                            @if ($profileUrl)
                                                <img src="{{ $profileUrl }}" id="profile-picture-preview" class="w-full h-full object-cover">
                                            @else
                                                <div id="profile-picture-placeholder" class="w-full h-full flex items-center justify-center text-3xl font-bold text-slate-400">
                                                    {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <input id="profile_picture" name="profile_picture" type="file" class="hidden" accept="image/*" onchange="this.form.submit()" />
                                    </div>
                                </form>
                            </div>
                            <div class="text-center">
                                <h3 class="text-xl font-bold text-slate-900">{{ $user->name }}</h3>
                                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                                <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                                    {{ Auth::user()->role ?? 'User Account' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Settings Forms --}}
                <div class="lg:col-span-8 space-y-8">
                    
                    {{-- 1. Profile Information --}}
                    <section class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="p-3 bg-emerald-50 rounded-2xl">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">{{ __('profile_basic_info') }}</h2>
                                <p class="text-sm text-slate-500">{{ __('profile_basic_desc') }}</p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf @method('patch')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="name" :value="__('profile_full_name')" class="text-slate-700 font-bold ml-1" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1.5 block w-full rounded-2xl border-slate-200 bg-slate-50/50 focus:bg-white transition-all shadow-sm" :value="old('name', $user->name)" placeholder="ឈ្មោះពេញ" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="email" :value="__('profile_email')" class="text-slate-700 font-bold ml-1" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1.5 block w-full rounded-2xl border-slate-200 bg-slate-50/50 focus:bg-white transition-all shadow-sm" :value="old('email', $user->email)" placeholder="example@gmail.com" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 ml-1 mb-1.5">លេខសម្គាល់</label>
                                    <input type="text" value="{{ $user->student_id_code }}" readonly disabled
                                           class="mt-1.5 block w-full rounded-2xl border-slate-200 bg-slate-100 text-slate-500 px-4 py-2.5 cursor-not-allowed" />
                                </div>
                            </div>
                            <div class="flex justify-end pt-4">
                                <x-primary-button class="bg-slate-900 hover:bg-slate-800 text-white rounded-xl px-8 py-3 font-bold transition-all shadow-lg shadow-slate-200">
                                    {{ __('profile_save') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </section>

                    {{-- 2. Password Security --}}
                    <section class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="p-3 bg-amber-50 rounded-2xl">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">{{ __('profile_security') }}</h2>
                                <p class="text-sm text-slate-500">{{ __('profile_security_desc') }}</p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf @method('put')
                            <div class="space-y-5">
                                <div>
                                    <x-input-label for="current_password" :value="__('profile_current_password')" class="text-slate-700 font-bold ml-1" />
                                    <x-text-input id="current_password" name="current_password" type="password" class="mt-1.5 block w-full rounded-2xl border-slate-200 bg-slate-50/50 shadow-sm" placeholder="ពាក្យសម្ងាត់បច្ចុប្បន្ន" />
                                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="password" :value="__('profile_new_password')" class="text-slate-700 font-bold ml-1" />
                                        <x-text-input id="password" name="password" type="password" class="mt-1.5 block w-full rounded-2xl border-slate-200 bg-slate-50/50 shadow-sm" placeholder="ពាក្យសម្ងាត់ថ្មី" />
                                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="password_confirmation" :value="__('profile_confirm_password')" class="text-slate-700 font-bold ml-1" />
                                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1.5 block w-full rounded-2xl border-slate-200 bg-slate-50/50 shadow-sm" placeholder="បញ្ជាក់ពាក្យសម្ងាត់ថ្មី" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end pt-4">
                                <x-primary-button class="bg-amber-600 hover:bg-amber-700 text-white rounded-xl px-8 py-3 font-bold transition-all shadow-lg shadow-amber-200">
                                    {{ __('profile_update_password') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </section>

                    {{-- 3. Danger Zone --}}
                    <section class="bg-red-50/50 rounded-[2rem] border border-red-100 p-8">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-red-700">{{ __('profile_delete_account') }}</h2>
                                <p class="text-sm text-red-600/70">{{ __('profile_delete_warning') }}</p>
                            </div>
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" 
                                    class="inline-flex justify-center items-center px-6 py-3 bg-white border border-red-200 text-red-600 rounded-xl font-bold hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                {{ __('profile_delete_btn') }}
                            </button>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>

    {{-- File Preview Script --}}
    <script>
        document.getElementById('profile-picture-container').onclick = () => document.getElementById('profile_picture').click();
    </script>
</x-app-layout>
