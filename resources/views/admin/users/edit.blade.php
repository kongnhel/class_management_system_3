<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.manage-users') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 leading-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-amber-100 text-amber-600">
                        <i class="fas fa-user-edit"></i>
                    </span>
                    {{ __('edit_user') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.update-user', $user->id) }}" enctype="multipart/form-data"
                x-data="{
                    userRole: '{{ old('role', $user->role) }}',
                    profilePicturePreview: '{{ $user->profile?->profile_picture_url ?? $user->studentProfile?->profile_picture_url ?? '' }}'
                }" 
                class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" id="profile_picture_base64" name="profile_picture_base64" value="" />

                {{-- Section 1: Basic Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fas fa-user"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('basic_information') }}</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                        <div>
                            <x-input-label for="name" class="font-semibold text-gray-700 mb-1.5">
                                {{ __('user_name') }}
                            </x-input-label>
                            <x-text-input id="name" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 shadow-sm py-2.5 px-4 h-[50px]" type="text" name="name" :value="old('name', $user->name)" placeholder="{{ __('enter_username') }}" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="role" class="font-semibold text-gray-700 mb-1.5">
                                {{ __('role') }}
                            </x-input-label>
                            <select id="role" name="role" x-model="userRole" class="block w-full rounded-xl border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500 shadow-sm py-2.5 px-4 h-[50px]" required>
                                <option value="">{{ __('select_a_role') }}</option>
                                <option value="admin">{{ __('Admin') }}</option>
                                <option value="professor">{{ __('Professor') }}</option>
                                <option value="student">{{ __('Student') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Section 2: Account Info (Admin/Professor) --}}
                <div x-show="userRole === 'admin' || userRole === 'professor'" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-100 text-purple-600">
                            <i class="fas fa-key"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('account_information') }}</h3>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <x-input-label for="email" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-envelope mr-1.5 text-purple-500"></i> {{ __('email_2') }}
                            </x-input-label>
                            <x-text-input id="email" 
                                class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" 
                                type="email" 
                                name="email" 
                                :value="old('email', $user->email)" 
                                placeholder="example@gmail.com"
                                ::required="userRole !== 'student'" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- ពាក្យសម្ងាត់ថ្មី --}}
                            <div>
                                <x-input-label for="password" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-lock mr-1.5 text-purple-500"></i>
                                    {{ __('new_password_2') }}
                                </x-input-label>
                                <div class="relative w-full h-[50px]">
                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        autocomplete="new-password"
                                        placeholder="{{ __('leave_empty_if_not_changing') }}"
                                        class="block w-full h-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 pr-12 shadow-sm text-gray-900 bg-white transition"
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

                            {{-- បញ្ជាក់ពាក្យសម្ងាត់ថ្មី --}}
                            <div>
                                <x-input-label for="password_confirmation" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-shield-alt mr-1.5 text-purple-500"></i>
                                    {{ __('confirm_new_password_2') }}
                                </x-input-label>
                                <div class="relative w-full h-[50px]">
                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                        placeholder="{{ __('type_the_password_again') }}"
                                        class="block w-full h-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 pr-12 shadow-sm text-gray-900 bg-white transition"
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
                <div x-show="userRole === 'student'" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fas fa-graduation-cap"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('student_information') }}</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                        <div>
                            <x-input-label for="student_id_code" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-id-card mr-1.5 text-emerald-500"></i> {{ __('student_id_code') }}
                            </x-input-label>
                            <div class="block w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-600 font-mono font-bold flex items-center px-4 h-[50px]">
                                {{ $user->student_id_code ?? __('not_created_yet') }}
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5">{{ __('this_id_is_generated_automatically_and_cannot_be_changed') }}</p>
                        </div>
                        <div>
                            <x-input-label for="department_id" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-graduation-cap mr-1.5 text-emerald-500"></i> {{ __('department_3') }}
                            </x-input-label>
                            <select id="department_id" name="department_id" class="block w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]">
                                <option value="">{{ __('select_a_department_2') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name_km ?? $dept->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="degree_level" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-award mr-1.5 text-emerald-500"></i> {{ __('degree_level') }}
                            </x-input-label>
                            @php
                                $enrollmentDegreeLevel = $user->studentDepartmentEnrollments()->where('status', 'active')->first()?->degree_level ?? '';
                            @endphp
                            <select id="degree_level" name="degree_level" class="block w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]">
                                <option value="">{{ __('select_degree_level') }}</option>
                                <option value="បរិញ្ញាបត្រ" {{ old('degree_level', $enrollmentDegreeLevel) == 'បរិញ្ញាបត្រ' ? 'selected' : '' }}>{{ __('bachelor_s_degree') }}</option>
                                <option value="បរិញ្ញាបត្ររង" {{ old('degree_level', $enrollmentDegreeLevel) == 'បរិញ្ញាបត្ររង' ? 'selected' : '' }}>{{ __('associate_degree') }}</option>
                                <option value="អនុបណ្ឌិត" {{ old('degree_level', $enrollmentDegreeLevel) == 'អនុបណ្ឌិត' ? 'selected' : '' }}>{{ __('master_s_degree') }}</option>
                                <option value="បណ្ឌិត" {{ old('degree_level', $enrollmentDegreeLevel) == 'បណ្ឌិត' ? 'selected' : '' }}>{{ __('doctoral_degree') }}</option>
                                <option value="វិញ្ញាបនបត្រ" {{ old('degree_level', $enrollmentDegreeLevel) == 'វិញ្ញាបនបត្រ' ? 'selected' : '' }}>{{ __('certificate') }}</option>
                                <option value="ផ្សេងៗ" {{ old('degree_level', $enrollmentDegreeLevel) == 'ផ្សេងៗ' ? 'selected' : '' }}>{{ __('other_2') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('degree_level')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="generation" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-layer-group mr-1.5 text-emerald-500"></i> {{ __('generation') }}
                            </x-input-label>
                            <select id="generation" name="generation" class="block w-full rounded-xl border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500 shadow-sm transition px-4 py-2.5 h-[50px]">
                                <option value="">{{ __('select_a_generation') }}</option>
                                @foreach(\App\Models\Generation::where('is_active', true)->orderByDesc('name')->get() as $gen)
                                    <option value="{{ $gen->name }}" {{ old('generation', $user->generation) == $gen->name ? 'selected' : '' }}>{{ $gen->name }} ({{ __('enrolled_in_year') }} {{ $gen->join_year }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('generation')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Section 4: Professor Info --}}
                <div x-show="userRole === 'professor'" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('lecturer_information') }}</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                        <div>
                            <x-input-label for="faculty_id" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-university mr-1.5 text-emerald-500"></i> {{ __('faculty') }}
                            </x-input-label>
                            <select id="faculty_id" name="faculty_id" class="block w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]">
                                <option value="">{{ __('select_a_faculty') }}</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ (old('faculty_id', $user->department?->faculty_id ?? '')) == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name_km ?? $faculty->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="department_id" class="font-semibold text-gray-700 mb-1.5">
                                <i class="fas fa-building mr-1.5 text-emerald-500"></i> {{ __('department_3') }}
                            </x-input-label>
                            <select id="department_id" name="department_id" class="block w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]">
                                <option value="">{{ __('please_select_a_department') }}</option>
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
                        <h3 class="text-xl font-bold text-gray-900">{{ __('personal_information') }}</h3>
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
                                                    profilePicturePreview = '{{ $userProfile->profile_picture_url ?? '' }}';
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
                            <div>
                                <label class="inline-flex items-center gap-2 cursor-pointer mt-1">
                                    <input type="checkbox" id="remove_profile_picture" name="remove_profile_picture" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                                    <span class="text-sm text-gray-600 font-medium">{{ __('remove_profile_picture') }}</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                        </div>

                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                            <div>
                                <x-input-label for="full_name_km" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-file-alt mr-1.5 text-orange-500"></i> {{ __('full_name_khmer') }}
                                </x-input-label>
                                <x-text-input id="full_name_km" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="full_name_km" :value="old('full_name_km', $user->profile?->full_name_km ?? $user->studentProfile?->full_name_km ?? '')" placeholder="{{ __('enter_full_name_in_khmer') }}" />
                                <x-input-error :messages="$errors->get('full_name_km')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="full_name_en" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-file-alt mr-1.5 text-orange-500"></i> {{ __('full_name_english') }}
                                </x-input-label>
                                <x-text-input id="full_name_en" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="full_name_en" :value="old('full_name_en', $user->profile?->full_name_en ?? $user->studentProfile?->full_name_en ?? '')" placeholder="{{ __('enter_full_name_in_english') }}" />
                                <x-input-error :messages="$errors->get('full_name_en')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="gender" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-venus-mars mr-1.5 text-orange-500"></i> {{ __('gender') }}
                                </x-input-label>
                                <select id="gender" name="gender" class="block w-full rounded-xl border-gray-200 bg-white focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]">
                                    <option value="">{{ __('select_gender') }}</option>
                                    <option value="male" {{ old('gender', $user->profile?->gender ?? $user->studentProfile?->gender ?? '') == 'male' ? 'selected' : '' }}>{{ __('male') }}</option>
                                    <option value="female" {{ old('gender', $user->profile?->gender ?? $user->studentProfile?->gender ?? '') == 'female' ? 'selected' : '' }}>{{ __('female') }}</option>
                                    <option value="other" {{ old('gender', $user->profile?->gender ?? $user->studentProfile?->gender ?? '') == 'other' ? 'selected' : '' }}>{{ __('other') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="date_of_birth" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-calendar-alt mr-1.5 text-orange-500"></i> {{ __('date_of_birth') }}
                                </x-input-label>
                                <x-text-input id="date_of_birth" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="date" name="date_of_birth" :value="old('date_of_birth', $user->profile?->date_of_birth ?? $user->studentProfile?->date_of_birth ?? '')" />
                                <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone_number" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-phone mr-1.5 text-orange-500"></i> {{ __('phone_number') }}
                                </x-input-label>
                                <x-text-input id="phone_number" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="phone_number" :value="old('phone_number', $user->profile?->phone_number ?? $user->studentProfile?->phone_number ?? '')" placeholder="012 345 678" />
                                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="address" class="font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-map-marker-alt mr-1.5 text-orange-500"></i> {{ __('address') }}
                                </x-input-label>
                                <x-text-input id="address" class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 py-2.5 px-4 h-[50px]" type="text" name="address" :value="old('address', $user->profile?->address ?? $user->studentProfile?->address ?? '')" placeholder="{{ __('enter_address') }}" />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('admin.manage-users') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                        <i class="fas fa-times"></i> {{ __('cancel_2') }}
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl font-bold text-white hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all shadow-lg shadow-green-200">
                        <i class="fas fa-save"></i> {{ __('save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const facultySelect = document.getElementById('faculty_id');
            const departmentSelect = document.getElementById('department_id');
            const selectedDepartmentId = {{ old('department_id', $user->department_id) ?? 'null' }};

            function updateDepartments(facultyId, defaultDepartmentId = null) {
                if (!departmentSelect) return;
                departmentSelect.innerHTML = '<option value="">{{ __("loading_2") }}</option>';
                departmentSelect.disabled = true;

                if (!facultyId) {
                    departmentSelect.innerHTML = '<option value="">{{ __("please_select_a_faculty_first") }}</option>';
                    return;
                }

                fetch(`/admin/get-departments-by-faculty/${facultyId}`)
                    .then(response => response.json())
                    .then(departments => {
                        departmentSelect.innerHTML = '<option value="">{{ __("select_a_department_2") }}</option>';
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

                    const levels = ['{{ __("weak") }}', '{{ __("medium") }}', '{{ __("good") }}', '{{ __("strong") }}', '{{ __("very_strong") }}'];
                    const colors = ['text-red-400', 'text-yellow-400', 'text-green-400', 'text-green-500', 'text-green-600'];
                    
                    strengthText.className = 'text-sm mt-2'; 
                    
                    if (value) {
                        const levelIndex = strength > 0 ? strength - 1 : 0;
                        strengthText.textContent = '{{ __("password_strength") }} ' + levels[levelIndex];
                        strengthText.classList.add(colors[levelIndex]);
                    } else {
                        strengthText.textContent = '';
                    }
                });
            }
        });

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