<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        {{-- Dark Gradient Header --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-16 pt-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.manage-users') }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="fas fa-arrow-left text-white"></i>
                    </a>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                            <i class="fas fa-user-plus text-emerald-300 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight">{{ __('create_new_user') }}</h2>
                            <p class="text-slate-400 mt-1 text-sm">{{ __('fill_details_for_new_user_account') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 pb-12 relative z-10">

            {{-- Error Banner --}}
            @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 text-sm">{{ __('there_is_a_problem') }}</p>
                        <ul class="text-red-600 text-xs mt-1 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xs"></i></button>
                </div>
            </div>
            @endif

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
                        else if (name === 'department_id') val = document.getElementById('student_department_id')?.value || document.getElementById('professor_department_id')?.value || '';
                        else if (name === 'degree_level') val = document.getElementById('degree_level')?.value || '';
                        else if (name === 'generation') val = document.getElementById('generation')?.value || '';

                        let err = '';
                        if (name === 'name') {
                            if (!val.trim()) err = '{{ __("validation_name_required") }}';
                            else if (val.length > 255) err = '{{ __("validation_name_max") }}';
                        } else if (name === 'role') {
                            if (!val) err = '{{ __("validation_role_required") }}';
                        } else if (name === 'email') {
                            if ((this.userRole === 'admin' || this.userRole === 'professor') && !val.trim()) err = '{{ __("validation_email_required") }}';
                            else if (val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) err = '{{ __("validation_email_invalid") }}';
                        } else if (name === 'password') {
                            if ((this.userRole === 'admin' || this.userRole === 'professor')) {
                                if (!val) err = '{{ __("validation_password_required") }}';
                                else if (val.length < 8) err = '{{ __("validation_password_min") }}';
                                else if (!/[a-z]/.test(val)) err = '{{ __("validation_password_lowercase") }}';
                                else if (!/[A-Z]/.test(val)) err = '{{ __("validation_password_uppercase") }}';
                                else if (!/[0-9]/.test(val)) err = '{{ __("validation_password_number") }}';
                                else if (!/[@$!%*?&#]/.test(val)) err = '{{ __("validation_password_special") }}';
                            }
                        } else if (name === 'password_confirmation') {
                            let pw = document.getElementById('password')?.value || '';
                            if ((this.userRole === 'admin' || this.userRole === 'professor') && val !== pw) err = '{{ __("validation_password_confirm") }}';
                        } else if (name === 'faculty_id') {
                            if (this.userRole === 'professor' && !val) err = '{{ __("validation_faculty_required") }}';
                        } else if (name === 'department_id') {
                            if (this.userRole === 'student' && !val) err = '{{ __("please_select_a_department") }}';
                        } else if (name === 'degree_level') {
                            if (this.userRole === 'student' && !val) err = '{{ __("validation_degree_required") }}';
                        } else if (name === 'generation') {
                            if (this.userRole === 'student' && !val) err = '{{ __("validation_generation_required") }}';
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
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <span class="text-emerald-600 font-bold text-sm">1</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('basic_information') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('basic_info_for_new_user') }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('user_name') }} <span class="text-red-500">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('enter_username') }}"
                                class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5"
                                required autofocus @blur="onBlur('name')" @input="onInput('name')"
                                x-bind:class="fieldErrors.name ? 'ring-2 ring-red-400 bg-red-50' : ''" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            <p x-show="fieldErrors.name" x-text="fieldErrors.name" class="text-sm text-red-600 mt-2"></p>
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('role') }} <span class="text-red-500">*</span></label>
                            <select id="role" name="role" x-model="userRole"
                                class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5"
                                required @blur="onBlur('role')" @change="touched.role = true; validateField('role'); Object.keys(fieldErrors).forEach(k => { if (k !== 'role') validateField(k); })"
                                x-bind:class="fieldErrors.role ? 'ring-2 ring-red-400 bg-red-50' : ''">
                                <option value="">{{ __('select_a_role') }}</option>
                                <option value="admin">Admin</option>
                                <option value="professor">Professor</option>
                                <option value="student">Student</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            <p x-show="fieldErrors.role" x-text="fieldErrors.role" class="text-sm text-red-600 mt-2"></p>
                        </div>
                    </div>
                </div>

                {{-- Section 2A: Account Info (Admin/Professor) --}}
                <template x-if="userRole === 'admin' || userRole === 'professor'">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                            <span class="text-purple-600 font-bold text-sm">2</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('account_information') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('fill_account_info_for_new_user') }}</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">
                                <i class="fas fa-envelope mr-1.5 text-purple-500"></i> {{ __('email_2') }} <span class="text-red-500">*</span>
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="example@gmail.com"
                                class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5"
                                @blur="onBlur('email')" @input="onInput('email')"
                                x-bind:class="fieldErrors.email ? 'ring-2 ring-red-400 bg-red-50' : ''" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            <p x-show="fieldErrors.email" x-text="fieldErrors.email" class="text-sm text-red-600 mt-2"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-lock mr-1.5 text-purple-500"></i> {{ __('password') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input id="password" :type="passwordVisible ? 'text' : 'password'" name="password" autocomplete="new-password"
                                        placeholder="{{ __('enter_password') }}"
                                        class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5 pr-12"
                                        required @blur="onBlur('password')"
                                        @input="
                                            let v = $event.target.value;
                                            passwordValue = v;
                                            let s = 0;
                                            if (/[A-Z]/.test(v)) s++;
                                            if (/[a-z]/.test(v)) s++;
                                            if (/[0-9]/.test(v)) s++;
                                            if (/[@$!%*?&]/.test(v)) s++;
                                            if (v.length >= 8) s++;
                                            let levels = ['{{ __("weak") }}','{{ __("medium") }}','{{ __("good") }}','{{ __("strong") }}','{{ __("very_strong") }}'];
                                            let colors = ['text-red-400','text-yellow-400','text-green-400','text-green-500','text-green-600'];
                                            passwordStrength = v ? levels[s > 0 ? s - 1 : 0] : '';
                                            passwordStrengthColor = v ? colors[s > 0 ? s - 1 : 0] : '';
                                            onInput('password');
                                        "
                                        x-bind:class="fieldErrors.password ? 'ring-2 ring-red-400 bg-red-50' : ''" />
                                    <button type="button" @click="passwordVisible = !passwordVisible"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600 transition">
                                        <i class="fas" :class="passwordVisible ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <p x-show="passwordStrength" x-text="'{{ __("password_strength") }} ' + passwordStrength" :class="passwordStrengthColor" class="text-sm mt-2"></p>
                                {{-- Compact Password Requirements --}}
                                <div x-show="passwordValue" class="flex flex-wrap gap-1.5 mt-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold" :class="passwordValue.length >= 8 ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400'">
                                        <i class="fas" :class="passwordValue.length >= 8 ? 'fa-check' : 'fa-times'"></i> ≥8
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold" :class="/[a-z]/.test(passwordValue) ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400'">
                                        <i class="fas" :class="/[a-z]/.test(passwordValue) ? 'fa-check' : 'fa-times'"></i> abc
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold" :class="/[A-Z]/.test(passwordValue) ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400'">
                                        <i class="fas" :class="/[A-Z]/.test(passwordValue) ? 'fa-check' : 'fa-times'"></i> ABC
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold" :class="/[0-9]/.test(passwordValue) ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400'">
                                        <i class="fas" :class="/[0-9]/.test(passwordValue) ? 'fa-check' : 'fa-times'"></i> 123
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold" :class="/[@$!%*?&#]/.test(passwordValue) ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400'">
                                        <i class="fas" :class="/[@$!%*?&#]/.test(passwordValue) ? 'fa-check' : 'fa-times'"></i> @!#
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                <p x-show="fieldErrors.password" x-text="fieldErrors.password" class="text-sm text-red-600 mt-2"></p>
                            </div>

                            {{-- Password Confirmation --}}
                            <div>
                                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-shield-alt mr-1.5 text-purple-500"></i> {{ __('confirm_password') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input id="password_confirmation" :type="passwordConfirmVisible ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password"
                                        placeholder="{{ __('type_the_password_again') }}"
                                        class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5 pr-12"
                                        required @blur="onBlur('password_confirmation')" @input="onInput('password_confirmation')"
                                        x-bind:class="fieldErrors.password_confirmation ? 'ring-2 ring-red-400 bg-red-50' : ''" />
                                    <button type="button" @click="passwordConfirmVisible = !passwordConfirmVisible"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600 transition">
                                        <i class="fas" :class="passwordConfirmVisible ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                <p x-show="fieldErrors.password_confirmation" x-text="fieldErrors.password_confirmation" class="text-sm text-red-600 mt-2"></p>
                            </div>
                        </div>
                    </div>
                </div>
                </template>

                {{-- Section 2B: Student Info --}}
                <template x-if="userRole === 'student'" x-init="$nextTick(() => {
                    const departmentSelect = document.getElementById('student_department_id');
                    const degreeSelect = document.getElementById('degree_level');
                    const generationSelect = document.getElementById('generation');
                    const previewEl = document.getElementById('preview-student-id');
                    let previewTimer = null;

                    function fetchPreview() {
                        const departmentId = departmentSelect?.value;
                        const degreeLevel = degreeSelect?.value;
                        const generation = generationSelect?.value;
                        if (!departmentId || !degreeLevel || !generation) { previewEl.textContent = '—'; return; }
                        clearTimeout(previewTimer);
                        previewTimer = setTimeout(() => {
                            fetch('{{ route('admin.preview-student-id') }}?department_id=' + departmentId + '&degree_level=' + encodeURIComponent(degreeLevel) + '&generation=' + generation, {
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            })
                            .then(res => res.json())
                            .then(data => { if (data.student_id) previewEl.textContent = data.student_id; })
                            .catch(() => { previewEl.textContent = '—'; });
                        }, 300);
                    }

                    if (departmentSelect) departmentSelect.addEventListener('change', fetchPreview);
                    if (degreeSelect) degreeSelect.addEventListener('change', fetchPreview);
                    if (generationSelect) generationSelect.addEventListener('change', fetchPreview);
                })">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <span class="text-emerald-600 font-bold text-sm">2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ __('student_information') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('student_information') }}</p>
                            </div>
                        </div>

                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-5">
                            <p class="text-sm text-emerald-700 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                {{ __('student_id') }} <span id="preview-student-id" class="font-bold text-emerald-800 font-mono">—</span>
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label for="student_department_id" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-graduation-cap mr-1.5 text-emerald-500"></i> {{ __('department_3') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="student_department_id" name="department_id"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5"
                                    required @blur="onBlur('department_id')" @change="touched.department_id = true; validateField('department_id')"
                                    x-bind:class="fieldErrors.department_id ? 'ring-2 ring-red-400 bg-red-50' : ''">
                                    <option value="">{{ __('select_a_department_2') }}</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name_km }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                <p x-show="fieldErrors.department_id" x-text="fieldErrors.department_id" class="text-sm text-red-600 mt-2"></p>
                            </div>
                            <div>
                                <label for="degree_level" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-award mr-1.5 text-emerald-500"></i> {{ __('degree_level') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="degree_level" name="degree_level"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5"
                                    required @blur="onBlur('degree_level')" @change="touched.degree_level = true; validateField('degree_level')"
                                    x-bind:class="fieldErrors.degree_level ? 'ring-2 ring-red-400 bg-red-50' : ''">
                                    <option value="">{{ __('select_degree_level') }}</option>
                                    <option value="បរិញ្ញាបត្រ">{{ __('bachelor_s_degree') }}</option>
                                    <option value="បរិញ្ញាបត្ររង">{{ __('associate_degree') }}</option>
                                    <option value="វិញ្ញាបនបត្រ">{{ __('certificate') }}</option>
                                    <option value="ផ្សេងៗ">{{ __('other_2') }}</option>
                                </select>
                                <p x-show="fieldErrors.degree_level" x-text="fieldErrors.degree_level" class="text-sm text-red-600 mt-2"></p>
                            </div>
                            <div>
                                <label for="generation" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-layer-group mr-1.5 text-emerald-500"></i> {{ __('generation') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="generation" name="generation"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5"
                                    required @blur="onBlur('generation')" @change="touched.generation = true; validateField('generation')"
                                    x-bind:class="fieldErrors.generation ? 'ring-2 ring-red-400 bg-red-50' : ''">
                                    <option value="">{{ __('select_a_generation') }}</option>
                                    @foreach(\App\Models\Generation::where('is_active', true)->orderByDesc('name')->get() as $gen)
                                        <option value="{{ $gen->name }}">{{ $gen->name }} ({{ __('enrolled_in_year') }} {{ $gen->join_year }})</option>
                                    @endforeach
                                </select>
                                <p x-show="fieldErrors.generation" x-text="fieldErrors.generation" class="text-sm text-red-600 mt-2"></p>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Section 2C: Professor Info --}}
                <template x-if="userRole === 'professor'" x-init="$nextTick(() => {
                    const facultySelect = document.getElementById('faculty_id');
                    const departmentSelect = document.getElementById('professor_department_id');
                    const oldFacultyId = '{{ old('faculty_id') }}';
                    const oldDepartmentId = '{{ old('department_id') }}';

                    function updateDepartments(facultyId, defaultDepartmentId = null) {
                        if (!departmentSelect) return;
                        departmentSelect.innerHTML = '<option value=\"{{ __("loading_2") }}\"></option>';
                        departmentSelect.disabled = true;

                        if (!facultyId) {
                            departmentSelect.innerHTML = '<option value=\"{{ __("please_select_a_faculty_first") }}\"></option>';
                            return;
                        }

                        fetch('/admin/get-departments-by-faculty/' + facultyId)
                            .then(response => response.json())
                            .then(departments => {
                                departmentSelect.innerHTML = '<option value=\"{{ __("select_a_department_2") }}\"></option>';
                                departments.forEach(department => {
                                    const option = document.createElement('option');
                                    option.value = department.id;
                                    option.textContent = department.name_km || department.name_en;
                                    if (department.id == defaultDepartmentId) option.selected = true;
                                    departmentSelect.appendChild(option);
                                });
                                departmentSelect.disabled = false;
                            })
                            .catch(error => console.error('Error fetching departments:', error));
                    }

                    if (facultySelect) {
                        facultySelect.addEventListener('change', function() { updateDepartments(this.value); });
                        if (oldFacultyId) updateDepartments(oldFacultyId, oldDepartmentId);
                    }
                })">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <span class="text-emerald-600 font-bold text-sm">2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ __('lecturer_information') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('professor_information') }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="faculty_id" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-university mr-1.5 text-emerald-500"></i> {{ __('faculty') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="faculty_id" name="faculty_id"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5"
                                    @blur="onBlur('faculty_id')" @change="touched.faculty_id = true; validateField('faculty_id')"
                                    x-bind:class="fieldErrors.faculty_id ? 'ring-2 ring-red-400 bg-red-50' : ''">
                                    <option value="">{{ __('select_a_faculty') }}</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}">{{ $faculty->name_km ?? $faculty->name_en }}</option>
                                    @endforeach
                                </select>
                                <p x-show="fieldErrors.faculty_id" x-text="fieldErrors.faculty_id" class="text-sm text-red-600 mt-2"></p>
                            </div>
                            <div>
                                <label for="professor_department_id" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-building mr-1.5 text-emerald-500"></i> {{ __('department_3') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="professor_department_id" name="department_id"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5"
                                    @blur="onBlur('department_id')" @change="touched.department_id = true; validateField('department_id')"
                                    x-bind:class="fieldErrors.department_id ? 'ring-2 ring-red-400 bg-red-50' : ''">
                                    <option value="">{{ __('please_select_a_department') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                <p x-show="fieldErrors.department_id" x-text="fieldErrors.department_id" class="text-sm text-red-600 mt-2"></p>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Section 3: Profile Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                            <span class="text-orange-600 font-bold text-sm">3</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('personal_information') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('personal_information') }}</p>
                        </div>
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
                                                    showToast('{{ __("validation_file_max_size") }}', 'error');
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
                            <p class="text-xs text-gray-500 font-medium">{{ __('profile_picture_optional') }}</p>
                        </div>

                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="full_name_km" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-file-alt mr-1.5 text-orange-500"></i> {{ __('full_name_khmer') }}
                                </label>
                                <input id="full_name_km" type="text" name="full_name_km" value="{{ old('full_name_km') }}" placeholder="{{ __('enter_full_name_in_khmer') }}"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                            </div>
                            <div>
                                <label for="full_name_en" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-file-alt mr-1.5 text-orange-500"></i> {{ __('full_name_english') }}
                                </label>
                                <input id="full_name_en" type="text" name="full_name_en" value="{{ old('full_name_en') }}" placeholder="FULL NAME IN ENGLISH"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-venus-mars mr-1.5 text-orange-500"></i> {{ __('gender') }}
                                </label>
                                <select id="gender" name="gender"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5">
                                    <option value="">{{ __('select_gender') }}</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('male') }}</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('female') }}</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('other') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="phone_number" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-phone mr-1.5 text-orange-500"></i> {{ __('phone_number') }}
                                </label>
                                <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="012 345 678"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                            </div>
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-bold text-gray-700 mb-1.5">
                                    <i class="fas fa-map-marker-alt mr-1.5 text-orange-500"></i> {{ __('address') }}
                                </label>
                                <input id="address" type="text" name="address" value="{{ old('address') }}" placeholder="{{ __('enter_address') }}"
                                    class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm px-4 py-2.5" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.manage-users') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition text-sm">
                            <i class="fas fa-times"></i> {{ __('cancel_2') }}
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-600 rounded-xl font-bold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all shadow-lg shadow-emerald-200 text-sm">
                            <i class="fas fa-save"></i> {{ __('save_and_create_user') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
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
