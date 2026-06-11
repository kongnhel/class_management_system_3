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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('កែប្រែមហាវិទ្យាល័យ') }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $faculty->name_km }}</p>
                    </div>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <form method="POST" action="{{ route('admin.update-faculty', $faculty->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Name Khmer --}}
                    <div class="mb-5">
                        <label for="name_km" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ឈ្មោះមហាវិទ្យាល័យជាខ្មែរ') }} <span class="text-red-500">*</span></label>
                        <input
                            id="name_km"
                            type="text"
                            name="name_km"
                            value="{{ old('name_km', $faculty->name_km) }}"
                            required
                            autofocus
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
                            value="{{ old('name_en', $faculty->name_en) }}"
                            required
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
                                <option value="{{ $professor->id }}" {{ old('dean_user_id', $faculty->dean_user_id) == $professor->id ? 'selected' : '' }}>
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

                    {{-- Department Info --}}
                    @if($faculty->departments->count() > 0)
                        <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                                <span>{{ __('មហាវិទ្យាល័យនេះមាន') }} <span class="font-semibold text-gray-900">{{ $faculty->departments->count() }}</span> {{ __('ដេប៉ាតឺម៉ង់') }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-100">
                        <a href="{{ route('admin.manage-faculties') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                            {{ __('បោះបង់') }}
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-blue-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {{ __('រក្សាទុកការកែប្រែ') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
