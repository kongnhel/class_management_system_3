<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen font-inter antialiased">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Page Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="h-12 w-12 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-md shadow-emerald-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.462 9.492 5 8 5c-5.072 0-8 3.844-8 7s2.928 7 8 7c1.492 0 2.832-.462 4-1.253m0-13C13.168 5.462 14.508 5 16 5c5.072 0 8 3.844 8 7s-2.928 7-8 7c-1.492 0-2.832-.462-4-1.253"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">{{ __('create_new_course') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('fill_in_the_information_below_to_create_a_new_course') }}</p>
                </div>
            </div>

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6" role="alert">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-red-800">{{ __('there_is_a_problem') }}</p>
                            <ul class="mt-1 text-sm text-red-600 list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:p-8 overflow-visible">
                <form action="{{ route('admin.store-course') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Section: Basic Info --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="h-8 w-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            {{ __('basic_information') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Title KM --}}
                            <div class="md:col-span-2">
                                <label for="title_km" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('title_khmer') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="title_km" id="title_km"
                                       class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                                       value="{{ old('title_km') }}" required placeholder="{{ __('enter_title_in_khmer') }}">
                                @error('title_km')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Title EN --}}
                            <div class="md:col-span-2">
                                <label for="title_en" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('title_english') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="title_en" id="title_en"
                                       class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                                       value="{{ old('title_en') }}" required placeholder="{{ __('enter_title_in_english') }}">
                                @error('title_en')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Credits --}}
                            <div>
                                <label for="credits" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('credits') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="credits" id="credits"
                                       class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                                       value="{{ old('credits') }}" min="0.5" step="any" required placeholder="{{ __('4_0') }}">
                                <p class="text-xs text-gray-400 mt-1">{{ __('e_g_4_0_3_0_2_5') }}</p>
                                @error('credits')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section: Assignment --}}
                    <div class="border-t border-gray-100 pt-6 overflow-visible">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="h-8 w-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            {{ __('instructions') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 overflow-visible">
                            {{-- Department --}}
                            <div>
                                <label for="department_id" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('department') }} <span class="text-red-500">*</span></label>
                                <select name="department_id" id="department_id"
                                        class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm" required>
                                    <option value="">{{ __('select_a_department') }}</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name_km }} ({{ $department->name_en }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section: Descriptions --}}
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="h-8 w-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            </div>
                            {{ __('description') }}
                        </h3>
                        <div class="grid grid-cols-1 gap-6">
                            {{-- Description KM --}}
                            <div>
                                <label for="description_km" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('description_khmer') }}</label>
                                <textarea name="description_km" id="description_km" rows="4"
                                          class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                                          placeholder="{{ __('enter_description_in_khmer') }}">{{ old('description_km') }}</textarea>
                                @error('description_km')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description EN --}}
                            <div>
                                <label for="description_en" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('description_english') }}</label>
                                <textarea name="description_en" id="description_en" rows="4"
                                          class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                                          placeholder="{{ __('enter_description_in_english') }}">{{ old('description_en') }}</textarea>
                                @error('description_en')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="border-t border-gray-100 pt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.manage-courses') }}"
                           class="px-6 py-3 bg-white border border-gray-200 text-sm font-bold text-gray-600 rounded-xl hover:bg-gray-100 transition-all">
                            {{ __('cancel_2') }}
                        </a>
                        <button type="submit"
                                class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-emerald-200 active:scale-95">
                            {{ __('create_course') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
