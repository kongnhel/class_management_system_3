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

        /* Right panel form fields */
        .reg-input {
            display: block;
            width: 100%;
            height: 2.75rem;
            border-radius: 0.625rem;
            border: 1px solid #d1d5db;
            background-color: #fff;
            color: #111827;
            font-size: 0.875rem;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .reg-input::placeholder { color: #9ca3af; }
        .reg-input:hover { border-color: #9ca3af; }
        .reg-input:focus { border-color: #059669; box-shadow: 0 0 0 4px rgba(5, 150, 105, .12); }
        .reg-input.is-ok { border-color: #10b981; }
        .reg-input.is-bad { border-color: #f43f5e; }
        .reg-label { display: block; font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 0.375rem; }
        .reg-group-title { display: flex; align-items: center; gap: .625rem; font-size: .9375rem; font-weight: 700; color: #111827; }
        .reg-group-title i { width: 1.75rem; height: 1.75rem; display: inline-flex; align-items: center; justify-content: center; border-radius: .5rem; background: #ecfdf5; color: #047857; font-size: .75rem; }
        .strength-seg { height: 4px; flex: 1; border-radius: 9999px; background: #e5e7eb; transition: background-color .25s; }
        @media (prefers-reduced-motion: reduce) { .reg-input, .strength-seg { transition: none; } }
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

        {{-- Right: Registration Form Panel (redesigned) --}}
        <div class="relative w-full lg:w-1/2 flex items-start lg:items-center justify-center px-5 sm:px-10 lg:px-14 py-12 bg-white overflow-y-auto">

            {{-- Top-Right Language Switcher --}}
            <div class="absolute top-4 right-4 sm:top-6 sm:right-6 flex gap-1 rounded-xl bg-gray-100 p-1 z-20">
                <a href="{{ route('locale.switch', ['locale' => 'km']) }}"
                   class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ app()->getLocale() === 'km' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-white' }}">
                    ខ្មែរ
                </a>
                <a href="{{ route('locale.switch', ['locale' => 'en']) }}"
                   class="px-3 py-1 rounded-lg text-xs font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-white' }}">
                    EN
                </a>
            </div>

            <div class="w-full max-w-lg mt-8 lg:mt-0">

                {{-- Mobile University Logo Header --}}
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" class="w-11 h-11 object-contain">
                    <span class="text-base font-bold text-gray-800">Class Management System</span>
                </div>

                {{-- Page Heading --}}
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('register_heading') }}</h2>
                    <p class="text-gray-500 mt-2 text-sm leading-relaxed">{{ __('register_subtitle') }}</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-8">
                    @csrf

                    {{-- GROUP 1: Study details --}}
                    <fieldset class="space-y-5">
                        <legend class="reg-group-title mb-5">
                            <i class="fas fa-university"></i>
                            <span>{{ __('study_program') }} & {{ __('student_id_2') }}</span>
                        </legend>

                        {{-- Student ID Code --}}
                        <div>
                            <label for="student_id_code" class="reg-label">
                                {{ __('student_id_2') }} <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-id-badge text-sm"></i>
                                </span>
                                <input type="text" name="student_id_code" id="student_id_code" value="{{ old('student_id_code') }}" required
                                       autocomplete="off"
                                       class="reg-input pl-10 pr-28"
                                       placeholder="e.g. B-XVI-000123" />
                                <span id="idLookupSpinner" class="absolute inset-y-0 right-3 items-center text-emerald-600 text-xs font-medium" style="display:none;">
                                    <i class="fas fa-circle-notch fa-spin mr-1.5"></i> Checking
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1.5">{{ __('register_student_id_hint') }}</p>
                            <x-input-error :messages="$errors->get('student_id_code')" class="mt-1 text-xs text-rose-500" />

                            {{-- Student Auto-Found Banner (Populated via AJAX lookup) --}}
                            <div id="studentFoundBanner" class="hidden mt-3 pl-3.5 pr-2 py-2.5 border-l-4 border-emerald-500 bg-emerald-50 rounded-r-lg text-sm flex items-start gap-3">
                                <i class="fas fa-circle-check text-emerald-600 mt-0.5"></i>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-emerald-900">{{ __('register_swal_found_title') }}</div>
                                    <div id="studentFoundDetails" class="text-emerald-800 text-xs mt-0.5 break-words"></div>
                                </div>
                                <button type="button" onclick="dismissStudentFoundBanner()" aria-label="Dismiss"
                                        class="w-7 h-7 flex items-center justify-center rounded-md text-emerald-700 hover:bg-emerald-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Department --}}
                        <div>
                            <label for="department_id" class="reg-label">
                                {{ __('course') }} <span class="text-rose-500">*</span>
                            </label>
                            <select name="department_id" id="department_id" required class="reg-input pl-3.5 pr-9">
                                <option value="">{{ __('select_course') }}</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name_km }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('department_id')" class="mt-1 text-xs text-rose-500" />
                        </div>

                        {{-- Degree Level & Generation --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="degree_level" class="reg-label">
                                    {{ __('degree_level') }} <span class="text-rose-500">*</span>
                                </label>
                                <select name="degree_level" id="degree_level" required class="reg-input pl-3.5 pr-9">
                                    <option value="">{{ __('select_degree_level') }}</option>
                                    <option value="បរិញ្ញាបត្រ" {{ old('degree_level') == 'បរិញ្ញាបត្រ' ? 'selected' : '' }}>បរិញ្ញាបត្រ</option>
                                    <option value="បរិញ្ញាបត្ររង" {{ old('degree_level') == 'បរិញ្ញាបត្ររង' ? 'selected' : '' }}>បរិញ្ញាបត្ររង</option>
                                    <option value="អនុបណ្ឌិត" {{ old('degree_level') == 'អនុបណ្ឌិត' ? 'selected' : '' }}>អនុបណ្ឌិត</option>
                                    <option value="វិញ្ញាបនបត្រ" {{ old('degree_level') == 'វិញ្ញាបនបត្រ' ? 'selected' : '' }}>វិញ្ញាបនបត្រ</option>
                                    <option value="ផ្សេងៗ" {{ old('degree_level') == 'ផ្សេងៗ' ? 'selected' : '' }}>ផ្សេងៗ</option>
                                </select>
                                <x-input-error :messages="$errors->get('degree_level')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            <div>
                                <label for="generation" class="reg-label">
                                    {{ __('generation') }} <span class="text-rose-500">*</span>
                                </label>
                                <select name="generation" id="generation" required class="reg-input pl-3.5 pr-9">
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
                    </fieldset>

                    <hr class="border-gray-200">

                    {{-- GROUP 2: Personal information --}}
                    <fieldset class="space-y-5">
                        <legend class="reg-group-title mb-5">
                            <i class="fas fa-user"></i>
                            <span>{{ __('personal_information') }}</span>
                        </legend>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Full Name --}}
                            <div>
                                <label for="name" class="reg-label">
                                    {{ __('display_name') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-user-circle text-sm"></i>
                                    </span>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="reg-input pl-10 pr-3.5"
                                           placeholder="Full Name" />
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="reg-label">
                                    {{ __('email') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-envelope text-sm"></i>
                                    </span>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                           class="reg-input pl-10 pr-3.5"
                                           placeholder="name@nmu.edu.kh" />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
                            </div>
                        </div>
                    </fieldset>

                    <hr class="border-gray-200">

                    {{-- GROUP 3: Password --}}
                    <fieldset class="space-y-5">
                        <legend class="reg-group-title mb-5">
                            <i class="fas fa-lock"></i>
                            <span>{{ __('security_and_login') }}</span>
                        </legend>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Password --}}
                            <div>
                                <label for="password" class="reg-label">
                                    {{ __('password') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-key text-xs"></i>
                                    </span>
                                    <input id="password" type="password" name="password" required autocomplete="new-password"
                                           class="reg-input pl-10 pr-10"
                                           placeholder="••••••••" />
                                    <button type="button" onclick="togglePassword('password', 'eyeOpen1', 'eyeClosed1')" aria-label="Show or hide password"
                                            class="absolute inset-y-0 right-0 w-10 flex items-center justify-center text-gray-400 hover:text-gray-600 focus-visible:outline-none focus-visible:text-emerald-600">
                                        <i id="eyeOpen1" class="fas fa-eye text-xs"></i>
                                        <i id="eyeClosed1" class="fas fa-eye-slash text-xs hidden"></i>
                                    </button>
                                </div>
                                {{-- Password Strength Meter (4 segments) --}}
                                <div id="strength-bar" class="mt-2.5 flex gap-1.5" aria-hidden="true">
                                    <span class="strength-seg"></span>
                                    <span class="strength-seg"></span>
                                    <span class="strength-seg"></span>
                                    <span class="strength-seg"></span>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label for="password_confirmation" class="reg-label">
                                    {{ __('confirm_password') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-shield-alt text-xs"></i>
                                    </span>
                                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                           class="reg-input pl-10 pr-10"
                                           placeholder="••••••••" />
                                    <button type="button" onclick="togglePassword('password_confirmation', 'eyeOpen2', 'eyeClosed2')" aria-label="Show or hide password"
                                            class="absolute inset-y-0 right-0 w-10 flex items-center justify-center text-gray-400 hover:text-gray-600 focus-visible:outline-none focus-visible:text-emerald-600">
                                        <i id="eyeOpen2" class="fas fa-eye text-xs"></i>
                                        <i id="eyeClosed2" class="fas fa-eye-slash text-xs hidden"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-rose-500" />
                            </div>
                        </div>
                    </fieldset>

                    @if (config('services.turnstile.site_key'))
                        <div>
                            <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-action="register"></div>
                            <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-1.5 text-xs text-rose-500" />
                        </div>
                    @endif

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="w-full h-12 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold rounded-xl transition-colors duration-150 text-sm flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-emerald-600/30">
                        <i class="fas fa-user-plus text-xs"></i>
                        <span>{{ __('btn_register') }}</span>
                    </button>
                </form>

                {{-- Login Link Footer --}}
                <p class="text-sm text-gray-500 text-center mt-8">
                    {{ __('auth_already_have_account') }}
                    <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:text-emerald-800 ml-1 hover:underline underline-offset-4">
                        {{ __('log_in') }}
                    </a>
                </p>

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

        // Password strength meter (4 segments)
        const pswInput = document.getElementById('password');
        const pswConfirm = document.getElementById('password_confirmation');
        const sBar = document.getElementById('strength-bar');
        if (pswInput && sBar) {
            const segs = sBar.querySelectorAll('.strength-seg');
            const colors = ['', '#f43f5e', '#f59e0b', '#facc15', '#10b981'];
            pswInput.addEventListener('input', () => {
                const val = pswInput.value;
                let strength = 0;
                if (val.length >= 8) strength++;
                if (/[A-Z]/.test(val)) strength++;
                if (/[0-9]/.test(val)) strength++;
                if (/[!@#$%^&*]/.test(val)) strength++;
                segs.forEach((seg, i) => {
                    seg.style.backgroundColor = i < strength ? colors[strength] : '';
                });
                checkConfirmMatch();
            });
        }

        // Confirm password match feedback
        function checkConfirmMatch() {
            if (!pswConfirm || !pswInput) return;
            pswConfirm.classList.remove('is-ok', 'is-bad');
            if (pswConfirm.value.length === 0) return;
            pswConfirm.classList.add(pswConfirm.value === pswInput.value ? 'is-ok' : 'is-bad');
        }
        if (pswConfirm) pswConfirm.addEventListener('input', checkConfirmMatch);

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

            const showSpinner = (on) => { if (spinner) spinner.style.display = on ? 'flex' : 'none'; };

            if (studentIdInput) {
                studentIdInput.addEventListener('blur', function() {
                    const code = this.value.trim();
                    if (code.length >= 3) {
                        showSpinner(true);

                        fetch(`/api/check-student/${encodeURIComponent(code)}`)
                            .then(res => res.json())
                            .then(data => {
                                showSpinner(false);

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
                                showSpinner(false);
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