<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('admin.manage-faculties') }}" class="flex-shrink-0 w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('បង្កើតមហាវិទ្យាល័យថ្មី') }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">{{ __('បំពេញព័ត៌មានខាងក្រោមដើម្បីបន្ថែមមហាវិទ្យាល័យថ្មី') }}</p>
                    </div>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <form method="POST" action="{{ route('admin.store-faculty') }}">
                    @csrf

                    {{-- Name Khmer --}}
                    <div class="mb-5">
                        <label for="name_km" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ឈ្មោះមហាវិទ្យាល័យជាខ្មែរ') }} <span class="text-red-500">*</span></label>
                        <input
                            id="name_km"
                            type="text"
                            name="name_km"
                            value="{{ old('name_km') }}"
                            required
                            autofocus
                            placeholder="{{ __('ឧ. មហាវិទ្យាល័យវិទ្យាសាស្ត្រ និងបច្ចេកវិទ្យា') }}"
                            class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 @error('name_km') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                        @error('name_km')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Name English --}}
                    <div class="mb-5">
                        <label for="name_en" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ឈ្មោះមហាវិទ្យាល័យជាអង់គ្លេស') }} <span class="text-red-500">*</span></label>
                        <input
                            id="name_en"
                            type="text"
                            name="name_en"
                            value="{{ old('name_en') }}"
                            required
                            placeholder="{{ __('e.g. Faculty of Science and Technology') }}"
                            class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 @error('name_en') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                        @error('name_en')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Dean --}}
                    <div class="mb-8">
                        <label for="dean_user_id" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ប្រធានមហាវិទ្យាល័យ (Dean)') }}</label>
                        <select
                            id="dean_user_id"
                            name="dean_user_id"
                            class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 @error('dean_user_id') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        >
                            <option value="">{{ __('ជ្រើសរើសប្រធានមហាវិទ្យាល័យ (ស្រេចចិត្ត)') }}</option>
                            @foreach ($professors as $professor)
                                <option value="{{ $professor->id }}" {{ old('dean_user_id') == $professor->id ? 'selected' : '' }}>
                                    {{ $professor->name }} ({{ $professor->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('dean_user_id')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-100">
                        <a href="{{ route('admin.manage-faculties') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                            {{ __('បោះបង់') }}
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-blue-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('បង្កើតមហាវិទ្យាល័យ') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
