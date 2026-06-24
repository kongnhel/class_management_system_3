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
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 text-blue-600">
                            <i class="fas fa-user"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('ព័ត៌មានមូលដ្ឋាន') }}</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="name" class="font-semibold text-gray-700 mb-1.5">
                                {{ __('ឈ្មោះអ្នកប្រើប្រាស់ (Username)') }}
                            </x-input-label>
                            <x-text-input id="name" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition" type="text" name="name" :value="old('name')" placeholder="បញ្ចូលឈ្មោះអ្នកប្រើប្រាស់" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="role" class="font-semibold text-gray-700 mb-1.5">
                                {{ __('តួនាទីក្នុងប្រព័ន្ធ') }}
                            </x-input-label>
                            <select id="role" name="role" x-model="userRole" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition">
                                <option value="admin">Admin</option>
                                <option value="professor">Professor</option>
                                <option value="student">Student</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Section 2: Account Info (Admin/Professor) --}}
                <div x-show="userRole === 'admin' || userRole === 'professor'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-100 text-purple-600">
                            <i class="fas fa-key"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('ព័ត៌មានគណនី') }}</h3>
                    </div>
                    
                    <div class="space-y-5">
                        <div>
                            <x-input-label for="email" class="font-semibold text-gray-700 mb-1.5">{{ __('អាសយដ្ឋានអ៊ីម៉ែល') }}</x-input-label>
                            <x-text-input id="email" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition" type="email" name="email" :value="old('email')" placeholder="example@email.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <x-input-label for="password" class="font-semibold text-gray-700 mb-1.5">
                                    {{ __('ពាក្យសម្ងាត់') }}
                                </x-input-label>
                                <div class="relative">
                                    <x-text-input
                                        id="password"
                                        type="password"
                                        name="password"
                                        autocomplete="new-password"
                                        class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm pr-12 transition"
                                        placeholder="បញ្ចូលពាក្យសម្ងាត់"
                                    />
                                    <button
                                        type="button"
                                        id="togglePassword"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600 transition"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <p id="password-strength" class="text-sm mt-2"></p>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" class="font-semibold text-gray-700 mb-1.5">
                                    {{ __('បញ្ជាក់ពាក្យសម្ងាត់') }}
                                </x-input-label>
                                <div class="relative">
                                    <x-text-input
                                        id="password_confirmation"
                                        type="password"
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                        class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm pr-12 transition"
                                        placeholder="បញ្ជាក់ពាក្យសម្ងាត់ម្តងទៀត"
                                    />
                                    <button
                                        type="button"
                                        id="togglePasswordConfirm"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600 transition"
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
                <div x-show="userRole === 'student'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600">
                            <i class="fas fa-graduation-cap"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('ព័ត៌មាននិស្សិត') }}</h3>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-5">
                        <p class="text-sm text-blue-700 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            {{ __('លេខសម្គាល់និស្សិតនឹងត្រូវបានបង្កើតដោយស្វ័យប្រវត្តិ។ ទម្រង់៖ [Prefix]-[Generation]-[Serial]') }}
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="program_id" class="font-semibold text-gray-700 mb-1.5">{{ __('កម្មវិធីសិក្សា') }}</x-input-label>
                            <select id="program_id" name="program_id" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                                <option value="">{{ __('ជ្រើសរើសកម្មវិធីសិក្សា') }}</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name_km }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="generation" class="font-semibold text-gray-700 mb-1.5">{{ __('ជំនាន់') }}</x-input-label>
                            <x-text-input id="generation" name="generation" type="number" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition" placeholder="ឧ. ១៦" />
                        </div>
                    </div>
                </div>
                
                {{-- Section 4: Professor Info --}}
                <div x-show="userRole === 'professor'" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('ព័ត៌មានសាស្ត្រាចារ្យ') }}</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="faculty_id" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-university mr-1.5 text-emerald-500"></i> {{ __('មហាវិទ្យាល័យ') }}
                            </x-input-label>
                            <select id="faculty_id" name="faculty_id" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white py-2.5 px-4 transition">
                                <option value="">{{ __('ជ្រើសរើសមហាវិទ្យាល័យ') }}</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ (old('faculty_id', $user->department?->faculty_id ?? '')) == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name_km ?? $faculty->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="department_id" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-building mr-1.5 text-emerald-500"></i> {{ __('ដេប៉ាតឺម៉ង់') }}
                            </x-input-label>
                            <select id="department_id" name="department_id" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white py-2.5 px-4 transition">
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
                                <div class="h-40 w-32 rounded-2xl bg-gray-50 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative shadow-inner group-hover:border-blue-400 transition">
                                    <template x-if="profilePicturePreview">
                                        <img :src="profilePicturePreview.includes('ik.imagekit.io') ? profilePicturePreview + '?tr=w-300,h-400,fo-face' : profilePicturePreview" 
                                             class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!profilePicturePreview">
                                        <div class="text-center">
                                            <i class="fas fa-camera text-3xl text-gray-300"></i>
                                            <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold">Upload</p>
                                        </div>
                                    </template>
                                </div>
                                <label class="absolute -bottom-2 -right-2 bg-blue-600 text-white p-2.5 rounded-xl cursor-pointer hover:bg-blue-700 shadow-lg transition-all hover:scale-110 active:scale-95">
                                    <i class="fas fa-pen text-xs"></i>
                                    <input type="file" name="profile_picture" class="hidden" 
                                        @change="
                                            const file = $event.target.files[0];
                                            if (file) {
                                                if (file.size > 2 * 1024 * 1024) {
                                                    alert('រូបភាពធំពេក! សូមជ្រើសរើសរូបភាពដែលមានទំហំតូចជាង ២MB');
                                                    $event.target.value = '';
                                                    profilePicturePreview = '{{ $userProfile->profile_picture_url ?? '' }}';
                                                } else {
                                                    profilePicturePreview = URL.createObjectURL(file);
                                                }
                                            }
                                        ">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 font-medium">{{ __('រូបភាព Profile (4x6)') }}</p>
                        </div>

                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <x-input-label for="full_name_km" class="font-semibold text-gray-700 mb-1.5">{{ __('ឈ្មោះពេញ (ខ្មែរ)') }}</x-input-label>
                                <x-text-input id="full_name_km" name="full_name_km" value="{{ old('full_name_km', $userProfile->full_name_km ?? '') }}" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition" placeholder="បញ្ចូលឈ្មោះពេញជាភាសាខ្មែរ" />
                            </div>
                            <div>
                                <x-input-label for="full_name_en" class="font-semibold text-gray-700 mb-1.5">{{ __('ឈ្មោះពេញ (អង់គ្លេស)') }}</x-input-label>
                                <x-text-input id="full_name_en" name="full_name_en" value="{{ old('full_name_en', $userProfile->full_name_en ?? '') }}" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white uppercase transition" placeholder="FULL NAME IN ENGLISH" />
                            </div>
                            <div>
                                <x-input-label for="gender" class="font-semibold text-gray-700 mb-1.5">{{ __('ភេទ') }}</x-input-label>
                                <select id="gender" name="gender" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white shadow-sm transition">
                                    <option value="">{{ __('ជ្រើសរើសភេទ') }}</option>
                                    <option value="male" {{ (old('gender', $userProfile->gender ?? '') == 'male') ? 'selected' : '' }}>{{ __('ប្រុស') }}</option>
                                    <option value="female" {{ (old('gender', $userProfile->gender ?? '') == 'female') ? 'selected' : '' }}>{{ __('ស្រី') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="phone_number" class="font-semibold text-gray-700 mb-1.5">{{ __('លេខទូរស័ព្ទ') }}</x-input-label>
                                <x-text-input id="phone_number" name="phone_number" value="{{ old('phone_number', $userProfile->phone_number ?? '') }}" class="block w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition" placeholder="012 345 678" />
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
    </script>
</x-app-layout>
