<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{
                    viewMode: $persist('grid').as('faculty_view_mode')
                }">

                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ __('គ្រប់គ្រងមហាវិទ្យាល័យ') }}</h1>
                            <p class="text-sm text-gray-500 mt-0.5">{{ __('បញ្ជីឈ្មោះមហាវិទ្យាល័យទាំងអស់នៅក្នុងប្រព័ន្ធ') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- View Toggle --}}
                        <div class="inline-flex rounded-xl bg-white border border-gray-200 p-1 shadow-sm">
                            <button @click="viewMode = 'grid'"
                                    :class="viewMode === 'grid' ? 'bg-emerald-50 text-emerald-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                    class="p-2 rounded-lg transition-all duration-200" title="{{ __('ទម្រង់ប័ណ្ណ') }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                            </button>
                            <button @click="viewMode = 'table'"
                                    :class="viewMode === 'table' ? 'bg-emerald-50 text-emerald-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                    class="p-2 rounded-lg transition-all duration-200" title="{{ __('ទម្រង់តារាង') }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 0v.375" />
                                </svg>
                            </button>
                        </div>

                        {{-- Add Button --}}
                        <a href="{{ route('admin.create-faculty') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-emerald-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('បន្ថែមថ្មី') }}
                        </a>
                    </div>
                </div>

                {{-- Toast --}}
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
                                }, 40);
                            }
                        }"
                        x-init="startTimer()"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="translate-y-2 opacity-0"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed top-5 right-5 z-[9999] w-full max-w-sm"
                    >
                        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg border border-gray-200 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    @if(session('success'))
                                        <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ session('success') ? __('ជោគជ័យ!') : __('បរាជ័យ!') }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-0.5">
                                        {{ session('success') ?? session('error') }}
                                    </p>
                                </div>
                                <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="absolute bottom-0 left-0 h-1 bg-gray-100 w-full">
                                <div class="h-full transition-all duration-75 ease-linear {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}" :style="`width: ${progress}%`"></div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Content --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                    @livewire('faculty-table')
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
