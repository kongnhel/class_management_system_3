<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-gray-900 leading-tight tracking-wide flex items-center">
            <i class="fas fa-graduation-cap mr-3 text-purple-600"></i>
            {{ __('កម្មវិធីសិក្សាទាំងអស់') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-200 transition-all duration-300 transform hover:shadow-3xl">

                <div class="flex flex-col md:flex-row items-center justify-between mb-10 pb-4 border-b border-gray-200">
                    <div>
                        <h2 class="font-extrabold text-4xl text-gray-900 leading-tight">
                            {{ __('គ្រប់គ្រងកម្មវិធីសិក្សា') }}
                        </h2>
                        <p class="mt-2 text-lg text-gray-500">{{ __('បញ្ជីឈ្មោះកម្មវិធីសិក្សាទាំងអស់នៅក្នុងប្រព័ន្ធ') }}</p>
                    </div>
                    {{-- <div class="mt-4 md:mt-0">
                        <a href="{{ route('admin.create-program') }}" class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold rounded-full shadow-lg hover:from-emerald-600 hover:to-emerald-700 transition duration-300 transform hover:scale-105 flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ __('បន្ថែមកម្មវិធីថ្មី') }}</span>
                        </a>
                    </div> --}}
                </div>
                
                <div class="mt-8">
                    @if ($programs->isEmpty())
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white p-12 rounded-3xl text-center text-gray-400 shadow-xl border border-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-20 w-20 mb-4 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <p class="font-semibold text-lg">{{ __('មិនទាន់មានកម្មវិធីសិក្សាណាមួយនៅឡើយទេ។') }}</p>
                            <p class="mt-2 text-sm">{{ __('ចាប់ផ្តើមដោយបន្ថែមកម្មវិធីសិក្សាដំបូងរបស់អ្នកដើម្បីគ្រប់គ្រងមុខវិជ្ជា និងនិស្សិត។') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach ($programs as $program)
                                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 flex flex-col justify-between hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-start mb-6">
                                        <div class="flex-shrink-0 w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                                            {{-- Icon for program card, you can change this SVG if needed --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20v2.5a2.5 2.5 0 0 1-2.5 2.5H4zM20 17V5.5A2.5 2.5 0 0 0 17.5 3H6.5A2.5 2.5 0 0 0 4 5.5v11.5"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-2xl font-bold text-gray-900 leading-tight">{{ $program->name_km ?? 'N/A' }}</h4>
                                            <p class="text-base text-gray-500 mt-1">{{ $program->name_en ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2 mb-6">
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('ដេប៉ាតឺម៉ង់') }}:</span> <span class="text-gray-600">{{ $program->department->name_km ?? 'N/A' }}</span></p>
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('រយៈពេល (ឆ្នាំ)') }}:</span> <span class="text-gray-600">{{ $program->duration_years ?? 'N/A' }}</span></p>
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('កម្រិតសញ្ញាបត្រ') }}:</span> <span class="text-gray-600">{{ $program->degree_level ?? 'N/A' }}</span></p>
                                    </div>
                                    {{-- <div class="flex justify-end mt-auto">
                                        <a href="#" class="inline-block w-full text-center bg-emerald-600 text-white font-semibold py-3 px-6 rounded-full shadow-lg hover:bg-emerald-700 transition-colors duration-200 transform hover:scale-105">
                                            {{ __('មើលលម្អិត') }}
                                        </a>
                                    </div> --}}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                {{-- Pagination Links --}}
                @if ($programs->hasPages())
                    <div class="mt-8">
                        {{ $programs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
