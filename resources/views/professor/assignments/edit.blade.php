<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-800 leading-tight">
                    {{ __('កែសម្រួលកិច្ចការស្រាវជ្រាវ') }}
                </h2>
                <p class="mt-1 text-lg text-gray-500">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en ?? 'N/A' }}</p>
            </div>
            <a href="{{ route('professor.manage-assignments', ['offering_id' => $courseOffering->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('ត្រឡប់ទៅបញ្ជីកិច្ចការវិញ') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-100">

                <h4 class="text-2xl font-bold text-gray-700 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    {{ __('ទម្រង់កែសម្រួលកិច្ចការ') }}
                </h4>
                <div class="bg-gray-50 p-8 rounded-2xl shadow-inner border border-gray-100">
                    {{-- Form pointing to the update route --}}
                    <form action="{{ route('professor.assignments.update', ['offering_id' => $courseOffering->id, 'assignment' => $assignment->id]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updating --}}
                        
                        <div>
                            <label for="title_km" class="block text-sm font-medium text-gray-700">{{ __('ចំណងជើង (ខ្មែរ)') }} <span class="text-red-500">*</span></label>
                            <input type="text" id="title_km" name="title_km" value="{{ old('title_km', $assignment->title_km) }}" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div>
                            <label for="title_en" class="block text-sm font-medium text-gray-700">{{ __('ចំណងជើង (អង់គ្លេស)') }}</label>
                            <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $assignment->title_en) }}" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div class="md:col-span-2">
                            <label for="description_km" class="block text-sm font-medium text-gray-700">{{ __('បរិយាយ (ខ្មែរ)') }}</label>
                            <textarea id="description_km" name="description_km" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">{{ old('description_km', $assignment->description_km) }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="description_en" class="block text-sm font-medium text-gray-700">{{ __('បរិយាយ (អង់គ្លេស)') }}</label>
                            <textarea id="description_en" name="description_en" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">{{ old('description_en', $assignment->description_en) }}</textarea>
                        </div>
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700">{{ __('ថ្ងៃផុតកំណត់') }} <span class="text-red-500">*</span></label>
                            {{-- Format the date for the datetime-local input --}}
                            <input type="datetime-local" id="due_date" name="due_date" value="{{ old('due_date', \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d\TH:i')) }}" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div>
                            <label for="max_score" class="block text-sm font-medium text-gray-700">{{ __('ពិន្ទុអតិបរមា') }} <span class="text-red-500">*</span></label>
                            <input type="number" id="max_score" name="max_score" value="{{ old('max_score', $assignment->max_score) }}" required min="0" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        
                        {{-- Uncomment and implement if you use Grading Categories --}}
                        {{-- <div class="md:col-span-2">
                            <label for="grading_category_id" class="block text-sm font-medium text-gray-700">{{ __('ប្រភេទពិន្ទុ') }} <span class="text-red-500">*</span></label>
                            <select id="grading_category_id" name="grading_category_id" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                                @forelse ($gradingCategories as $category)
                                    <option value="{{ $category->id }}" {{ $assignment->grading_category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_km }} ({{ $category->weight_percentage }}%)
                                    </option>
                                @empty
                                    <option value="" disabled>សូមបង្កើតប្រភេទពិន្ទុសម្រាប់មុខវិជ្ជានេះជាមុនសិន</option>
                                @endforelse
                            </select>
                        </div> --}}

                        <div class="md:col-span-2 flex justify-end mt-4">
                            <button type="submit" class="w-full md:w-auto px-8 py-4 text-white font-extrabold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:scale-[1.01] bg-gradient-to-r from-purple-600 to-pink-700 hover:from-purple-700 hover:to-pink-800">
                                <span class="flex items-center justify-center space-x-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
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