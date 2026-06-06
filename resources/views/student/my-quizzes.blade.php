<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
                {{ __('Quiz របស់ខ្ញុំ') }}
            </h2>
            <a href="#" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-300">
                {{ __('Quiz ដែលបានបញ្ចប់') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8">
                <h3 class="text-3xl font-extrabold text-gray-800 mb-8">{{ __('បញ្ជី Quiz ដែលត្រូវធ្វើ') }}</h3>

                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm leading-5 font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($quizzes as $quiz)
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 flex flex-col justify-between hover:shadow-2xl transition-all duration-300">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $quiz->title_km ?? 'N/A' }}</h3>
                                <p class="text-sm text-gray-600 mb-4 font-medium">{{ __('មុខវិជ្ជា:') }} <span class="text-indigo-600">{{ $quiz->courseOffering->course->title_km ?? 'N/A' }}</span></p>
                                <p class="text-gray-700 mb-4 line-clamp-3">{{ $quiz->description_km ?? 'N/A' }}</p>
                            </div>
                            <div class="flex items-center justify-between mt-4">
                                @if ($quiz->score)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold leading-5 bg-green-100 text-green-800">
                                        {{ __('ពិន្ទុ:') }} {{ $quiz->score->score }} / {{ $quiz->total_points }}
                                    </span>
                                    <button class="bg-gray-400 text-white px-4 py-2 rounded-lg cursor-not-allowed" disabled>
                                        {{ __('បានបញ្ចប់') }}
                                    </button>
                                @else
                                    <span class="text-sm text-red-500 font-semibold">{{ __('មិនទាន់បានធ្វើ') }}</span>
                                    <a href="{{ route('student.take-quiz', $quiz->id) }}" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-all duration-200 ease-in-out">
                                        {{ __('ចូលរួម Quiz') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-500 font-medium">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1a9 9 0 1118 0c0 4.97-4.03 9-9 9S0 18.97 0 14z"></path>
                                </svg>
                                <p>{{ __('មិនមាន Quiz ដែលត្រូវធ្វើទេ។') }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if ($quizzes->hasPages())
                    <div class="mt-8 flex justify-end">
                        <nav role="navigation" aria-label="Pagination Navigation">
                            {{ $quizzes->links() }}
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>