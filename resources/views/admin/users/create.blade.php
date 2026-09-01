<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.manage-users') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 leading-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-green-100 text-green-600">
                        <i class="fas fa-user-plus"></i>
                    </span>
                    {{ __('បង្កើតអ្នកប្រើប្រាស់ថ្មី') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.store-user') }}" enctype="multipart/form-data" novalidate
                x-data="{
                    userRole: '{{ old('role', 'professor') }}',
                    profilePicturePreview: null,
                    passwordVisible: false,
                    passwordConfirmVisible: false,
                    passwordStrength: '',
                    passwordStrengthColor: '',
                    passwordValue: '',
                    fieldErrors: {},
                    touched: {},

                    validateField(name) {
                        let val = '';
                        if (name === 'name') val = document.getElementById('name')?.value || '';
                        else if (name === 'role') val = this.userRole;
                        else if (name === 'email') val = document.getElementById('email')?.value || '';
                        else if (name === 'password') val = this.passwordValue;
                        else if (name === 'password_confirmation') val = document.getElementById('password_confirmation')?.value || '';
                        else if (name === 'faculty_id') val = document.getElementById('faculty_id')?.value || '';
                        else if (name === 'department_id') val = document.getElementById('department_id')?.value || '';
                        else if (name === 'program_id') val = document.getElementById('program_id')?.value || '';
                        else if (name === 'degree_level') val = document.getElementById('degree_level')?.value || '';
                        else if (name === 'generation') val = document.getElementById('generation')?.value || '';

                        let err = '';
                        if (name === 'name') {
                            if (!val.trim()) err = '{{ __("ឈ្មោះអ្នកប្រើប្រាស់ត្រូវតែបំពេញ") }}';
                            else if (val.length > 255) err = '{{ __("ឈ្មោះមិនអាចធំជាង 255 តួអក្សរឡើយ") }}';
                        } else if (name === 'role') {
                            if (!val) err = '{{ __("សូមជ្រើសរើសតួនាទី") }}';
                        } else if (name === 'email') {
                            if ((this.userRole === 'admin' || this.userRole === 'professor') && !val.trim()) err = '{{ __("អ៊ីម៉ែលត្រូវតែបំពេញ") }}';
                            else if (val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) err = '{{ __("អ៊ីម៉ែលមិនត្រឹមត្រូវ") }}';
                        } else if (name === 'password') {
                            if ((this.userRole === 'admin' || this.userRole === 'professor')) {
                                if (!val) err = '{{ __("ពាក្យសម្ងាត់ត្រូវតែបំពេញ") }}';
                                else if (val.length < 8) err = '{{ __("ពាក្យសម្ងាត់ត្រូវតែមានយ៉ាងតិច 8 តួអក្សរ") }}';
                                else if (!/[a-z]/.test(val)) err = '{{ __("ពាក្យសម្ងាត់ត្រូវតែមានអក្ខរាតូចយ៉ាងតិចមួយ") }}';
                                else if (!/[A-Z]/.test(val)) err = '{{ __("ពាក្យសម្ងាត់ត្រូវតែមានអក្ខរាធ្ងន់យ៉ាងតិចមួយ") }}';
                                else if (!/[0-9]/.test(val)) err = '{{ __("ពាក្យសម្ងាត់ត្រូវតែមានចំនួនយ៉ាងតិចមួយ") }}';
                                else if (!/[@$!%*?&#]/.test(val)) err = '{{ __("ពាក្យសម្ងាត់ត្រូវតែមានសញ្ញាពិសេសយ៉ាងតិចមួយ") }}';
                            }
                        } else if (name === 'password_confirmation') {
                            let pw = document.getElementById('password')?.value || '';
                            if ((this.userRole === 'admin' || this.userRole === 'professor') && val !== pw) err = '{{ __("ពាក្យសម្ងាត់មិនត្រូវគ្នា") }}';
                        } else if (name === 'faculty_id') {
                            if (this.userRole === 'professor' && !val) err = '{{ __("សូមជ្រើសរើសមហាវិទ្យាល័យ") }}';
                        } else if (name === 'department_id') {
                            if (this.userRole === 'professor' && !val) err = '{{ __("សូមជ្រើសរើសដេប៉ាតឺម៉ង់") }}';
                        } else if (name === 'program_id') {
                            if (this.userRole === 'student' && !val) err = '{{ __("សូមជ្រើសរើសកម្មវិធីសិក្សា") }}';
                        } else if (name === 'degree_level') {
                            if (this.userRole === 'student' && !val) err = '{{ __("សូមជ្រើសរើសកម្រិតសញ្ញាបត្រ") }}';
                        } else if (name === 'generation') {
                            if (this.userRole === 'student' && !val) err = '{{ __("សូមជ្រើសរើសជំនាន់") }}';
                        }

                        if (err) this.fieldErrors[name] = err;
                        else delete this.fieldErrors[name];
                    },

                    onBlur(name) {
                        this.touched[name] = true;
                        this.validateField(name);
                    },

                    onInput(name) {
                        if (this.touched[name]) this.validateField(name);
                    }
                }" class="space-y-6">
                @csrf
                <input type="hidden" id="profile_picture_base64" name="profile_picture_base64" value="" />

                {{-- Section 1: Basic Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fas fa-user"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('ព័ត៌មានមូលដ្ឋាន') }}</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                        <div>
                            <x-input-label for="name" class="font-semibold text-gray-700 mb-1.5">
                                {{ __('ឈ្មោះអ្នកប្រើប្រាស់') }}
                            </x-input-label>
                            <x-text-input id="name" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="name" :value="old('name')" placeholder="{{ __('បញ្ចូលឈ្មោះអ្នកប្រើប្រាស់') }}" required autofocus @blur="onBlur('name')" @input="onInput('name')" :class="fieldErrors.name ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : ''" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            <p x-show="fieldErrors.name" x-text="fieldErrors.name" class="text-sm text-red-600 mt-2"></p>
                        </div>

                        <div>
                            <x-input-label for="role" class="font-semibold text-gray-700 mb-1.5">
                                {{ __('តួនាទី') }}
                            </x-input-label>
                            <select id="role" name="role" x-model="userRole" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]" required @blur="onBlur('role')" @change="touched.role = true; validateField('role'); Object.keys(fieldErrors).forEach(k => { if (k !== 'role') validateField(k); })" :class="fieldErrors.role ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : ''">
                                <option value="">{{ __('ជ្រើសរើសតួនាទី') }}</option>
                                <option value="admin">Admin</option>
                                <option value="professor">Professor</option>
                                <option value="student">Student</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            <p x-show="fieldErrors.role" x-text="fieldErrors.role" class="text-sm text-red-600 mt-2"></p>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Account Info (Admin/Professor) --}}
                <div x-show="userRole === 'admin' || userRole === 'professor'" x-cloak x-transition class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-100 text-purple-600">
                            <i class="fas fa-key"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('ព័ត៌មានគណនី') }}</h3>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <x-input-label for="email" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-envelope mr-1.5 text-purple-500"></i> {{ __('អ៊ីម៉ែល') }}
                            </x-input-label>
                            <x-text-input id="email" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="email" name="email" :value="old('email')" placeholder="example@gmail.com" @blur="onBlur('email')" @input="onInput('email')" :class="fieldErrors.email ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : ''" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            <p x-show="fieldErrors.email" x-text="fieldErrors.email" class="text-sm text-red-600 mt-2"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- ពាក្យសម្ងាត់ --}}
                            <div>
                                <x-input-label for="password" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-lock mr-1.5 text-purple-500"></i>
                                    {{ __('ពាក្យសម្ងាត់') }}
                                </x-input-label>
                                <div class="relative w-full h-[50px]">
                                    <input
                                        id="password"
                                        :type="passwordVisible ? 'text' : 'password'"
                                        name="password"
                                        autocomplete="new-password"
                                        placeholder="{{ __('បញ្ចូលពាក្យសម្ងាត់') }}"
                                        class="block w-full h-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 pr-12 shadow-sm text-gray-900 bg-white transition"
                                        required
                                        @blur="onBlur('password')"
                                        @input="
                                            let v = $event.target.value;
                                            passwordValue = v;
                                            let s = 0;
                                            if (/[A-Z]/.test(v)) s++;
                                            if (/[a-z]/.test(v)) s++;
                                            if (/[0-9]/.test(v)) s++;
                                            if (/[@$!%*?&]/.test(v)) s++;
                                            if (v.length >= 8) s++;
                                            let levels = ['{{ __("ខ្សោយ") }}','{{ __("មធ្យម") }}','{{ __("ល្អ") }}','{{ __("ខ្លាំង") }}','{{ __("ខ្លាំងណាស់") }}'];
                                            let colors = ['text-red-400','text-yellow-400','text-green-400','text-green-500','text-green-600'];
                                            passwordStrength = v ? levels[s > 0 ? s - 1 : 0] : '';
                                            passwordStrengthColor = v ? colors[s > 0 ? s - 1 : 0] : '';
                                            onInput('password');
                                        "
                                        :class="fieldErrors.password ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : ''"
                                    />
                                    <button
                                        type="button"
                                        @click="passwordVisible = !passwordVisible"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600 transition h-full"
                                    >
                                        <i class="fas" :class="passwordVisible ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <p x-show="passwordStrength" x-text="'{{ __('កម្លាំងពាក្យសម្ងាត់៖') }} ' + passwordStrength" :class="passwordStrengthColor" class="text-sm mt-2"></p>
                                <div x-show="(userRole === 'admin' || userRole === 'professor') && passwordValue" class="mt-2 space-y-1">
                                    <p class="text-xs font-bold text-gray-500 mb-1">{{ __('តម្រូវការពាក្យសម្ងាត់៖') }}</p>
                                    <div class="flex items-center gap-2 text-xs" :class="passwordValue.length >= 8 ? 'text-green-600' : 'text-gray-400'">
                                        <i class="fas" :class="passwordValue.length >= 8 ? 'fa-check-circle' : 'fa-circle'"></i>
                                        <span>{{ __('យ៉ាងតិច ៨ តួអក្សរ') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs" :class="/[a-z]/.test(passwordValue) ? 'text-green-600' : 'text-gray-400'">
                                        <i class="fas" :class="/[a-z]/.test(passwordValue) ? 'fa-check-circle' : 'fa-circle'"></i>
                                        <span>{{ __('អក្ខរាតូច') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs" :class="/[A-Z]/.test(passwordValue) ? 'text-green-600' : 'text-gray-400'">
                                        <i class="fas" :class="/[A-Z]/.test(passwordValue) ? 'fa-check-circle' : 'fa-circle'"></i>
                                        <span>{{ __('អក្ខរាធ្ងន់') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs" :class="/[0-9]/.test(passwordValue) ? 'text-green-600' : 'text-gray-400'">
                                        <i class="fas" :class="/[0-9]/.test(passwordValue) ? 'fa-check-circle' : 'fa-circle'"></i>
                                        <span>{{ __('ចំនួន') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs" :class="/[@$!%*?&#]/.test(passwordValue) ? 'text-green-600' : 'text-gray-400'">
                                        <i class="fas" :class="/[@$!%*?&#]/.test(passwordValue) ? 'fa-check-circle' : 'fa-circle'"></i>
                                        <span>{{ __('សញ្ញាពិសេស (@$!%*?&)') }}</span>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                <p x-show="fieldErrors.password" x-text="fieldErrors.password" class="text-sm text-red-600 mt-2"></p>
                            </div>

                            {{-- បញ្ជាក់ពាក្យសម្ងាត់ --}}
                            <div>
                                <x-input-label for="password_confirmation" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-shield-alt mr-1.5 text-purple-500"></i>
                                    {{ __('បញ្ជាក់ពាក្យសម្ងាត់') }}
                                </x-input-label>
                                <div class="relative w-full h-[50px]">
                                    <input
                                        id="password_confirmation"
                                        :type="passwordConfirmVisible ? 'text' : 'password'"
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                        placeholder="{{ __('វាយពាក្យសម្ងាត់ម្តងទៀត') }}"
                                        class="block w-full h-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 pr-12 shadow-sm text-gray-900 bg-white transition"
                                        required
                                        @blur="onBlur('password_confirmation')"
                                        @input="onInput('password_confirmation')"
                                        :class="fieldErrors.password_confirmation ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : ''"
                                    />
                                    <button
                                        type="button"
                                        @click="passwordConfirmVisible = !passwordConfirmVisible"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600 transition h-full"
                                    >
                                        <i class="fas" :class="passwordConfirmVisible ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                <p x-show="fieldErrors.password_confirmation" x-text="fieldErrors.password_confirmation" class="text-sm text-red-600 mt-2"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Student Info --}}
                <div x-show="userRole === 'student'" x-cloak x-transition class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fas fa-graduation-cap"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('ព័ត៌មាននិស្សិត') }}</h3>
                    </div>

                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-5">
                        <p class="text-sm text-emerald-700 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            {{ __('លេខសម្គាល់និស្សិត៖') }} <span id="preview-student-id" class="font-bold text-emerald-800 font-mono">—</span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
                        <div>
                            <x-input-label for="program_id" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-graduation-cap mr-1.5 text-emerald-500"></i> {{ __('កម្មវិធីសិក្សា') }}
                            </x-input-label>
                            <select id="program_id" name="program_id" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]" required @blur="onBlur('program_id')" @change="touched.program_id = true; validateField('program_id')" :class="fieldErrors.program_id ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : ''">
                                <option value="">{{ __('ជ្រើសរើសកម្មវិធីសិក្សា') }}</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name_km }}</option>
                                @endforeach
                            </select>
                            <p x-show="fieldErrors.program_id" x-text="fieldErrors.program_id" class="text-sm text-red-600 mt-2"></p>
                        </div>
                        <div>
                            <x-input-label for="degree_level" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-award mr-1.5 text-emerald-500"></i> {{ __('កម្រិតសញ្ញាបត្រ') }}
                            </x-input-label>
                            <select id="degree_level" name="degree_level" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]" required @blur="onBlur('degree_level')" @change="touched.degree_level = true; validateField('degree_level')" :class="fieldErrors.degree_level ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : ''">
                                <option value="">{{ __('ជ្រើសរើសកម្រិតសញ្ញាបត្រ') }}</option>
                                <option value="បរិញ្ញាបត្រ">{{ __('បរិញ្ញាបត្រ') }}</option>
                                <option value="បរិញ្ញាបត្ររង">{{ __('បរិញ្ញាបត្ររង') }}</option>
                                <option value="អនុបណ្ឌិត">{{ __('អនុបណ្ឌិត') }}</option>
                                <option value="បណ្ឌិត">{{ __('បណ្ឌិត') }}</option>
                                <option value="វិញ្ញាបនបត្រ">{{ __('វិញ្ញាបនបត្រ') }}</option>
                                <option value="ផ្សេងៗ">{{ __('ផ្សេងៗ') }}</option>
                            </select>
                            <p x-show="fieldErrors.degree_level" x-text="fieldErrors.degree_level" class="text-sm text-red-600 mt-2"></p>
                        </div>
                        <div>
                            <x-input-label for="generation" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-layer-group mr-1.5 text-emerald-500"></i> {{ __('ជំនាន់') }}
                            </x-input-label>
                            <select id="generation" name="generation" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]" required @blur="onBlur('generation')" @change="touched.generation = true; validateField('generation')" :class="fieldErrors.generation ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : ''">
                                <option value="">{{ __('ជ្រើសរើសជំនាន់') }}</option>
                                @foreach(\App\Models\Generation::where('is_active', true)->orderByDesc('name')->get() as $gen)
                                    <option value="{{ $gen->name }}">{{ $gen->name }} ({{ __('ចូលរៀនឆ្នាំ') }} {{ $gen->join_year }})</option>
                                @endforeach
                            </select>
                            <p x-show="fieldErrors.generation" x-text="fieldErrors.generation" class="text-sm text-red-600 mt-2"></p>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Professor Info --}}
                <div x-show="userRole === 'professor'" x-cloak x-transition class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('ព័ត៌មានសាស្ត្រាចារ្យ') }}</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                        <div>
                            <x-input-label for="faculty_id" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-university mr-1.5 text-emerald-500"></i> {{ __('មហាវិទ្យាល័យ') }}
                            </x-input-label>
                            <select id="faculty_id" name="faculty_id" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]" @blur="onBlur('faculty_id')" @change="touched.faculty_id = true; validateField('faculty_id')" :class="fieldErrors.faculty_id ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : ''">
                                <option value="">{{ __('ជ្រើសរើសមហាវិទ្យាល័យ') }}</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}">{{ $faculty->name_km ?? $faculty->name_en }}</option>
                                @endforeach
                            </select>
                            <p x-show="fieldErrors.faculty_id" x-text="fieldErrors.faculty_id" class="text-sm text-red-600 mt-2"></p>
                        </div>
                        <div>
                            <x-input-label for="department_id" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-building mr-1.5 text-emerald-500"></i> {{ __('ដេប៉ាតឺម៉ង់') }}
                            </x-input-label>
                            <select id="department_id" name="department_id" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]" @blur="onBlur('department_id')" @change="touched.department_id = true; validateField('department_id')" :class="fieldErrors.department_id ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : ''">
                                <option value="">{{ __('សូមជ្រើសរើសដេប៉ាតឺម៉ង់') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                            <p x-show="fieldErrors.department_id" x-text="fieldErrors.department_id" class="text-sm text-red-600 mt-2"></p>
                        </div>
                    </div>
                </div>

                {{-- Section 5: Profile Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-orange-100 text-orange-600">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('ព័ត៌មានផ្ទាល់ខ្លួន') }}</h3>
                    </div>

                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="flex flex-col items-center space-y-3">
                            <div class="relative group">
                                <div class="h-32 w-32 rounded-2xl bg-gray-50 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative group-hover:border-emerald-400 transition">
                                    <template x-if="profilePicturePreview">
                                        <img :src="profilePicturePreview.includes('ik.imagekit.io') ? profilePicturePreview + '?tr=w-300,h-300,fo-face' : profilePicturePreview" 
                                             class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!profilePicturePreview">
                                        <i class="fas fa-camera text-3xl text-gray-300"></i>
                                    </template>
                                </div>
                                <label class="absolute -bottom-2 -right-2 bg-emerald-600 text-white p-2 rounded-xl cursor-pointer hover:bg-emerald-700 shadow-lg transition-all hover:scale-110 active:scale-95">
                                    <i class="fas fa-pen text-xs"></i>
                                    <input type="file" name="" class="hidden" 
                                        @change="
                                            const file = $event.target.files[0];
                                            if (file) {
                                                if (file.size > 5 * 1024 * 1024) {
                                                    showToast('{{ __("រូបភាពធំពេក! សូមជ្រើសរើសរូបភាពដែលមានទំហំតូចជាង 5MB") }}', 'error');
                                                    $event.target.value = '';
                                                    profilePicturePreview = '';
                                                } else {
                                                    profilePicturePreview = URL.createObjectURL(file);
                                                    (async function() {
                                                        var dataUrl = await window._compressToBase64(file);
                                                        document.getElementById('profile_picture_base64').value = dataUrl;
                                                    })();
                                                }
                                            }
                                        ">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 font-medium">{{ __('រូបភាព Profile (4x6)') }}</p>
                        </div>

                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                            <div>
                                <x-input-label for="full_name_km" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-file-alt mr-1.5 text-orange-500"></i> {{ __('ឈ្មោះពេញ (ខ្មែរ)') }}
                                </x-input-label>
                                <x-text-input id="full_name_km" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="full_name_km" :value="old('full_name_km')" placeholder="{{ __('បញ្ចូលឈ្មោះពេញជាភាសាខ្មែរ') }}" />
                            </div>
                            <div>
                                <x-input-label for="full_name_en" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-file-alt mr-1.5 text-orange-500"></i> {{ __('ឈ្មោះពេញ (អង់គ្លេស)') }}
                                </x-input-label>
                                <x-text-input id="full_name_en" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="full_name_en" :value="old('full_name_en')" placeholder="FULL NAME IN ENGLISH" />
                            </div>
                            <div>
                                <x-input-label for="gender" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-venus-mars mr-1.5 text-orange-500"></i> {{ __('ភេទ') }}
                                </x-input-label>
                                <select id="gender" name="gender" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]">
                                    <option value="">{{ __('ជ្រើសរើសភេទ') }}</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('ប្រុស') }}</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('ស្រី') }}</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('ផ្សេងទៀត') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="phone_number" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-phone mr-1.5 text-orange-500"></i> {{ __('លេខទូរស័ព្ទ') }}
                                </x-input-label>
                                <x-text-input id="phone_number" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="phone_number" :value="old('phone_number')" placeholder="012 345 678" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="address" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-map-marker-alt mr-1.5 text-orange-500"></i> {{ __('អាសយដ្ឋាន') }}
                                </x-input-label>
                                <x-text-input id="address" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="address" :value="old('address')" placeholder="{{ __('បញ្ចូលអាសយដ្ឋាន') }}" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('admin.manage-users') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                        <i class="fas fa-times"></i> {{ __('បោះបង់') }}
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl font-bold text-white hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all shadow-lg shadow-green-200">
                        <i class="fas fa-save"></i> {{ __('រក្សាទុក និងបង្កើតអ្នកប្រើប្រាស់') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const facultySelect = document.getElementById('faculty_id');
            const departmentSelect = document.getElementById('department_id');
            const oldFacultyId = '{{ old('faculty_id') }}';
            const oldDepartmentId = '{{ old('department_id') }}';

            function updateDepartments(facultyId, defaultDepartmentId = null) {
                if (!departmentSelect) return;
                departmentSelect.innerHTML = '<option value="">{{ __("កំពុងទាញយក...") }}</option>';
                departmentSelect.disabled = true;

                if (!facultyId) {
                    departmentSelect.innerHTML = '<option value="">{{ __("សូមជ្រើសរើសមហាវិទ្យាល័យជាមុនសិន") }}</option>';
                    return;
                }

                fetch(`/admin/get-departments-by-faculty/${facultyId}`)
                    .then(response => response.json())
                    .then(departments => {
                        departmentSelect.innerHTML = '<option value="">{{ __("ជ្រើសរើសដេប៉ាតឺម៉ង់") }}</option>';
                        departments.forEach(department => {
                            const option = document.createElement('option');
                            option.value = department.id;
                            option.textContent = department.name_km || department.name_en;
                            if (department.id == defaultDepartmentId) {
                                option.selected = true;
                            }
                            departmentSelect.appendChild(option);
                        });
                        departmentSelect.disabled = false;
                    })
                    .catch(error => console.error('Error fetching departments:', error));
            }

                if (facultySelect) {
                    facultySelect.addEventListener('change', function() {
                        updateDepartments(this.value);
                    });
                    if (oldFacultyId) {
                        updateDepartments(oldFacultyId, oldDepartmentId);
                    }
                }
            });

        // Student ID Preview
        const programSelect = document.getElementById('program_id');
        const degreeSelect = document.getElementById('degree_level');
        const generationSelect = document.getElementById('generation');
        const previewEl = document.getElementById('preview-student-id');
        let previewTimer = null;

        function fetchPreview() {
            const programId = programSelect?.value;
            const degreeLevel = degreeSelect?.value;
            const generation = generationSelect?.value;

            if (!programId || !degreeLevel || !generation) {
                previewEl.textContent = '—';
                return;
            }

            clearTimeout(previewTimer);
            previewTimer = setTimeout(() => {
                fetch('{{ route("admin.preview-student-id") }}?program_id=' + programId + '&degree_level=' + encodeURIComponent(degreeLevel) + '&generation=' + generation, {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.student_id) {
                        previewEl.textContent = data.student_id;
                    }
                })
                .catch(() => { previewEl.textContent = '—'; });
            }, 300);
        }

        if (programSelect) programSelect.addEventListener('change', fetchPreview);
        if (degreeSelect) degreeSelect.addEventListener('change', fetchPreview);
        if (generationSelect) generationSelect.addEventListener('change', fetchPreview);

        window._compressToBase64 = function(file) {
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
                                    fr.onload = function(e) { resolve(e.target.result); };
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
        };
    </script>
</x-app-layout>