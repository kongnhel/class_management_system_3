<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-4xl text-gray-900 leading-tight tracking-wide flex items-center">
            <i class="fas fa-user-edit mr-3 text-green-600"></i> {{ __('កែប្រែអ្នកប្រើប្រាស់') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-b from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-10">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 sm:p-10 lg:p-12 border border-gray-200">
                <h3 class="text-3xl font-extrabold text-green-700 mb-8 text-center">
                    {{ __('កែប្រែព័ត៌មានអ្នកប្រើប្រាស់') . ': ' . ($user->name ?? 'N/A') }}
                </h3>

<form method="POST" action="{{ route('admin.update-user', $user->id) }}" enctype="multipart/form-data"
    x-data="{
        userRole: '{{ old('role', $user->role) }}',
        profilePicturePreview: '{{ $user->profile?->profile_picture_url ?? $user->studentProfile?->profile_picture_url ?? '' }}'
    }" 
    class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div class="mb-6">
                            <x-input-label for="name" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
                                <i class="fas fa-user-alt mr-3 text-green-500"></i> {{ __('ឈ្មោះអ្នកប្រើប្រាស់') }}
                            </x-input-label>
                            <x-text-input id="name" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 py-3 px-4" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="role" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
                                <i class="fas fa-user-tag mr-3 text-green-500"></i> {{ __('តួនាទី') }}
                            </x-input-label>
                            <select id="role" name="role" x-model="userRole" class="block w-full rounded-xl border-gray-300 focus:border-green-600 focus:ring-green-600 shadow-sm py-3 px-4" required>
                                <option value="admin">{{ __('Admin') }}</option>
                                <option value="professor">{{ __('Professor') }}</option>
                                <option value="student">{{ __('Student') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                    </div>

                    <div class="border-t border-gray-200/50 pt-8 mt-8">
                        
<div x-show="userRole === 'admin' || userRole === 'professor'" x-cloak class="space-y-6">
    <h4 class="text-2xl font-bold text-gray-800 mb-4">{{ __('ព័ត៌មានគណនី') }}</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
    <x-input-label for="email" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
        <i class="fas fa-envelope mr-3 text-green-500"></i> {{ __('អ៊ីម៉ែល') }}
    </x-input-label>
    {{-- បន្ថែម :required អាស្រ័យលើ userRole --}}
    <x-text-input id="email" 
        class="block w-full rounded-xl py-3 px-4" 
        type="email" 
        name="email" 
        :value="old('email', $user->email)" 
        ::required="userRole !== 'student'" /> {{-- បន្ថែមបន្ទាត់នេះ --}}
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

    <!-- New Password -->
    <div>
        <x-input-label for="password" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
            <i class="fas fa-lock mr-3 text-green-500"></i>
            {{ __('ពាក្យសម្ងាត់ថ្មី') }}
        </x-input-label>

        <div class="relative">
            <x-text-input
                id="password"
                type="password"
                name="password"
                autocomplete="new-password"
                placeholder="ទុកឱ្យនៅទទេប្រសិនបើមិនប្តូរ"
                class="block w-full rounded-xl py-3 px-4 pr-12"
            />

            <!-- Toggle Password -->
            <button
                type="button"
                id="togglePassword"
                class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600 transition"
            >
                <i class="fas fa-eye"></i>
            </button>
        </div>

        <!-- Password Strength -->
        <p id="password-strength" class="text-sm mt-2"></p>

        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <!-- Confirm New Password -->
    <div>
        <x-input-label for="password_confirmation" class="flex items-center text-lg text-gray-700 font-semibold mb-2">
            <i class="fas fa-shield-alt mr-3 text-green-500"></i>
            {{ __('បញ្ជាក់ពាក្យសម្ងាត់ថ្មី') }}
        </x-input-label>

        <div class="relative">
            <x-text-input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="វាយពាក្យសម្ងាត់ម្តងទៀត"
                class="block w-full rounded-xl py-3 px-4 pr-12"
            />

            <!-- Toggle Password Confirm -->
            <button
                type="button"
                id="togglePasswordConfirm"
                class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-gray-600 transition"
            >
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>


    </div>
</div>

                        <div x-show="userRole === 'student'" x-cloak class="space-y-6 mt-6">
                            <h4 class="text-2xl font-bold text-gray-800 mb-4">{{ __('ព័ត៌មាននិស្សិត') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="student_id_code" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-id-card mr-3 text-green-500"></i> {{ __('លេខកូដអត្តសញ្ញាណសិស្ស') }}</x-input-label>
                                    <div class="block w-full rounded-xl py-3 px-4 bg-gray-100 border border-gray-200 text-gray-600 font-mono font-bold">
                                        {{ $user->student_id_code ?? __('មិនទាន់បង្កើត') }}
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">{{ __('លេខសម្គាល់នេះត្រូវបានបង្កើតដោយស្វ័យប្រវត្តិ ហើយមិនអាចកែប្រែបានទេ។') }}</p>
                                </div>
                                <div>
                                    <x-input-label for="program_id" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-graduation-cap mr-3 text-green-500"></i> {{ __('កម្មវិធីសិក្សា') }}</x-input-label>
                                    <select id="program_id" name="program_id" class="block w-full rounded-xl py-3 px-4">
                                        <option value="">{{ __('ជ្រើសរើសកម្មវិធីសិក្សា') }}</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ old('program_id', $user->program_id) == $program->id ? 'selected' : '' }}>
                                                {{ $program->name_km ?? $program->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="generation" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-layer-group mr-3 text-green-500"></i> {{ __('ជំនាន់') }}</x-input-label>
                                    <select name="generation" id="generation" class="block w-full rounded-xl border-gray-300 py-3 px-4">
                                        <option value="">{{ __('ជ្រើសរើសជំនាន់') }}</option>
                                        @foreach ($generations as $generation)
                                            <option value="{{ $generation }}" {{ old('generation', $user->generation) == $generation ? 'selected' : '' }}>
                                                {{ $generation }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('generation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div x-show="userRole === 'professor'" x-cloak class="space-y-6 mt-6">
                            <h4 class="text-2xl font-bold text-gray-800 mb-4">{{ __('ព័ត៌មានសាស្ត្រាចារ្យ') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="faculty_id" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-university mr-3 text-green-500"></i> {{ __('មហាវិទ្យាល័យ') }}</x-input-label>
                                    <select id="faculty_id" name="faculty_id" class="block w-full rounded-xl py-3 px-4">
                                        <option value="">{{ __('ជ្រើសរើសមហាវិទ្យាល័យ') }}</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" {{ (old('faculty_id', $user->department?->faculty_id ?? '')) == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->name_km ?? $faculty->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="department_id" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-building mr-3 text-green-500"></i> {{ __('ដេប៉ាតឺម៉ង់') }}</x-input-label>
                                    <select id="department_id" name="department_id" class="block w-full rounded-xl py-3 px-4">
                                        <option value="">{{ __('សូមជ្រើសរើសដេប៉ាតឺម៉ង់') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200/50 pt-8 mt-8">
                        <h4 class="text-2xl font-bold text-gray-800 mb-6">{{ __('ព័ត៌មាន Profile') }}</h4>
                        
<div class="flex flex-col items-center space-y-4" x-data="{ profilePicturePreview: '{{ $userProfile->profile_picture_url ?? '' }}' }">
    <div class="relative group">
        <div class="h-32 w-32 rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative">
            <template x-if="profilePicturePreview">
                <img :src="profilePicturePreview.includes('ik.imagekit.io') ? profilePicturePreview + '?tr=w-300,h-300,fo-face' : profilePicturePreview" 
                     class="h-full w-full object-cover">
            </template>
            
            <template x-if="!profilePicturePreview">
                <i class="fas fa-camera text-3xl text-gray-300"></i>
            </template>
        </div>

        <label class="absolute -bottom-2 -right-2 bg-green-600 text-white p-2 rounded-lg cursor-pointer hover:bg-green-700 shadow-lg transition">
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
    <p class="text-xs text-gray-500">{{ __('រូបភាព Profile (4x6)') }}</p>
</div>
                            <div class="mt-4">
                                 <label for="remove_profile_picture" class="flex items-center">
                                    <x-checkbox id="remove_profile_picture" name="remove_profile_picture" value="1" />
                                    <span class="ms-2 text-sm text-gray-600">{{ __('លុបរូបភាព Profile ដែលមានស្រាប់') }}</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="full_name_km" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-file-alt mr-3 text-green-500"></i> {{ __('ឈ្មោះពេញ (ខ្មែរ)') }}</x-input-label>
                                <x-text-input id="full_name_km" class="block w-full rounded-xl py-3 px-4" type="text" name="full_name_km" :value="old('full_name_km', $user->profile?->full_name_km ?? $user->studentProfile?->full_name_km ?? '')" />
                                <x-input-error :messages="$errors->get('full_name_km')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="full_name_en" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-file-alt mr-3 text-green-500"></i> {{ __('ឈ្មោះពេញ (អង់គ្លេស)') }}</x-input-label>
                                <x-text-input id="full_name_en" class="block w-full rounded-xl py-3 px-4" type="text" name="full_name_en" :value="old('full_name_en', $user->profile?->full_name_en ?? $user->studentProfile?->full_name_en ?? '')" />
                                <x-input-error :messages="$errors->get('full_name_en')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="gender" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-venus-mars mr-3 text-green-500"></i> {{ __('ភេទ') }}</x-input-label>
                                <select id="gender" name="gender" class="block w-full rounded-xl py-3 px-4">
                                    <option value="">{{ __('ជ្រើសរើសភេទ') }}</option>
                                    <option value="male" {{ old('gender', $user->profile?->gender ?? $user->studentProfile?->gender ?? '') == 'male' ? 'selected' : '' }}>{{ __('ប្រុស') }}</option>
                                    <option value="female" {{ old('gender', $user->profile?->gender ?? $user->studentProfile?->gender ?? '') == 'female' ? 'selected' : '' }}>{{ __('ស្រី') }}</option>
                                    <option value="other" {{ old('gender', $user->profile?->gender ?? $user->studentProfile?->gender ?? '') == 'other' ? 'selected' : '' }}>{{ __('ផ្សេងទៀត') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="date_of_birth" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-calendar-alt mr-3 text-green-500"></i> {{ __('ថ្ងៃខែឆ្នាំកំណើត') }}</x-input-label>
                                <x-text-input id="date_of_birth" class="block w-full rounded-xl py-3 px-4" type="date" name="date_of_birth" :value="old('date_of_birth', $user->profile?->date_of_birth ?? $user->studentProfile?->date_of_birth ?? '')" />
                                <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone_number" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-phone mr-3 text-green-500"></i> {{ __('លេខទូរស័ព្ទ') }}</x-input-label>
                                <x-text-input id="phone_number" class="block w-full rounded-xl py-3 px-4" type="text" name="phone_number" :value="old('phone_number', $user->profile?->phone_number ?? $user->studentProfile?->phone_number ?? '')" />
                                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="address" class="flex items-center text-lg text-gray-700 font-semibold mb-2"><i class="fas fa-map-marker-alt mr-3 text-green-500"></i> {{ __('អាសយដ្ឋាន') }}</x-input-label>
                                <x-text-input id="address" class="block w-full rounded-xl py-3 px-4" type="text" name="address" :value="old('address', $user->profile?->address ?? $user->studentProfile?->address ?? '')" />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-10">
                        <x-primary-button class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-600 border border-transparent rounded-full font-bold text-base text-white hover:from-green-700 hover:to-green-700">
                            <i class="fas fa-save mr-3 text-lg"></i> {{ __('រក្សាទុកការកែប្រែ') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script for dependent dropdowns -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const facultySelect = document.getElementById('faculty_id');
            const departmentSelect = document.getElementById('department_id');
            const selectedDepartmentId = {{ old('department_id', $user->department_id) ?? 'null' }};

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

                const initialFacultyId = facultySelect.value;
                if (initialFacultyId) {
                    updateDepartments(initialFacultyId, selectedDepartmentId);
                }
            }
        });






          document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Toggle Password Visibility Logic ---
        function togglePassword(inputId, buttonId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);
            
            if (input && button) {
                button.addEventListener('click', function() {
                    const type = input.type === 'password' ? 'text' : 'password';
                    input.type = type;
                    const icon = this.querySelector('i');
                    // Toggle the eye icon between 'fa-eye' (show) and 'fa-eye-slash' (hide)
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
        }

        // Apply toggle to both password fields
        togglePassword('password', 'togglePassword');
        togglePassword('password_confirmation', 'togglePasswordConfirm');

        // --- 2. Password Strength Checker Logic ---
        const passwordInput = document.getElementById('password');
        const strengthText = document.getElementById('password-strength');

        if (passwordInput && strengthText) {
            passwordInput.addEventListener('input', () => {
                const value = passwordInput.value;
                let strength = 0;
                // Criteria checks
                if (/[A-Z]/.test(value)) strength++;       // Uppercase
                if (/[a-z]/.test(value)) strength++;       // Lowercase
                if (/[0-9]/.test(value)) strength++;       // Numbers
                if (/[@$!%*?&]/.test(value)) strength++;   // Symbols
                if (value.length >= 8) strength++;         // Length

                const levels = ['ខ្សោយ', 'មធ្យម', 'ល្អ', 'ខ្លាំង', 'ខ្លាំងណាស់'];
                const colors = ['text-red-400', 'text-yellow-400', 'text-green-400', 'text-green-500', 'text-green-600'];
                
                // Reset classes before setting the new one
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

