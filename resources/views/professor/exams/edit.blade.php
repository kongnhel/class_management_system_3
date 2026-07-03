<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
            <div>
                <h2 class="font-extrabold text-4xl text-gray-900 leading-tight">
                    {{ __('កែសម្រួលការប្រលង') }}
                </h2>
                <p class="mt-1 text-lg text-gray-500">{{ $exam->title_km ?? $exam->title_en }}</p>
                <p class="text-md text-gray-400">{{ __('សម្រាប់វគ្គសិក្សា:') }} {{ $courseOffering->course->title_km ?? 'N/A' }}</p>
            </div>
            <a href="{{ route('professor.manage-exams', ['offering_id' => $courseOffering->id]) }}" class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-full font-bold text-base text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-300 shadow-sm">
                <i class="fas fa-arrow-left mr-3"></i>
                {{ __('ត្រឡប់ទៅបញ្ជីការប្រលង') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-100">

                <h4 class="text-2xl font-bold text-gray-700 mb-6 flex items-center border-b pb-2">
                    <i class="fas fa-edit text-purple-600 mr-3"></i>
                    {{ __('ទម្រង់កែសម្រួលការប្រលង') }}
                </h4>
                <div class="bg-gray-50 p-8 rounded-2xl shadow-inner border border-gray-100">
                    <form action="{{ route('professor.exams.update', ['offering_id' => $courseOffering->id, 'exam' => $exam->id]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updating --}}
                        
                        <div class="md:col-span-1">
                            <label for="title_km" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ចំណងជើង (ខ្មែរ)') }}</label>
                            <input type="text" id="title_km" name="title_km" value="{{ old('title_km', $exam->title_km) }}" required class="block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div class="md:col-span-1">
                            <label for="title_en" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ចំណងជើង (អង់គ្លេស)') }}</label>
                            <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $exam->title_en) }}" required class="block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div class="lg:col-span-1">
                            <label for="max_score" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ពិន្ទុអតិបរមា') }}</label>
                            <input type="number" id="max_score" name="max_score" value="{{ old('max_score', $exam->max_score) }}" required min="0" class="block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>

                        <div class="md:col-span-1">
                            <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ថ្ងៃប្រលង') }}</label>
                            <input type="datetime-local" id="exam_date" name="exam_date" value="{{ old('exam_date', \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d\TH:i')) }}" required class="block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div class="md:col-span-1">
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('រយៈពេល (នាទី)') }}</label>
                            <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required min="10" class="block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="description_km" class="block text-sm font-medium text-gray-700 mb-1">{{ __('បរិយាយ (ខ្មែរ)') }}</label>
                            <textarea id="description_km" name="description_km" rows="2" class="block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">{{ old('description_km', $exam->description_km) }}</textarea>
                        </div>
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="description_en" class="block text-sm font-medium text-gray-700 mb-1">{{ __('បរិយាយ (អង់គ្លេស)') }}</label>
                            <textarea id="description_en" name="description_en" rows="2" class="block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">{{ old('description_en', $exam->description_en) }}</textarea>
                        </div>
                        
                        <div class="md:col-span-2 lg:col-span-3 flex justify-end mt-4">
                            <button type="submit" class="w-full md:w-auto px-8 py-4 text-white font-extrabold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:scale-[1.01] bg-gradient-to-r from-purple-600 to-pink-700 hover:from-purple-700 hover:to-pink-800">
                                <span class="flex items-center justify-center space-x-2">
                                    <i class="fas fa-save text-lg"></i>
                                    <span>{{ __('រក្សាទុកការផ្លាស់ប្តូរ') }}</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>