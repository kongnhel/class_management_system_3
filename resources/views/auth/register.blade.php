<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }} - Register</title>

<x-guest-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Battambang:wght@400;700&display=swap');
        body { font-family: 'Inter', 'Battambang', sans-serif; margin: 0; }
        .min-h-screen { padding: 0 !important; justify-content: stretch !important; align-items: stretch !important; max-width: 100% !important; }
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.85rem center;
            background-size: 1.15em 1.15em;
        }
    </style>

    <div class="min-h-screen flex">
        {{-- Left: Branding (Matches Login Page) --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center">
            <img src="{{ asset('assets/image/download (5).jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-emerald-900/70"></div>
            <div class="relative z-10 text-center px-12">
                <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" class="w-28 h-28 mx-auto mb-8 drop-shadow-2xl">
                <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">Class Management<br>System</h1>
                <p class="text-emerald-100 text-lg max-w-sm mx-auto leading-relaxed">{{ __('register_branding_subtitle') }}</p>
                <div class="mt-10 flex items-center justify-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-emerald-300 animate-pulse"></div>
                    <span class="text-emerald-200 text-sm font-medium">{{ __('register_please_fill_info') }}</span>
                </div>
            </div>
        </div>

        {{-- Right: Registration Form Panel --}}
        <div class="relative w-full lg:w-1/2 flex items-center justify-center px-4 sm:px-8 lg:px-12 py-10 bg-gray-50 overflow-y-auto">

            {{-- Top-Right Language Switcher (matches login page) --}}
            <div class="absolute top-4 right-4 sm:top-6 sm:right-6 flex gap-1 rounded-xl bg-white p-1 shadow-sm border border-gray-200 z-20">
                <a href="{{ route('locale.switch', ['locale' => 'km']) }}"
                   class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ app()->getLocale() === 'km' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    ខ្មែរ
                </a>
                <a href="{{ route('locale.switch', ['locale' => 'en']) }}"
                   class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    EN
                </a>
            </div>

            <div class="w-full max-w-xl">

                {{-- Mobile University Logo Header --}}
                <div class="lg:hidden text-center mb-6">
                    <div class="w-16 h-16 mx-auto mb-2.5 p-2 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center justify-center">
                        <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <h2 class="text-lg font-bold text-gray-800">Class Management System</h2>
                </div>

                {{-- Page Heading --}}
                <div class="mb-6">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('register_heading') }}</h2>
                    <p class="text-gray-500 mt-1.5 text-sm">{{ __('register_subtitle') }}</p>
                </div>

                {{-- Student Auto-Found Banner (Populated via AJAX lookup) --}}
                <div id="studentFoundBanner" class="hidden mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-start gap-3 transition-all duration-300">
                    <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-emerald-950">{{ __('register_swal_found_title') }}</div>
                        <div id="studentFoundDetails" class="text-emerald-700 mt-0.5"></div>
                    </div>
                    <button type="button" onclick="dismissStudentFoundBanner()" class="text-emerald-600 hover:text-emerald-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    {{-- SECTION 1: Academic Identity --}}
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                        <div class="flex items-center gap-2.5 pb-2 border-b border-gray-100">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                                <i class="fas fa-university"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600">{{ __('study_program') }} & {{ __('student_id_2') }}</span>
                        </div>

                        {{-- Student ID Code --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                                {{ __('student_id_2') }} <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-id-badge text-sm"></i>
                                </span>
                                <input type="text" name="student_id_code" id="student_id_code" value="{{ old('student_id_code') }}" required
                                       class="block w-full pl-10 pr-24 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white text-gray-900 placeholder-gray-400 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                                       placeholder="e.g. B-XVI-000123" />
                                <span id="idLookupSpinner" class="hidden absolute inset-y-0 right-3 flex items-center text-emerald-600 text-xs">
                                    <i class="fas fa-circle-notch fa-spin mr-1"></i> Checking
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                <i class="fas fa-info-circle text-[10px]"></i>
                                <span>{{ __('register_student_id_hint') }}</span>
                            </p>
                            <x-input-error :messages="$errors->get('student_id_code')" class="mt-1 text-xs text-rose-500" />
                        </div>

                        {{-- Department & Degree Level --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                                    {{ __('course') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="department_id" id="department_id" required
                                            class="block w-full pl-3.5 pr-8 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white text-gray-900 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                                        <option value="">{{ __('select_course') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name_km }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('department_id')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                                    {{ __('degree_level') }} <span class="text-rose-500">*</span>
                                </label>
                                <select name="degree_level" id="degree_level" required
                                        class="block w-full pl-3.5 pr-8 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white text-gray-900 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                                    <option value="">{{ __('select_degree_level') }}</option>
                                    <option value="បរិញ្ញាបត្រ" {{ old('degree_level') == 'បរិញ្ញាបត្រ' ? 'selected' : '' }}>បរិញ្ញាបត្រ</option>
                                    <option value="បរិញ្ញាបត្ររង" {{ old('degree_level') == 'បរិញ្ញាបត្ររង' ? 'selected' : '' }}>បរិញ្ញាបត្ររង</option>
                                    <option value="អនុបណ្ឌិត" {{ old('degree_level') == 'អនុបណ្ឌិត' ? 'selected' : '' }}>អនុបណ្ឌិត</option>
                                    <option value="វិញ្ញាបនបត្រ" {{ old('degree_level') == 'វិញ្ញាបនបត្រ' ? 'selected' : '' }}>វិញ្ញាបនបត្រ</option>
                                    <option value="ផ្សេងៗ" {{ old('degree_level') == 'ផ្សេងៗ' ? 'selected' : '' }}>ផ្សេងៗ</option>
                                </select>
                                <x-input-error :messages="$errors->get('degree_level')" class="mt-1 text-xs text-rose-500" />
                            </div>
                        </div>

                        {{-- Generation --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                                {{ __('generation') }} <span class="text-rose-500">*</span>
                            </label>
                            <select name="generation" id="generation" required
                                    class="block w-full pl-3.5 pr-8 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white text-gray-900 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                                <option value="">{{ __('select_a_generation') }}</option>
                                @foreach($generations as $generation)
                                    <option value="{{ $generation }}" {{ old('generation') == $generation ? 'selected' : '' }}>
                                        {{ __('generation_2') }} {{ $generation }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('generation')" class="mt-1 text-xs text-rose-500" />
                        </div>
                    </div>

                    {{-- SECTION 2: Account Details --}}
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                        <div class="flex items-center gap-2.5 pb-2 border-b border-gray-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600">{{ __('personal_information') }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Full Name --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                                    {{ __('display_name') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-user-circle text-sm"></i>
                                    </span>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="block w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white text-gray-900 placeholder-gray-400 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                                           placeholder="Full Name" />
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                                    {{ __('email') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-envelope text-sm"></i>
                                    </span>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                           class="block w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white text-gray-900 placeholder-gray-400 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                                           placeholder="name@nmu.edu.kh" />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: Password & Security --}}
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                        <div class="flex items-center gap-2.5 pb-2 border-b border-gray-100">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                                <i class="fas fa-lock"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600">{{ __('security_and_login') }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Password --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                                    {{ __('password') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-key text-xs"></i>
                                    </span>
                                    <input id="password" type="password" name="password" required
                                           class="block w-full pl-10 pr-10 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white text-gray-900 placeholder-gray-400 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                                           placeholder="••••••••" />
                                    <button type="button" onclick="togglePassword('password', 'eyeOpen1', 'eyeClosed1')"
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                                        <i id="eyeOpen1" class="fas fa-eye text-xs"></i>
                                        <i id="eyeClosed1" class="fas fa-eye-slash text-xs hidden"></i>
                                    </button>
                                </div>
                                {{-- Password Strength Meter --}}
                                <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div id="strength-bar" class="h-full w-0 transition-all duration-500 rounded-full"></div>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                                    {{ __('confirm_password') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-shield-alt text-xs"></i>
                                    </span>
                                    <input id="password_confirmation" type="password" name="password_confirmation" required
                                           class="block w-full pl-10 pr-10 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white text-gray-900 placeholder-gray-400 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none"
                                           placeholder="••••••••" />
                                    <button type="button" onclick="togglePassword('password_confirmation', 'eyeOpen2', 'eyeClosed2')"
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                                        <i id="eyeOpen2" class="fas fa-eye text-xs"></i>
                                        <i id="eyeClosed2" class="fas fa-eye-slash text-xs hidden"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-rose-500" />
                            </div>
                        </div>
                    </div>

                    @if (config('services.turnstile.site_key'))
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-action="register"></div>
                        <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-1.5 text-xs text-rose-500" />
                    @endif

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 active:scale-[0.99] transition-all duration-200 text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus text-xs"></i>
                        <span>{{ __('btn_register') }}</span>
                    </button>
                </form>

                {{-- Login Link Footer --}}
                <div class="text-center mt-6 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        {{ __('auth_already_have_account') }}
                        <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700 ml-1 inline-flex items-center gap-1 hover:underline">
                            <span>{{ __('log_in') }}</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- Toggle Password Visibility Script --}}
    <script>
        function togglePassword(inputId, openId, closedId) {
            const input = document.getElementById(inputId);
            const open = document.getElementById(openId);
            const closed = document.getElementById(closedId);
            if (input.type === 'password') {
                input.type = 'text';
                open.classList.add('hidden');
                closed.classList.remove('hidden');
            } else {
                input.type = 'password';
                open.classList.remove('hidden');
                closed.classList.add('hidden');
            }
        }

        // Password strength meter
        const pswInput = document.getElementById('password');
        const sBar = document.getElementById('strength-bar');
        if (pswInput && sBar) {
            pswInput.addEventListener('input', () => {
                const val = pswInput.value;
                let strength = 0;
                if (val.length >= 8) strength++;
                if (/[A-Z]/.test(val)) strength++;
                if (/[0-9]/.test(val)) strength++;
                if (/[!@#$%^&*]/.test(val)) strength++;
                const colors = ['bg-transparent', 'bg-rose-500', 'bg-amber-500', 'bg-yellow-400', 'bg-emerald-500'];
                sBar.className = `h-full transition-all duration-500 rounded-full ${colors[strength]}`;
                sBar.style.width = (strength * 25) + '%';
            });
        }

        function dismissStudentFoundBanner() {
            const banner = document.getElementById('studentFoundBanner');
            if (banner) banner.classList.add('hidden');
        }
    </script>

    {{-- Student ID Automatic Lookup Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const studentIdInput = document.getElementById('student_id_code');
            const spinner = document.getElementById('idLookupSpinner');
            const banner = document.getElementById('studentFoundBanner');
            const bannerDetails = document.getElementById('studentFoundDetails');

            if (studentIdInput) {
                studentIdInput.addEventListener('blur', function() {
                    const code = this.value.trim();
                    if (code.length >= 3) {
                        if (spinner) spinner.classList.remove('hidden');

                        fetch(`/api/check-student/${encodeURIComponent(code)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (spinner) spinner.classList.add('hidden');

                                if (data.success) {
                                    Swal.fire({
                                        title: '{{ __('register_swal_found_title') }}',
                                        html: `{{ __('register_swal_found_text') }} <b>${data.name}</b> {{ __('register_swal_found_generation') }} <b>${data.generation}</b> {{ __('register_swal_found_confirm') }}`,
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonColor: '#059669',
                                        cancelButtonColor: '#e11d48',
                                        confirmButtonText: '{{ __('register_swal_confirm_yes') }}',
                                        cancelButtonText: '{{ __('register_swal_confirm_no') }}',
                                        customClass: {
                                            popup: 'rounded-2xl',
                                            confirmButton: 'rounded-xl font-bold px-5 py-2.5',
                                            cancelButton: 'rounded-xl font-bold px-5 py-2.5'
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            const nameField = document.getElementById('name');
                                            const deptField = document.getElementById('department_id');
                                            const genField = document.getElementById('generation');

                                            if (nameField && data.name) nameField.value = data.name;
                                            if (deptField && data.department_id) deptField.value = data.department_id;
                                            if (genField && data.generation) genField.value = data.generation;

                                            if (banner && bannerDetails) {
                                                bannerDetails.innerHTML = `Verified as <strong>${data.name}</strong> · Generation ${data.generation}`;
                                                banner.classList.remove('hidden');
                                            }

                                            Swal.fire({
                                                title: '{{ __('register_swal_thanks_title') }}',
                                                text: '{{ __('register_swal_thanks_text') }}',
                                                icon: 'success',
                                                timer: 2000,
                                                showConfirmButton: false,
                                                customClass: { popup: 'rounded-2xl' }
                                            });
                                        } else {
                                            studentIdInput.value = '';
                                            if (banner) banner.classList.add('hidden');
                                        }
                                    });
                                } else {
                                    if (banner) banner.classList.add('hidden');
                                    Swal.fire({
                                        title: '{{ __('register_swal_not_found_title') }}',
                                        text: '{{ __('register_swal_not_found_text') }}',
                                        icon: 'error',
                                        confirmButtonColor: '#059669',
                                        customClass: {
                                            popup: 'rounded-2xl',
                                            confirmButton: 'rounded-xl font-bold px-5 py-2.5'
                                        }
                                    });
                                    studentIdInput.value = '';
                                }
                            })
                            .catch(error => {
                                if (spinner) spinner.classList.add('hidden');
                                Swal.fire({
                                    title: 'Error',
                                    text: '{{ __('register_swal_error_text') }}',
                                    icon: 'error',
                                    confirmButtonColor: '#059669',
                                    customClass: {
                                        popup: 'rounded-2xl',
                                        confirmButton: 'rounded-xl font-bold px-5 py-2.5'
                                    }
                                });
                            });
                    }
                });
            }
        });
    </script>
</x-guest-layout>
