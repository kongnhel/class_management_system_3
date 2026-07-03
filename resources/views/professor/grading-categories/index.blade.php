<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-800 leading-tight">
            {{ __('គ្រប់គ្រងប្រភេទពិន្ទុ') }}
        </h2>
        <p class="mt-1 text-lg text-gray-500">{{ $course->title_km }}</p>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 border border-gray-100">
                    <h4 class="text-2xl font-bold text-gray-700 mb-6">{{ __('បន្ថែមប្រភេទថ្មី') }}</h4>
                    <form action="{{ route('professor.grading-categories.store', $course->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="name_km" class="block text-sm font-medium text-gray-700">{{ __('ឈ្មោះ (ខ្មែរ)') }} <span class="text-red-500">*</span></label>
                            <input type="text" id="name_km" name="name_km" value="{{ old('name_km') }}" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label for="name_en" class="block text-sm font-medium text-gray-700">{{ __('ឈ្មោះ (អង់គ្លេស)') }}</label>
                            <input type="text" id="name_en" name="name_en" value="{{ old('name_en') }}" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label for="weight_percentage" class="block text-sm font-medium text-gray-700">{{ __('ភាគរយទម្ងន់ (%)') }} <span class="text-red-500">*</span></label>
                            <input type="number" id="weight_percentage" name="weight_percentage" value="{{ old('weight_percentage') }}" required min="1" max="100" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div class="pt-2">
                             <button type="submit" class="w-full px-8 py-4 text-white font-extrabold rounded-xl shadow-lg transition-all duration-300 bg-gradient-to-r from-emerald-600 to-purple-700 hover:from-emerald-700 hover:to-purple-800">
                                {{ __('បន្ថែម') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 border border-gray-100">
                    <h4 class="text-2xl font-bold text-gray-700 mb-6">{{ __('បញ្ជីប្រភេទពិន្ទុ') }}</h4>
                    @php
                        $totalWeight = $course->gradingCategories->sum('weight_percentage');
                    @endphp
                    <div class="mb-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-base font-medium text-emerald-700">{{ __('ភាគរយសរុប') }}</span>
                            <span class="text-sm font-medium text-emerald-700">{{ $totalWeight }}% / 100%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-emerald-600 h-2.5 rounded-full" style="width: {{ $totalWeight }}%"></div>
                        </div>
                         @if($totalWeight < 100)
                             <p class="text-sm text-yellow-600 mt-2">{{ __('នៅសល់') }} {{ 100 - $totalWeight }}% {{ __('ទៀតដែលត្រូវបំពេញ។') }}</p>
                        @elseif($totalWeight > 100)
                            <p class="text-sm text-red-600 mt-2">{{ __('ភាគរយសរុបលើស 100%!') }}</p>
                        @else
                             <p class="text-sm text-green-600 mt-2">{{ __('ភាគរយសរុបបានពេញ 100%។') }}</p>
                        @endif
                    </div>
                    <div class="space-y-3">
                        @forelse ($course->gradingCategories as $category)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 border">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $category->name_km }}</p>
                                    <p class="text-sm text-gray-500">{{ $category->name_en }}</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="font-bold text-emerald-600 text-lg">{{ $category->weight_percentage }}%</span>
                                    <form action="{{ route('professor.grading-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('{{ __('តើអ្នកប្រាកដទេថាចង់លុប?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-8">{{ __('មិនទាន់មានប្រភេទពិន្ទុត្រូវបានបង្កើតទេ។') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>