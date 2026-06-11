<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:p-8">

                {{-- Header --}}
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
                    <span class="p-3 bg-blue-100 text-blue-600 rounded-2xl shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">{{ __('បង្កើតដេប៉ាតឺម៉ង់ថ្មី') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('បំពេញព័ត៌មានខាងក្រោមដើម្បីបង្កើតដេប៉ាតឺម៉ង់ថ្មី') }}</p>
                    </div>
                </div>

                {{-- Floating Toast --}}
                @if (session('success') || session('error'))
                    <div
                        x-data="{
                            show: false,
                            progress: 100,
                            startTimer() {
                                this.show = true;
                                let interval = setInterval(() => {
                                    this.progress -= 1;
                                    if (this.progress <= 0) {
                                        this.show = false;
                                        clearInterval(interval);
                                    }
                                }, 50);
                            }
                        }"
                        x-init="startTimer()"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="translate-y-12 opacity-0"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed top-6 right-6 z-[9999] w-full max-w-sm"
                    >
                        <div class="relative overflow-hidden bg-white rounded-2xl shadow-xl border border-gray-200 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    @if(session('success'))
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 pt-0.5">
                                    <p class="text-sm font-bold text-gray-900">{{ session('success') ? __('ជោគជ័យ!') : __('បរាជ័យ!') }}</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ session('success') ?? session('error') }}</p>
                                </div>
                                <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <div class="absolute bottom-0 left-0 h-1 bg-gray-100 w-full">
                                <div class="h-full transition-all duration-75 ease-linear {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}" :style="`width: ${progress}%`"></div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
                        <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-red-800">{{ __('សូមពិនិត្យកំហុសខាងក្រោម៖') }}</p>
                            <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.store-department') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name_km" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ឈ្មោះដេប៉ាតឺម៉ង់ (ខ្មែរ)') }} <span class="text-red-500">*</span></label>
                            <input id="name_km" type="text" name="name_km" value="{{ old('name_km') }}" required autofocus
                                placeholder="{{ __('បញ្ចូលឈ្មោះជាភាសាខ្មែរ') }}"
                                class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            @error('name_km') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="name_en" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ឈ្មោះដេប៉ាតឺម៉ង់ (អង់គ្លេស)') }} <span class="text-red-500">*</span></label>
                            <input id="name_en" type="text" name="name_en" value="{{ old('name_en') }}" required
                                placeholder="{{ __('បញ្ចូលឈ្មោះជាភាសាអង់គ្លេស') }}"
                                class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            @error('name_en') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="faculty_id" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('មហាវិទ្យាល័យ') }} <span class="text-red-500">*</span></label>
                            <select id="faculty_id" name="faculty_id" required
                                class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="">{{ __('ជ្រើសរើសមហាវិទ្យាល័យ') }}</option>
                                @foreach ($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name_km }} ({{ $faculty->name_en }})
                                    </option>
                                @endforeach
                            </select>
                            @error('faculty_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="head_user_id" class="block text-sm font-bold text-gray-700 mb-1.5">{{ __('ប្រធានដេប៉ាតឺម៉ង់') }}</label>
                            <select id="head_user_id" name="head_user_id"
                                class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="">{{ __('ជ្រើសរើសប្រធាន (ស្រេចចិត្ត)') }}</option>
                                @foreach ($professors as $professor)
                                    <option value="{{ $professor->id }}" {{ old('head_user_id') == $professor->id ? 'selected' : '' }}>
                                        {{ $professor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('head_user_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.manage-departments') }}" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                            {{ __('បោះបង់') }}
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl shadow hover:bg-blue-700 transition active:scale-95">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('បង្កើតដេប៉ាតឺម៉ង់') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
