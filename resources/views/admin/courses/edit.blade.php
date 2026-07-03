<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen font-inter antialiased">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Page Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="h-12 w-12 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-md shadow-emerald-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">{{ __('កែប្រែមុខវិជ្ជា') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $course->title_km }}</p>
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
                            <p class="text-sm font-bold text-red-800">{{ __('មានបញ្ហា!') }}</p>
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:p-8">
                <form action="{{ route('admin.update-course', $course->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Section: Basic Info --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="h-8 w-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            {{ __('ព័ត៌មានមូលដ្ឋាន') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Title KM --}}
                            <div class="md:col-span-2">
                                <label for="title_km" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ចំណងជើង (ខ្មែរ)') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="title_km" id="title_km"
                                       class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                                       value="{{ old('title_km', $course->title_km) }}" required>
                                @error('title_km')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Title EN --}}
                            <div class="md:col-span-2">
                                <label for="title_en" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ចំណងជើង (អង់គ្លេស)') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="title_en" id="title_en"
                                       class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                                       value="{{ old('title_en', $course->title_en) }}" required>
                                @error('title_en')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Credits --}}
                            <div>
                                <label for="credits" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ក្រេឌីត') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="credits" id="credits"
                                       class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm"
                                       value="{{ old('credits', $course->credits) }}" min="0.5" step="0.1" required>
                                @error('credits')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Generation --}}
                            <div>
                                <label for="generation" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ជំនាន់') }} <span class="text-red-500">*</span></label>
                                <select name="generation" id="generation"
                                        class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm" required>
                                    <option value="">{{ __('ជ្រើសរើសជំនាន់') }}</option>
                                    @foreach ($generations as $generation)
                                        <option value="{{ $generation }}" {{ old('generation', $course->generation) == $generation ? 'selected' : '' }}>
                                            {{ $generation }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('generation')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section: Assignment --}}
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="h-8 w-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            {{ __('ការចង្អុលបង្ហាញ') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Department --}}
                            <div>
                                <label for="department_id" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('នាយកដ្ឋាន') }} <span class="text-red-500">*</span></label>
                                <select name="department_id" id="department_id"
                                        class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm" required>
                                    <option value="">{{ __('ជ្រើសរើសនាយកដ្ឋាន') }}</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $course->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name_km }} ({{ $department->name_en }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Multi-Program Selection --}}
                            <div x-data="{
                                    selectedPrograms: {{ json_encode(old('program_ids', $selectedPrograms)) }}
                                 }">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('កម្មវិធីសិក្សា') }} <span class="text-red-500">*</span></label>
                                <div class="space-y-3">
                                    <template x-for="(item, index) in selectedPrograms" :key="index">
                                        <div class="flex items-center gap-2">
                                            <select name="program_ids[]"
                                                    class="flex-1 min-w-0 rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm" required>
                                                <option value="">{{ __('ជ្រើសរើសកម្មវិធីសិក្សា') }}</option>
                                                @foreach($programs as $program)
                                                    <option value="{{ $program->id }}" x-bind:selected="item == {{ $program->id }}">
                                                        {{ $program->name_km }} ({{ $program->name_en }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" @click="selectedPrograms.splice(index, 1)" x-show="selectedPrograms.length > 1"
                                                    class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="selectedPrograms.push('')"
                                            class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-bold hover:bg-emerald-100 transition-all border border-emerald-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        {{ __('បន្ថែមកម្មវិធីសិក្សា') }}
                                    </button>
                                </div>
                                @error('program_ids')
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
                            {{ __('ការពិពណ៌នា') }}
                        </h3>
                        <div class="grid grid-cols-1 gap-6">
                            {{-- Description KM --}}
                            <div>
                                <label for="description_km" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ការពិពណ៌នា (ខ្មែរ)') }}</label>
                                <textarea name="description_km" id="description_km" rows="4"
                                          class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm">{{ old('description_km', $course->description_km) }}</textarea>
                                @error('description_km')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description EN --}}
                            <div>
                                <label for="description_en" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ការពិពណ៌នា (អង់គ្លេស)') }}</label>
                                <textarea name="description_en" id="description_en" rows="4"
                                          class="w-full rounded-xl border-0 bg-gray-100 text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm">{{ old('description_en', $course->description_en) }}</textarea>
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
                            {{ __('បោះបង់') }}
                        </a>
                        <button type="submit"
                                class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-emerald-200 active:scale-95">
                            {{ __('រក្សារាងការផ្លាស់ប្តូរ') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
