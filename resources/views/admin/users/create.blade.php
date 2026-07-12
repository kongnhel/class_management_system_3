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
                    profilePicturePreview: null
                }" class="space-y-6">
                @csrf

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
                            <x-text-input id="name" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="name" :value="old('name')" placeholder="បញ្ចូលឈ្មោះអ្នកប្រើប្រាស់" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="role" class="font-semibold text-gray-700 mb-1.5">
                                {{ __('តួនាទី') }}
                            </x-input-label>
                            <select id="role" name="role" x-model="userRole" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]" required>
                                <option value="">{{ __('ជ្រើសរើសតួនាទី') }}</option>
                                <option value="admin">Admin</option>
                                <option value="professor">Professor</option>
                                <option value="student">Student</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
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
                            <x-text-input id="email" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="email" name="email" :value="old('email')" placeholder="example@gmail.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
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
                                        type="password"
                                        name="password"
                                        autocomplete="new-password"
                                        placeholder="បញ្ចូលពាក្យសម្ងាត់"
                                        class="block w-full h-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 pr-12 shadow-sm text-gray-900 bg-white transition"
                                        required
                                    />
                                    <button
                                        type="button"
                                        id="togglePassword"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600 transition h-full"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <p id="password-strength" class="text-sm mt-2"></p>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
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
                                        type="password"
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                        placeholder="វាយពាក្យសម្ងាត់ម្តងទៀត"
                                        class="block w-full h-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 pr-12 shadow-sm text-gray-900 bg-white transition"
                                        required
                                    />
                                    <button
                                        type="button"
                                        id="togglePasswordConfirm"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600 transition h-full"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
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
                            <select id="program_id" name="program_id" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]" required>
                                <option value="">{{ __('ជ្រើសរើសកម្មវិធីសិក្សា') }}</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name_km }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="degree_level" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-award mr-1.5 text-emerald-500"></i> {{ __('កម្រិតសញ្ញាបត្រ') }}
                            </x-input-label>
                            <select id="degree_level" name="degree_level" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]" required>
                                <option value="">{{ __('ជ្រើសរើសកម្រិតសញ្ញាបត្រ') }}</option>
                                <option value="បរិញ្ញាបត្រ">បរិញ្ញាបត្រ</option>
                                <option value="បរិញ្ញាបត្ររង">បរិញ្ញាបត្ររង</option>
                                <option value="អនុបណ្ឌិត">អនុបណ្ឌិត</option>
                                <option value="បណ្ឌិត">បណ្ឌិត</option>
                                <option value="វិញ្ញាបនបត្រ">វិញ្ញាបនបត្រ</option>
                                <option value="ផ្សេងៗ">ផ្សេងៗ</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="generation" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-layer-group mr-1.5 text-emerald-500"></i> {{ __('ជំនាន់') }}
                            </x-input-label>
                            <select id="generation" name="generation" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]" required>
                                <option value="">{{ __('ជ្រើសរើសជំនាន់') }}</option>
                                @foreach(\App\Models\Generation::orderByDesc('name')->get() as $gen)
                                    <option value="{{ $gen->name }}">{{ $gen->name }} (ចូលរៀនឆ្នាំ {{ $gen->join_year }})</option>
                                @endforeach
                            </select>
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
                            <select id="faculty_id" name="faculty_id" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]">
                                <option value="">{{ __('ជ្រើសរើសមហាវិទ្យាល័យ') }}</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}">{{ $faculty->name_km ?? $faculty->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="department_id" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-building mr-1.5 text-emerald-500"></i> {{ __('ដេប៉ាតឺម៉ង់') }}
                            </x-input-label>
                            <select id="department_id" name="department_id" class="block w-full border-gray-200 bg-white text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm transition py-2.5 px-4 h-[50px]">
                                <option value="">{{ __('សូមជ្រើសរើសដេប៉ាតឺម៉ង់') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
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
                                    <input type="file" name="profile_picture" class="hidden" 
                                        @change="
                                            const file = $event.target.files[0];
                                            if (file) {
                                                if (file.size > 2 * 1024 * 1024) {
                                                    showToast('រូបភាពធំពេក! សូមជ្រើសរើសរូបភាពដែលមានទំហំតូចជាង ២MB', 'error');
                                                    $event.target.value = '';
                                                    profilePicturePreview = '';
                                                } else {
                                                    profilePicturePreview = URL.createObjectURL(file);
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
                                <x-text-input id="full_name_km" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="full_name_km" :value="old('full_name_km')" placeholder="បញ្ចូលឈ្មោះពេញជាភាសាខ្មែរ" />
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
                                <x-text-input id="address" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="address" :value="old('address')" placeholder="បញ្ចូលអាសយដ្ឋាន" />
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

            // Toggle Password Visibility
            function togglePassword(inputId, buttonId) {
                const input = document.getElementById(inputId);
                const button = document.getElementById(buttonId);
                
                if (input && button) {
                    button.addEventListener('click', function() {
                        const type = input.type === 'password' ? 'text' : 'password';
                        input.type = type;
                        const icon = this.querySelector('i');
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    });
                }
            }

            togglePassword('password', 'togglePassword');
            togglePassword('password_confirmation', 'togglePasswordConfirm');

            // Password Strength Checker
            const passwordInput = document.getElementById('password');
            const strengthText = document.getElementById('password-strength');

            if (passwordInput && strengthText) {
                passwordInput.addEventListener('input', () => {
                    const value = passwordInput.value;
                    let strength = 0;
                    if (/[A-Z]/.test(value)) strength++;
                    if (/[a-z]/.test(value)) strength++;
                    if (/[0-9]/.test(value)) strength++;
                    if (/[@$!%*?&]/.test(value)) strength++;
                    if (value.length >= 8) strength++;

                    const levels = ['ខ្សោយ', 'មធ្យម', 'ល្អ', 'ខ្លាំង', 'ខ្លាំងណាស់'];
                    const colors = ['text-red-400', 'text-yellow-400', 'text-green-400', 'text-green-500', 'text-green-600'];
                    
                    strengthText.className = 'text-sm mt-2'; 
                    
                    if (value) {
                        const levelIndex = strength > 0 ? strength - 1 : 0;
                        strengthText.textContent = 'កម្លាំងពាក្យសម្ងាត់៖ ' + levels[levelIndex];
                        strengthText.classList.add(colors[levelIndex]);
                    } else {
                        strengthText.textContent = '';
                    }
                });
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
    </script>
</x-app-layout>