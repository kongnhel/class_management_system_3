<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-200 transition-all duration-300 transform hover:shadow-3xl">

                <!-- Page Header -->
                <div class="mb-8 pb-4 border-b border-gray-200">
                    <h2 class="font-extrabold text-4xl text-gray-900 leading-tight">
                        {{ __('បង្កើតកម្មវិធីសិក្សាថ្មី') }}
                    </h2>
                    <p class="mt-2 text-lg text-gray-500">{{ __('បំពេញព័ត៌មានលម្អិតខាងក្រោមដើម្បីបង្កើតកម្មវិធីសិក្សាថ្មីមួយ។') }}</p>
                </div>

                <!-- Error & Session Messages -->
                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl relative mb-8 flex items-center space-x-3 shadow-md" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="block sm:inline font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl relative mb-8 flex items-center space-x-3 shadow-md" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586l-1.293-1.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="block sm:inline font-medium">{{ session('error') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl relative mb-8 flex items-start space-x-3 shadow-md" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <strong class="font-bold block">{{ __('មានបញ្ហា!') }}</strong>
                            <span class="block sm:inline mt-1">{{ __('សូមពិនិត្យមើលកំហុសឆ្គងខាងក្រោម។') }}</span>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.store-program') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Name (Khmer) -->
                        <div>
                            <label for="name_km" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('ឈ្មោះកម្មវិធីសិក្សា (ខ្មែរ)') }}</label>
                            <input id="name_km" class="form-input w-full rounded-xl border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 transition duration-150 ease-in-out placeholder-gray-400" type="text" name="name_km" value="{{ old('name_km') }}" required autofocus placeholder="{{ __('បញ្ចូលឈ្មោះកម្មវិធីសិក្សាជាភាសាខ្មែរ') }}" />
                        </div>

                        <!-- Name (English) -->
                        <div>
                            <label for="name_en" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('ឈ្មោះកម្មវិធីសិក្សា (អង់គ្លេស)') }}</label>
                            <input id="name_en" class="form-input w-full rounded-xl border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 transition duration-150 ease-in-out placeholder-gray-400" type="text" name="name_en" value="{{ old('name_en') }}" required placeholder="{{ __('បញ្ចូលឈ្មោះកម្មវិធីសិក្សាជាភាសាអង់គ្លេស') }}" />
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department_id" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('ដេប៉ាតឺម៉ង់') }}</label>
                            <select id="department_id" name="department_id" class="form-select w-full rounded-xl border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 transition duration-150 ease-in-out" required>
                                <option value="">{{ __('ជ្រើសរើសដេប៉ាតឺម៉ង់') }}</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name_km }} ({{ $department->name_en }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Duration Years -->
                        <div>
                            <label for="duration_years" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('រយៈពេល (ឆ្នាំ)') }}</label>
                            <input id="duration_years" class="form-input w-full rounded-xl border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 transition duration-150 ease-in-out placeholder-gray-400" type="number" name="duration_years" value="{{ old('duration_years') }}" min="1" required placeholder="{{ __('ឧទាហរណ៍៖ ៤') }}" />
                        </div>
                        
                        <!-- Degree Level -->
                        <div>
                            <label for="degree_level" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('កម្រិតសញ្ញាបត្រ') }}</label>
                            <select id="degree_level" name="degree_level" class="form-select w-full rounded-xl border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 transition duration-150 ease-in-out" required>
                                <option value="">{{ __('ជ្រើសរើសកម្រិតសញ្ញាបត្រ') }}</option>
                                <option value="បរិញ្ញាបត្រ" {{ old('degree_level') == 'បរិញ្ញាបត្រ' ? 'selected' : '' }}>{{ __('បរិញ្ញាបត្រ') }}</option>
                                <option value="បរិញ្ញាបត្ររង" {{ old('degree_level') == 'បរិញ្ញាបត្ររង' ? 'selected' : '' }}>{{ __('បរិញ្ញាបត្ររង') }}</option>
                                <option value="អនុបណ្ឌិត" {{ old('degree_level') == 'អនុបណ្ឌិត' ? 'selected' : '' }}>{{ __('អនុបណ្ឌិត') }}</option>
                                <option value="បណ្ឌិត" {{ old('degree_level') == 'បណ្ឌិត' ? 'selected' : '' }}>{{ __('បណ្ឌិត') }}</option>
                                <option value="វិញ្ញាបនបត្រ" {{ old('degree_level') == 'វិញ្ញាបនបត្រ' ? 'selected' : '' }}>{{ __('វិញ្ញាបនបត្រ') }}</option>
                                <option value="ផ្សេងៗ" {{ old('degree_level') == 'ផ្សេងៗ' ? 'selected' : '' }}>{{ __('ផ្សេងៗ') }}</option>
                            </select>
                        </div>

                        <!-- Pathway Program (for bachelor's programs that accept associate's graduates) -->
                        <div>
                            <label for="pathway_program_id" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('កម្មវិធីសិក្សាផ្លូវបន្ត') }}</label>
                            <select id="pathway_program_id" name="pathway_program_id" class="form-select w-full rounded-xl border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 transition duration-150 ease-in-out">
                                <option value="">{{ __('មិនមានផ្លូវបន្ត') }}</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}" {{ old('pathway_program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name_km }} ({{ $program->name_en }}) - {{ $program->duration_years }} ឆ្នាំ
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">{{ __('ជ្រើសរើសកម្មវិធីសិក្សាបរិញ្ញាបត្ររងដែលសិស្សអាចផ្ទេរពី។ សិស្សនឹងចាប់ផ្តើមពីឆ្នាំទី ៣។') }}</p>
                        </div>
                    </div>

                    <div class="mt-12 flex justify-between items-center">
                        <a href="{{ route('admin.manage-programs') }}" class="px-6 py-3 text-gray-600 font-semibold rounded-full hover:bg-gray-200 transition duration-300 transform hover:scale-105">{{ __('បោះបង់') }}</a>
                        
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-full shadow-lg hover:from-green-600 hover:to-green-700 transition duration-300 transform hover:scale-105 flex items-center space-x-2">
                            <span>{{ __('បង្កើតកម្មវិធីសិក្សា') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
