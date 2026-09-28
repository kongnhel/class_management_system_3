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
                    <h2 class="text-3xl font-bold text-gray-900">{{ __('edit_course') }}</h2>
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:p-8">
                <form action="{{ route('admin.update-course', $course->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @include('admin.courses._form', ['course' => $course])

                    {{-- Action Buttons --}}
                    <div class="border-t border-gray-100 pt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.manage-courses') }}"
                           class="px-6 py-3 bg-white border border-gray-200 text-sm font-bold text-gray-600 rounded-xl hover:bg-gray-100 transition-all">
                            {{ __('cancel_2') }}
                        </a>
                        <button type="submit"
                                class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-emerald-200 active:scale-95">
                            {{ __('save_changes_2') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
