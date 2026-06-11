<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-10">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="bg-amber-100 rounded-2xl p-3">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ __('កែសម្រួលសេចក្តីប្រកាស') }}</h1>
                        <p class="text-gray-500 mt-1">{{ __('កែប្រែព័ត៌មានលម្អិតនៃសេចក្តីប្រកាសនេះ') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('ត្រឡប់') }}
                </a>
            </div>

            {{-- Toast --}}
            @if (session('success') || session('error'))
            <div
                x-data="{ show: false, progress: 100, init() { this.show = true; const t = setInterval(() => { this.progress -= 1; if (this.progress <= 0) { this.show = false; clearInterval(t); } }, 50); } }"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="fixed top-6 right-6 z-[9999] w-full max-w-sm"
            >
                <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-200 p-4">
                    <div class="flex items-center gap-3">
                        @if(session('success'))
                            <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @else
                            <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900">{{ session('success') ? 'ជោគជ័យ!' : 'បរាជ័យ!' }}</p>
                            <p class="text-sm text-gray-600 truncate">{{ session('success') ?? session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-100 rounded-b-2xl overflow-hidden">
                        <div class="h-full transition-all duration-75 ease-linear {{ session('success') ? 'bg-emerald-500' : 'bg-red-500' }}" :style="`width: ${progress}%`"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center mt-0.5">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-red-800">{{ __('មានបញ្ហា!') }}</p>
                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.announcements.update', $announcement->id) }}">
                @csrf
                @method('PUT')
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">

                    {{-- Titles --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            {{ __('ចំណងជើង') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="title_km" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ចំណងជើង (ខ្មែរ)') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="title_km" id="title_km" value="{{ old('title_km', $announcement->title_km) }}" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400"
                                    placeholder="{{ __('បញ្ចូលចំណងជើងជាភាសាខ្មែរ') }}">
                            </div>
                            <div>
                                <label for="title_en" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ចំណងជើង (អង់គ្លេស)') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="title_en" id="title_en" value="{{ old('title_en', $announcement->title_en) }}" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400"
                                    placeholder="{{ __('បញ្ចូលចំណងជើងជាភាសាអង់គ្លេស') }}">
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Content --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                            {{ __('ខ្លឹមសារ') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="content_km" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ខ្លឹមសារ (ខ្មែរ)') }} <span class="text-red-500">*</span></label>
                                <textarea name="content_km" id="content_km" rows="6" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400 resize-none"
                                    placeholder="{{ __('បញ្ចូលខ្លឹមសារជាភាសាខ្មែរ') }}">{{ old('content_km', $announcement->content_km) }}</textarea>
                            </div>
                            <div>
                                <label for="content_en" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('ខ្លឹមសារ (អង់គ្លេស)') }} <span class="text-red-500">*</span></label>
                                <textarea name="content_en" id="content_en" rows="6" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400 resize-none"
                                    placeholder="{{ __('បញ្ចូលខ្លឹមសារជាភាសាអង់គ្លេស') }}">{{ old('content_en', $announcement->content_en) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Settings --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            {{ __('ការកំណត់') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="target_role" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('កំណត់គោលដៅអ្នកប្រើប្រាស់') }}</label>
                                <select name="target_role" id="target_role"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 appearance-none bg-no-repeat bg-right pr-10"
                                    style="background-image: url('data:image/svg+xml;utf8,<svg fill=&quot;%236B7280&quot; viewBox=&quot;0 0 20 20&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><path fill-rule=&quot;evenodd&quot; d=&quot;M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z&quot; clip-rule=&quot;evenodd&quot;/></svg>'); background-position: right 0.75rem center; background-size: 1.25rem;">
                                    <option value="all" {{ old('target_role', $announcement->target_role) == 'all' ? 'selected' : '' }}>{{ __('ទាំងអស់') }}</option>
                                    <option value="student" {{ old('target_role', $announcement->target_role) == 'student' ? 'selected' : '' }}>{{ __('សិស្ស') }}</option>
                                    <option value="professor" {{ old('target_role', $announcement->target_role) == 'professor' ? 'selected' : '' }}>{{ __('គ្រូបង្រៀន') }}</option>
                                    <option value="admin" {{ old('target_role', $announcement->target_role) == 'admin' ? 'selected' : '' }}>{{ __('អ្នកគ្រប់គ្រង') }}</option>
                                </select>
                            </div>
                            <div></div>
                        </div>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('admin.announcements.index') }}" class="px-6 py-3 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        {{ __('ត្រឡប់ក្រោយ') }}
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200 hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('រក្សាទុកការកែប្រែ') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
