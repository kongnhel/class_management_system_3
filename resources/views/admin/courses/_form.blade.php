{{-- Shared course fields for the create and edit forms.
     Expects: $departments (array), $course (Course|null - null when creating). --}}

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
                   value="{{ old('title_km', $course?->title_km) }}" required placeholder="{{ __('enter_title_in_khmer') }}">
            @error('title_km')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Title EN --}}
        <div class="md:col-span-2">
            <label for="title_en" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('title_english') }} <span class="text-red-500">*</span></label>
            <input type="text" name="title_en" id="title_en"
                   class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                   value="{{ old('title_en', $course?->title_en) }}" required placeholder="{{ __('enter_title_in_english') }}">
            @error('title_en')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Credits --}}
        <div>
            <label for="credits" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('credits') }} <span class="text-red-500">*</span></label>
            <input type="number" name="credits" id="credits"
                   class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                   value="{{ old('credits', $course?->credits) }}" min="0.5" step="any" required placeholder="{{ __('4_0') }}">
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
                    <option value="{{ $department->id }}" {{ old('department_id', $course?->department_id) == $department->id ? 'selected' : '' }}>
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
                      placeholder="{{ __('enter_description_in_khmer') }}">{{ old('description_km', $course?->description_km) }}</textarea>
            @error('description_km')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description EN --}}
        <div>
            <label for="description_en" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('description_english') }}</label>
            <textarea name="description_en" id="description_en" rows="4"
                      class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                      placeholder="{{ __('enter_description_in_english') }}">{{ old('description_en', $course?->description_en) }}</textarea>
            @error('description_en')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
