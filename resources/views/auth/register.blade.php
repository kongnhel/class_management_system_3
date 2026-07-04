<link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
<title>{{ config('app.name', 'Class Management System') }} - Register</title>

<x-guest-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Battambang:wght@400;700&display=swap');
        body { font-family: 'Inter', 'Battambang', sans-serif; margin: 0; }
        .min-h-screen { padding: 0 !important; justify-content: stretch !important; align-items: stretch !important; max-width: 100% !important; }
        select { appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.2em 1.2em; }
    </style>

    {{-- Flash toasts are handled by <x-toast /> in the guest layout --}}

    <div class="min-h-screen flex">
        {{-- Left: Branding --}}
        <div class="hidden lg:flex lg:w-5/12 relative overflow-hidden items-center justify-center">
            <img src="{{ asset('assets/image/download (5).jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-emerald-900/70"></div>
            <div class="relative z-10 text-center px-12">
                <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" class="w-28 h-28 mx-auto mb-8 drop-shadow-2xl">
                <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">Class Management<br>System</h1>
                <p class="text-emerald-100 text-lg max-w-sm mx-auto leading-relaxed">ប្រព័ន្ធគ្រប់គ្រងសិក្សា សម្រាប់មហាវិទ្យាល័យ</p>
                <div class="mt-10 flex items-center justify-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-emerald-300 animate-pulse"></div>
                    <span class="text-emerald-200 text-sm font-medium">សូមបំពេញព័ត៌មានដើម្បីចុះឈ្មោះ</span>
                </div>
            </div>
        </div>

        {{-- Right: Register Form --}}
        <div class="w-full lg:w-7/12 flex items-center justify-center px-6 py-10 bg-gray-50 overflow-y-auto">
            <div class="w-full max-w-xl">
                {{-- Mobile Logo --}}
                <div class="lg:hidden text-center mb-6">
                    <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo" class="w-16 h-16 mx-auto mb-3">
                    <h2 class="text-xl font-bold text-gray-800">Class Management System</h2>
                </div>

                {{-- Header --}}
                <div class="mb-6">
                    <h2 class="text-3xl font-extrabold text-gray-900">បង្កើតគណនីថ្មី</h2>
                    <p class="text-gray-500 mt-2 text-sm">សូមបំពេញព័ត៌មានខាងក្រោមដើម្បីចុះឈ្មោះ</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    {{-- Student ID --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('លេខសម្គាល់និស្សិត') }}</label>
                        <input type="text" name="student_id_code" value="{{ old('student_id_code') }}" required 
                               class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none" 
                               placeholder="ID-0000X" />
                        <p class="text-xs text-gray-400 mt-1">* បញ្ចូលលេខសម្គាល់ដើម្បីទាញយកព័ត៌មានដែលរៀបចំដោយរដ្ឋបាល</p>
                        <x-input-error :messages="$errors->get('student_id_code')" class="mt-1 text-xs text-red-500" />
                    </div>

                    {{-- Name + Email --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ឈ្មោះបង្ហាញ') }}</label>
                            <input type="text" name="name" value="{{ old('name') }}" required 
                                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none" 
                                   placeholder="Full Name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('អ៊ីមែល') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" required 
                                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none" 
                                   placeholder="name@nmu.edu.kh" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500" />
                        </div>
                    </div>

                    {{-- Program + Degree Level + Generation --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('កម្មវិធីសិក្សា') }}</label>
                            <select name="program_id" required class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                                <option value="">{{ __('ជ្រើសរើសកម្មវិធី') }}</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name_km }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('program_id')" class="mt-1 text-xs text-red-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('កម្រិតសញ្ញាបត្រ') }}</label>
                            <select name="degree_level" required class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                                <option value="">{{ __('ជ្រើសរើសកម្រិតសញ្ញាបត្រ') }}</option>
                                <option value="បរិញ្ញាបត្រ" {{ old('degree_level') == 'បរិញ្ញាបត្រ' ? 'selected' : '' }}>បរិញ្ញាបត្រ</option>
                                <option value="បរិញ្ញាបត្ររង" {{ old('degree_level') == 'បរិញ្ញាបត្ររង' ? 'selected' : '' }}>បរិញ្ញាបត្ររង</option>
                                <option value="អនុបណ្ឌិត" {{ old('degree_level') == 'អនុបណ្ឌិត' ? 'selected' : '' }}>អនុបណ្ឌិត</option>
                                <option value="បណ្ឌិត" {{ old('degree_level') == 'បណ្ឌិត' ? 'selected' : '' }}>បណ្ឌិត</option>
                                <option value="វិញ្ញាបនបត្រ" {{ old('degree_level') == 'វិញ្ញាបនបត្រ' ? 'selected' : '' }}>វិញ្ញាបនបត្រ</option>
                                <option value="ផ្សេងៗ" {{ old('degree_level') == 'ផ្សេងៗ' ? 'selected' : '' }}>ផ្សេងៗ</option>
                            </select>
                            <x-input-error :messages="$errors->get('degree_level')" class="mt-1 text-xs text-red-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ជំនាន់') }}</label>
                            <select name="generation" required class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                                <option value="">{{ __('ជ្រើសរើសជំនាន់') }}</option>
                                @foreach($generations as $generation)
                                    <option value="{{ $generation }}" {{ old('generation') == $generation ? 'selected' : '' }}>{{ $generation }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('generation')" class="mt-1 text-xs text-red-500" />
                        </div>
                    </div>

                    {{-- Password + Confirm --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ពាក្យសម្ងាត់') }}</label>
                            <div class="relative">
                                <input id="password" type="password" name="password" required 
                                       class="block w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none" 
                                       placeholder="••••••••" />
                                <button type="button" onclick="togglePassword('password', 'eyeOpen1', 'eyeClosed1')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="eyeOpen1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg id="eyeClosed1" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div id="strength-bar" class="h-full w-0 transition-all duration-500 rounded-full"></div>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('បញ្ជាក់ពាក្យសម្ងាត់') }}</label>
                            <div class="relative">
                                <input id="password_confirmation" type="password" name="password_confirmation" required 
                                       class="block w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none" 
                                       placeholder="••••••••" />
                                <button type="button" onclick="togglePassword('password_confirmation', 'eyeOpen2', 'eyeClosed2')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="eyeOpen2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg id="eyeClosed2" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-red-500" />
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all duration-200 text-sm">
                        {{ __('ចុះឈ្មោះឥឡូវនេះ') }}
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-6">
                    {{ __('មានគណនីរួចហើយ?') }}
                    <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700 ml-1">{{ __('ចូលគណនី') }}</a>
                </p>
            </div>
        </div>
    </div>

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

        // Password strength
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
                const colors = ['bg-transparent', 'bg-red-500', 'bg-orange-500', 'bg-yellow-400', 'bg-emerald-500'];
                sBar.className = `h-full transition-all duration-500 rounded-full ${colors[strength]}`;
                sBar.style.width = (strength * 25) + '%';
            });
        }
    </script>

    {{-- SweetAlert2 for Student ID lookup --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const studentIdInput = document.querySelector('input[name="student_id_code"]');
            if (studentIdInput) {
                studentIdInput.addEventListener('blur', function() {
                    let code = this.value;
                    if (code.length >= 3) {
                        Swal.fire({
                            title: 'កំពុងស្វែងរកទិន្នន័យ...',
                            html: 'សូមរង់ចាំមួយភ្លែត',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        fetch(`/api/check-student/${code}`)
                            .then(res => res.json())
                            .then(data => {
                                Swal.close();
                                if (data.success) {
                                    Swal.fire({
                                        title: 'រកឃើញអត្តសញ្ញាណរបស់អ្នក!',
                                        html: `តើអ្នកពិតជាមានឈ្មោះ <b>${data.name}</b> ជំនាន់ <b>${data.generation}</b> មែនដែរឬទេ?`,
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonColor: '#10b981',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'បាទ/ចាស ត្រឹមត្រូវ',
                                        cancelButtonText: 'មិនមែនទេ'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            document.querySelector('input[name="name"]').value = data.name;
                                            document.querySelector('select[name="program_id"]').value = data.program_id;
                                            document.querySelector('select[name="generation"]').value = data.generation;
                                            Swal.fire({ title: 'អរគុណ!', text: 'សូមបន្តបង្កើតអ៊ីមែល និងពាក្យសម្ងាត់របស់អ្នក។', icon: 'success', timer: 2000, showConfirmButton: false });
                                        } else {
                                            studentIdInput.value = '';
                                        }
                                    });
                                } else {
                                    Swal.fire({ title: 'រកមិនឃើញ!', text: 'លេខសម្គាល់និស្សិតនេះមិនទាន់មានក្នុងប្រព័ន្ធរដ្ឋបាលឡើយ។', icon: 'error' });
                                    studentIdInput.value = '';
                                }
                            })
                            .catch(error => {
                                Swal.close();
                                Swal.fire('Error!', 'មានបញ្ហាបច្ចេកទេស។', 'error');
                            });
                    }
                });
            }
        });
    </script>
</x-guest-layout>
