<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }} - Register</title>

@php
    // Fallback so the raw key never shows if the translation is missing
    $securityTitle = \Illuminate\Support\Facades\Lang::has('security_and_login')
        ? __('security_and_login')
        : (app()->getLocale() === 'km' ? 'សុវត្ថិភាព និងការចូលប្រើ' : 'Security & login');
@endphp

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

        /* ===== Right panel (self-contained CSS, no dependency on compiled Tailwind) ===== */
        .reg-panel { position: relative; width: 100%; display: flex; justify-content: center; background: #fff; overflow-y: auto; padding: 3rem 1.25rem; box-sizing: border-box; }
        @media (min-width: 640px)  { .reg-panel { padding-left: 2.5rem; padding-right: 2.5rem; } }
        @media (min-width: 1024px) { .reg-panel { width: 50%; align-items: center; padding-left: 3.5rem; padding-right: 3.5rem; } }
        .reg-wrap { width: 100%; max-width: 32rem; margin-top: 2rem; }
        @media (min-width: 1024px) { .reg-wrap { margin-top: 0; } }

        .reg-lang { position: absolute; top: 1rem; right: 1rem; display: flex; gap: .25rem; padding: .25rem; border-radius: .75rem; background: #f3f4f6; z-index: 20; }
        @media (min-width: 640px) { .reg-lang { top: 1.5rem; right: 1.5rem; } }
        .reg-lang a { padding: .25rem .75rem; border-radius: .5rem; font-size: .75rem; font-weight: 700; color: #6b7280; text-decoration: none; transition: background-color .15s, color .15s; }
        .reg-lang a:hover { background: #fff; color: #111827; }
        .reg-lang a.active { background: #059669; color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.12); }

        .reg-mobile-brand { display: flex; align-items: center; gap: .75rem; margin-bottom: 2rem; }
        .reg-mobile-brand img { width: 2.75rem; height: 2.75rem; object-fit: contain; }
        .reg-mobile-brand span { font-size: 1rem; font-weight: 700; color: #1f2937; }
        @media (min-width: 1024px) { .reg-mobile-brand { display: none; } }

        .reg-title { margin: 0; font-size: 1.875rem; font-weight: 800; color: #111827; letter-spacing: -.01em; }
        .reg-subtitle { margin: .5rem 0 2rem; font-size: .875rem; line-height: 1.6; color: #6b7280; }

        .reg-form { display: grid; gap: 2rem; }
        .reg-fieldset { display: grid; gap: 1.25rem; border: 0; padding: 0; margin: 0; min-width: 0; }
        .reg-group-title { display: flex; align-items: center; gap: .625rem; padding: 0; margin: 0 0 1.25rem; font-size: .9375rem; font-weight: 700; color: #111827; }
        .reg-group-title i { width: 1.75rem; height: 1.75rem; display: inline-flex; align-items: center; justify-content: center; border-radius: .5rem; background: #ecfdf5; color: #047857; font-size: .75rem; }
        .reg-divider { border: 0; border-top: 1px solid #e5e7eb; margin: 0; }
        .reg-grid-2 { display: grid; grid-template-columns: 1fr; gap: 1.25rem; }
        @media (min-width: 640px) { .reg-grid-2 { grid-template-columns: 1fr 1fr; } }
        .reg-field { min-width: 0; position: relative; }

        .reg-label { display: block; font-size: .8125rem; font-weight: 600; color: #374151; margin-bottom: .375rem; }
        .reg-req { color: #f43f5e; }
        .reg-hint { margin: .375rem 0 0; font-size: .75rem; color: #6b7280; }
        .reg-box { position: relative; }

        .reg-input { display: block; width: 100%; height: 2.75rem; box-sizing: border-box; padding: 0 .875rem; border-radius: .625rem; border: 1px solid #d1d5db; background-color: #fff; color: #111827; font-size: .875rem; outline: none; transition: border-color .15s, box-shadow .15s; }
        .reg-input::placeholder { color: #9ca3af; }
        .reg-input:hover { border-color: #9ca3af; }
        .reg-input:focus { border-color: #059669; box-shadow: 0 0 0 4px rgba(5,150,105,.12); }
        .reg-input.is-ok  { border-color: #10b981; }
        .reg-input.is-bad { border-color: #f43f5e; }
        .reg-input.has-icon { padding-left: 2.5rem; }
        .reg-input.has-eye { padding-right: 2.5rem; }
        .reg-input.has-spinner { padding-right: 6.5rem; }
        select.reg-input { padding-right: 2.25rem; }

        .reg-icon { position: absolute; top: 0; bottom: 0; left: 0; width: 2.5rem; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: .8125rem; pointer-events: none; }
        .reg-eye { position: absolute; top: 0; bottom: 0; right: 0; width: 2.5rem; display: flex; align-items: center; justify-content: center; background: none; border: 0; padding: 0; color: #9ca3af; font-size: .8125rem; cursor: pointer; }
        .reg-eye:hover { color: #4b5563; }
        .reg-eye:focus-visible { outline: none; color: #059669; }
        /* Font Awesome sets display on .fas, which beat Tailwind's .hidden: force it */
        .reg-panel .hidden { display: none !important; }

        .reg-spinner { position: absolute; top: 0; bottom: 0; right: .75rem; align-items: center; gap: .375rem; font-size: .75rem; font-weight: 500; color: #059669; }

        .reg-strength { display: flex; gap: .375rem; margin-top: .625rem; }
        .reg-strength span { height: 4px; flex: 1; border-radius: 9999px; background: #e5e7eb; transition: background-color .25s; }

        .reg-banner { display: flex; align-items: flex-start; gap: .75rem; margin-top: .75rem; padding: .625rem .5rem .625rem .875rem; border-left: 4px solid #10b981; border-radius: 0 .5rem .5rem 0; background: #ecfdf5; font-size: .875rem; }
        .reg-banner.is-hidden { display: none; }
        .reg-banner > i { color: #059669; margin-top: .15rem; }
        .reg-banner-body { flex: 1; min-width: 0; }
        .reg-banner-title { font-weight: 600; color: #064e3b; }
        .reg-banner-details { margin-top: .125rem; font-size: .75rem; color: #065f46; word-break: break-word; }
        .reg-banner button { width: 1.75rem; height: 1.75rem; display: flex; align-items: center; justify-content: center; border: 0; border-radius: .375rem; background: none; color: #047857; cursor: pointer; }
        .reg-banner button:hover { background: #d1fae5; }
        .reg-banner button:focus-visible { outline: 2px solid #10b981; }

        .reg-submit { width: 100%; height: 3rem; display: flex; align-items: center; justify-content: center; gap: .5rem; border: 0; border-radius: .75rem; background: #047857; color: #fff; font-size: .875rem; font-weight: 700; cursor: pointer; transition: background-color .15s; }
        .reg-submit:hover { background: #065f46; }
        .reg-submit:active { background: #064e3b; }
        .reg-submit:focus-visible { outline: none; box-shadow: 0 0 0 4px rgba(5,150,105,.3); }

        .reg-footer { margin: 2rem 0 0; text-align: center; font-size: .875rem; color: #6b7280; }
        .reg-footer a { margin-left: .25rem; font-weight: 700; color: #047857; text-decoration: none; }
        .reg-footer a:hover { color: #065f46; text-decoration: underline; text-underline-offset: 4px; }

        @media (prefers-reduced-motion: reduce) { .reg-input, .reg-strength span, .reg-submit, .reg-lang a { transition: none; } }
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
        <div class="reg-panel">

            {{-- Language Switcher --}}
            <div class="reg-lang">
                <a href="{{ route('locale.switch', ['locale' => 'km']) }}" class="{{ app()->getLocale() === 'km' ? 'active' : '' }}">ខ្មែរ</a>
                <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            </div>

            <div class="reg-wrap">

                {{-- Mobile logo --}}
                <div class="reg-mobile-brand">
                    <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo">
                    <span>Class Management System</span>
                </div>

                {{-- Heading --}}
                <h2 class="reg-title">{{ __('register_heading') }}</h2>
                <p class="reg-subtitle">{{ __('register_subtitle') }}</p>

                <form method="POST" action="{{ route('register') }}" class="reg-form">
                    @csrf

                    {{-- GROUP 1: Study details --}}
                    <fieldset class="reg-fieldset">
                        <legend class="reg-group-title">
                            <i class="fas fa-university"></i>
                            <span>{{ __('study_program') }} & {{ __('student_id_2') }}</span>
                        </legend>

                        {{-- Student ID Code --}}
                        <div class="reg-field">
                            <label for="student_id_code" class="reg-label">
                                {{ __('student_id_2') }} <span class="reg-req">*</span>
                            </label>
                            <div class="reg-box">
                                <span class="reg-icon"><i class="fas fa-id-badge"></i></span>
                                <input type="text" name="student_id_code" id="student_id_code" value="{{ old('student_id_code') }}" required
                                       autocomplete="off" class="reg-input has-icon has-spinner"
                                       placeholder="e.g. B-XVI-000123" />
                                <span id="idLookupSpinner" class="reg-spinner" style="display:none;">
                                    <i class="fas fa-circle-notch fa-spin"></i> Checking
                                </span>
                            </div>
                            <p class="reg-hint">{{ __('register_student_id_hint') }}</p>
                            <x-input-error :messages="$errors->get('student_id_code')" class="mt-1 text-xs text-rose-500" />

                            {{-- Student Auto-Found Banner (Populated via AJAX lookup) --}}
                            <div id="studentFoundBanner" class="reg-banner is-hidden">
                                <i class="fas fa-circle-check"></i>
                                <div class="reg-banner-body">
                                    <div class="reg-banner-title">{{ __('register_swal_found_title') }}</div>
                                    <div id="studentFoundDetails" class="reg-banner-details"></div>
                                </div>
                                <button type="button" onclick="dismissStudentFoundBanner()" aria-label="Dismiss">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Department --}}
                        <div class="reg-field">
                            <label for="department_id" class="reg-label">
                                {{ __('course') }} <span class="reg-req">*</span>
                            </label>
                            <select name="department_id" id="department_id" required class="reg-input">
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
                        <div class="reg-grid-2">
                            <div class="reg-field">
                                <label for="degree_level" class="reg-label">
                                    {{ __('degree_level') }} <span class="reg-req">*</span>
                                </label>
                                <select name="degree_level" id="degree_level" required class="reg-input">
                                    <option value="">{{ __('select_degree_level') }}</option>
                                    <option value="បរិញ្ញាបត្រ" {{ old('degree_level') == 'បរិញ្ញាបត្រ' ? 'selected' : '' }}>បរិញ្ញាបត្រ</option>
                                    <option value="បរិញ្ញាបត្ររង" {{ old('degree_level') == 'បរិញ្ញាបត្ររង' ? 'selected' : '' }}>បរិញ្ញាបត្ររង</option>
                                    <option value="អនុបណ្ឌិត" {{ old('degree_level') == 'អនុបណ្ឌិត' ? 'selected' : '' }}>អនុបណ្ឌិត</option>
                                    <option value="វិញ្ញាបនបត្រ" {{ old('degree_level') == 'វិញ្ញាបនបត្រ' ? 'selected' : '' }}>វិញ្ញាបនបត្រ</option>
                                    <option value="ផ្សេងៗ" {{ old('degree_level') == 'ផ្សេងៗ' ? 'selected' : '' }}>ផ្សេងៗ</option>
                                </select>
                                <x-input-error :messages="$errors->get('degree_level')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            <div class="reg-field">
                                <label for="generation" class="reg-label">
                                    {{ __('generation') }} <span class="reg-req">*</span>
                                </label>
                                <select name="generation" id="generation" required class="reg-input">
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

                    <hr class="reg-divider">

                    {{-- GROUP 2: Personal information --}}
                    <fieldset class="reg-fieldset">
                        <legend class="reg-group-title">
                            <i class="fas fa-user"></i>
                            <span>{{ __('personal_information') }}</span>
                        </legend>

                        <div class="reg-grid-2">
                            <div class="reg-field">
                                <label for="name" class="reg-label">
                                    {{ __('display_name') }} <span class="reg-req">*</span>
                                </label>
                                <div class="reg-box">
                                    <span class="reg-icon"><i class="fas fa-user-circle"></i></span>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="reg-input has-icon" placeholder="Full Name" />
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            <div class="reg-field">
                                <label for="email" class="reg-label">
                                    {{ __('email') }} <span class="reg-req">*</span>
                                </label>
                                <div class="reg-box">
                                    <span class="reg-icon"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                           class="reg-input has-icon" placeholder="name@nmu.edu.kh" />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
                            </div>
                        </div>
                    </fieldset>

                    <hr class="reg-divider">

                    {{-- GROUP 3: Password --}}
                    <fieldset class="reg-fieldset">
                        <legend class="reg-group-title">
                            <i class="fas fa-lock"></i>
                            <span>{{ $securityTitle }}</span>
                        </legend>

                        <div class="reg-grid-2">
                            <div class="reg-field">
                                <label for="password" class="reg-label">
                                    {{ __('password') }} <span class="reg-req">*</span>
                                </label>
                                <div class="reg-box">
                                    <span class="reg-icon"><i class="fas fa-key"></i></span>
                                    <input id="password" type="password" name="password" required autocomplete="new-password"
                                           class="reg-input has-icon has-eye" placeholder="••••••••" />
                                    <button type="button" class="reg-eye" onclick="togglePassword('password', 'eyeOpen1', 'eyeClosed1')" aria-label="Show or hide password">
                                        <i id="eyeOpen1" class="fas fa-eye"></i>
                                        <i id="eyeClosed1" class="fas fa-eye-slash hidden"></i>
                                    </button>
                                </div>
                                {{-- Password strength (4 segments) --}}
                                <div id="strength-bar" class="reg-strength" aria-hidden="true">
                                    <span></span><span></span><span></span><span></span>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
                            </div>

                            <div class="reg-field">
                                <label for="password_confirmation" class="reg-label">
                                    {{ __('confirm_password') }} <span class="reg-req">*</span>
                                </label>
                                <div class="reg-box">
                                    <span class="reg-icon"><i class="fas fa-shield-alt"></i></span>
                                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                           class="reg-input has-icon has-eye" placeholder="••••••••" />
                                    <button type="button" class="reg-eye" onclick="togglePassword('password_confirmation', 'eyeOpen2', 'eyeClosed2')" aria-label="Show or hide password">
                                        <i id="eyeOpen2" class="fas fa-eye"></i>
                                        <i id="eyeClosed2" class="fas fa-eye-slash hidden"></i>
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

                    {{-- Submit --}}
                    <button type="submit" class="reg-submit">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ __('btn_register') }}</span>
                    </button>
                </form>

                {{-- Login link --}}
                <p class="reg-footer">
                    {{ __('auth_already_have_account') }}
                    <a href="{{ route('login') }}">{{ __('log_in') }}</a>
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
            const segs = sBar.querySelectorAll('span');
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
            if (banner) banner.classList.add('is-hidden');
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
                                                banner.classList.remove('is-hidden');
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
                                            if (banner) banner.classList.add('is-hidden');
                                        }
                                    });
                                } else {
                                    if (banner) banner.classList.add('is-hidden');
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