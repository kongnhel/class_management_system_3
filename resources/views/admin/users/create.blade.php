<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center px-4 md:px-0">
            <h2 class="text-3xl font-bold text-gray-900 leading-tight">
                {{ __('បង្កើតអ្នកប្រើប្រាស់ថ្មី') }} <i class="fas fa-user-plus text-green-600 ml-3"></i>
            </h2>
            <a href="{{ route('admin.manage-users') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-full font-semibold text-sm text-gray-700 shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> {{ __('ត្រឡប់ទៅបញ្ជីវិញ') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="p-8 sm:p-10">
                    <h3 class="text-2xl font-extrabold text-gray-800 mb-8 flex items-center">
                        <span class="bg-green-100 text-green-600 p-2 rounded-lg mr-4">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        {{ __('ព័ត៌មានលម្អិតអ្នកប្រើប្រាស់ថ្មី') }}
                    </h3>

                    <form method="POST" action="{{ route('admin.store-user') }}" enctype="multipart/form-data"
                        x-data="{ 
                            userRole: '{{ old('role', 'professor') }}',
                            profilePicturePreview: null
                        }" class="space-y-8">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" class="font-bold text-gray-700 mb-2">
                                    {{ __('ឈ្មោះអ្នកប្រើប្រាស់ (Username)') }}
                                </x-input-label>
                                <x-text-input id="name" class="block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="role" class="font-bold text-gray-700 mb-2">
                                    {{ __('តួនាទីក្នុងប្រព័ន្ធ') }}
                                </x-input-label>
                                <select id="role" name="role" x-model="userRole" class="block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                                    <option value="admin">Admin</option>
                                    <option value="professor">Professor</option>
                                    <option value="student">Student</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>
                        </div>

                        <div x-show="userRole === 'admin' || userRole === 'professor'" x-transition class="bg-gray-50 p-6 rounded-2xl border border-gray-100 space-y-6">
                            <h4 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-key mr-2 text-green-600"></i> {{ __('ព័ត៌មានគណនី') }}
                            </h4>
                            
                            <div>
                                <x-input-label for="email" class="font-semibold text-gray-600 mb-1">{{ __('អាសយដ្ឋានអ៊ីម៉ែល') }}</x-input-label>
                                <x-text-input id="email" class="block w-full rounded-xl border-gray-300 shadow-sm" type="email" name="email" :value="old('email')" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Password -->
    <div>
        <x-input-label for="password" class="font-semibold text-gray-600 mb-1">
            {{ __('ពាក្យសម្ងាត់') }}
        </x-input-label>

        <div class="relative">
            <x-text-input
                id="password"
                type="password"
                name="password"
                autocomplete="new-password"
                class="block w-full rounded-xl border-gray-300 shadow-sm pr-12"
            />

            <!-- Toggle Button -->
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

    <!-- Password Confirmation -->
    <div>
        <x-input-label for="password_confirmation" class="font-semibold text-gray-600 mb-1">
            {{ __('បញ្ជាក់ពាក្យសម្ងាត់') }}
        </x-input-label>

        <div class="relative">
            <x-text-input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                class="block w-full rounded-xl border-gray-300 shadow-sm pr-12"
            />

            <!-- Toggle Button -->
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

                        <div x-show="userRole === 'student'" x-transition class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100 space-y-6">
                            <h4 class="text-lg font-bold text-blue-800 flex items-center">
                                <i class="fas fa-graduation-cap mr-2"></i> {{ __('ព័ត៌មាននិស្សិត') }}
                            </h4>
                            <div class="bg-blue-100/50 border border-blue-200 rounded-xl p-3 mb-4">
                                <p class="text-sm text-blue-700 flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    {{ __('លេខសម្គាល់និស្សិតនឹងត្រូវបានបង្កើតដោយស្វ័យប្រវត្តិ។ ទម្រង់៖ [Prefix]-[Generation]-[Serial]') }}
                                </p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="program_id" class="font-semibold text-blue-700 mb-1">{{ __('កម្មវិធីសិក្សា') }}</x-input-label>
                                    <select id="program_id" name="program_id" class="block w-full rounded-xl border-blue-200" required>
                                        <option value="">{{ __('ជ្រើសរើស') }}</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}">{{ $program->name_km }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="generation" class="font-semibold text-blue-700 mb-1">{{ __('ជំនាន់') }}</x-input-label>
                                    <x-text-input id="generation" name="generation" type="number" class="block w-full rounded-xl border-blue-200" placeholder="16" required />
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

                        <div class="border-t border-gray-100 pt-8">
                            <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-user-circle mr-2 text-green-600"></i> {{ __('ព័ត៌មានផ្ទាល់ខ្លួន') }}
                            </h4>

                           <div class="flex flex-col md:flex-row gap-10" x-data="{ profilePicturePreview: '{{ $userProfile->profile_picture_url ?? '' }}' }">
    <div class="flex flex-col items-center space-y-4">
        <div class="relative group">
            <div class="h-40 w-32 rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative shadow-inner">
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

            <label class="absolute -bottom-2 -right-2 bg-green-600 text-white p-2.5 rounded-xl cursor-pointer hover:bg-green-700 shadow-lg transition-all hover:scale-110 active:scale-95">
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

    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-1">
            <x-input-label for="full_name_km" class="font-bold text-gray-700 ml-1">{{ __('ឈ្មោះពេញ (ខ្មែរ)') }}</x-input-label>
            <x-text-input id="full_name_km" name="full_name_km" value="{{ old('full_name_km', $userProfile->full_name_km ?? '') }}" class="block w-full rounded-xl border-gray-300 focus:ring-green-500" placeholder="បញ្ចូលឈ្មោះខ្មែរ" />
        </div>
        
        <div class="space-y-1">
            <x-input-label for="full_name_en" class="font-bold text-gray-700 ml-1">{{ __('ឈ្មោះពេញ (អង់គ្លេស)') }}</x-input-label>
            <x-text-input id="full_name_en" name="full_name_en" value="{{ old('full_name_en', $userProfile->full_name_en ?? '') }}" class="block w-full rounded-xl border-gray-300 focus:ring-green-500 uppercase" placeholder="FULL NAME IN ENGLISH" />
        </div>

        <div class="space-y-1">
            <x-input-label for="gender" class="font-bold text-gray-700 ml-1">{{ __('ភេទ') }}</x-input-label>
            <select id="gender" name="gender" class="block w-full rounded-xl mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm">
                <option value="male" {{ (old('gender', $userProfile->gender ?? '') == 'male') ? 'selected' : '' }}>{{ __('ប្រុស') }}</option>
                <option value="female" {{ (old('gender', $userProfile->gender ?? '') == 'female') ? 'selected' : '' }}>{{ __('ស្រី') }}</option>
            </select>
        </div>

        <div class="space-y-1">
            <x-input-label for="phone_number" class="font-bold text-gray-700 ml-1">{{ __('លេខទូរស័ព្ទ') }}</x-input-label>
            <x-text-input id="phone_number" name="phone_number" value="{{ old('phone_number', $userProfile->phone_number ?? '') }}" class="block w-full rounded-xl mt-1 border-gray-300 focus:ring-green-500" placeholder="012 345 678" />
        </div>
    </div>
</div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-4 bg-green-600 border border-transparent rounded-2xl font-bold text-lg text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-500/30 transition-all shadow-lg shadow-green-200">
                                <i class="fas fa-save mr-2"></i> {{ __('រក្សាទុក និងបង្កើតអ្នកប្រើប្រាស់') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

