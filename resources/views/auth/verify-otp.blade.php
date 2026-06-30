<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }} - ផ្ទៀងផ្ទាត់កូដ</title>

<x-guest-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { font-family: 'Battambang', sans-serif; margin: 0; }
        .min-h-screen { padding: 0 !important; justify-content: stretch !important; align-items: stretch !important; max-width: 100% !important; }
        .otp-input { width: 48px; height: 56px; text-align: center; font-size: 24px; font-weight: 700; border: 2px solid #e5e7eb; border-radius: 12px; outline: none; transition: all 0.2s; }
        .otp-input:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
        .otp-input.filled { border-color: #10b981; background-color: #ecfdf5; }
    </style>

    {{-- Flash toasts are handled by <x-toast /> in the guest layout --}}

    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-6 py-12">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900">ផ្ទៀងផ្ទាត់កូដ</h2>
                <p class="text-gray-500 mt-2 text-sm leading-relaxed">
                    កូដផ្ទៀងផ្ទាត់ ៥ ខ្ទង់ត្រូវបានផ្ញើទៅកាន់<br>
                    <span class="font-bold text-gray-700">{{ $maskedContact }}</span>
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
                    @csrf

                    <div class="flex justify-center gap-3 mb-6">
                        <input type="text" maxlength="1" class="otp-input" id="otp-1" name="otp[]" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" required>
                        <input type="text" maxlength="1" class="otp-input" id="otp-2" name="otp[]" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" class="otp-input" id="otp-3" name="otp[]" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" class="otp-input" id="otp-4" name="otp[]" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" class="otp-input" id="otp-5" name="otp[]" inputmode="numeric" pattern="[0-9]">
                    </div>

                    <input type="hidden" name="otp" id="otp-full">

                    @if(session('error'))
                        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-center">
                            <p class="text-sm text-red-600 font-medium">{{ session('error') }}</p>
                        </div>
                    @endif

                    <button type="submit" id="verifyBtn" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        ផ្ទៀងផ្ទាត់
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">មិនទទួលបានកូដ?</p>
                    <button type="button" id="resendBtn" onclick="resendOtp()" class="mt-2 text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        ផ្ញើឡើងវិញ <span id="cooldown"></span>
                    </button>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                        ← ត្រឡប់ទៅការចូលប្រព័ន្ធ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const fullInput = document.getElementById('otp-full');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendBtn = document.getElementById('resendBtn');
        const cooldownEl = document.getElementById('cooldown');
        let cooldown = {{ $user->otp_last_sent_at ? \App\Services\OtpService::OTP_RESEND_COOLDOWN_SECONDS - now()->diffInSeconds($user->otp_last_sent_at, false) : 0 }};
        cooldown = Math.max(0, cooldown);

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val;

                if (val) {
                    e.target.classList.add('filled');
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                } else {
                    e.target.classList.remove('filled');
                }

                updateFullOtp();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                    inputs[index - 1].value = '';
                    inputs[index - 1].classList.remove('filled');
                    updateFullOtp();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                for (let i = 0; i < Math.min(paste.length, inputs.length); i++) {
                    inputs[i].value = paste[i];
                    inputs[i].classList.add('filled');
                }
                if (paste.length >= inputs.length) {
                    inputs[inputs.length - 1].focus();
                } else {
                    inputs[paste.length].focus();
                }
                updateFullOtp();
            });

            input.addEventListener('focus', () => {
                input.select();
            });
        });

        function updateFullOtp() {
            let otp = '';
            inputs.forEach(input => { otp += input.value; });
            fullInput.value = otp;
            verifyBtn.disabled = otp.length !== 5;
        }

        function startCooldown() {
            if (cooldown <= 0) {
                resendBtn.disabled = false;
                cooldownEl.textContent = '';
                return;
            }

            resendBtn.disabled = true;
            const interval = setInterval(() => {
                cooldown--;
                if (cooldown <= 0) {
                    clearInterval(interval);
                    resendBtn.disabled = false;
                    cooldownEl.textContent = '';
                } else {
                    cooldownEl.textContent = `(${cooldown}s)`;
                }
            }, 1000);
            cooldownEl.textContent = `(${cooldown}s)`;
        }

        function resendOtp() {
            resendBtn.disabled = true;
            cooldown = {{ \App\Services\OtpService::OTP_RESEND_COOLDOWN_SECONDS }};

            fetch('{{ route("otp.resend") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(res => res.json()).then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'មានបញ្ហា។', 'error');
                }
            }).catch(() => {
                window.location.reload();
            });

            startCooldown();
        }

        startCooldown();
        inputs[0].focus();
    </script>
</x-guest-layout>