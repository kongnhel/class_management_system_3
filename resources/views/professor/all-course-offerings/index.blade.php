<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-800 leading-tight">
            {{ __('ការផ្តល់ជូនមុខវិជ្ជា') }}
        </h2>
        <p class="mt-1 text-lg text-gray-500">{{ __('បញ្ជីការផ្តល់ជូនមុខវិជ្ជាទាំងអស់នៅក្នុងប្រព័ន្ធ') }}</p>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-200 transition-all duration-300 transform hover:shadow-3xl">

                <div class="flex flex-col md:flex-row items-center justify-between mb-10 pb-4 border-b border-gray-200">
                    <div>
                        <h2 class="font-extrabold text-4xl text-gray-900 leading-tight">
                            {{ __('បញ្ជីការផ្តល់ជូនមុខវិជ្ជាទាំងអស់') }}
                        </h2>
                        <p class="mt-2 text-lg text-gray-500">{{ __('ការផ្តល់ជូនមុខវិជ្ជាទាំងអស់សម្រាប់ឆមាស និងឆ្នាំសិក្សានេះ') }}</p>
                    </div>
                </div>

               {{-- Success/Error Messages (Existing) --}}
                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mx-6 mt-6 mb-0" role="alert">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-green-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <p class="font-semibold">{{ __('ជោគជ័យ!') }}</p>
                            <p class="ml-auto">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mx-6 mt-6 mb-0" role="alert">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-red-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <p class="font-semibold">{{ __('បរាជ័យ!') }}</p>
                            <p class="ml-auto">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif
                
                <div class="mt-8">
                    @if ($courseOfferings->isEmpty())
                        <div class="bg-white p-12 rounded-3xl text-center text-gray-400 shadow-xl border border-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-20 w-20 mb-4 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <p class="font-semibold text-lg">{{ __('មិនទាន់មានការផ្តល់ជូនមុខវិជ្ជាណាមួយនៅឡើយទេ។') }}</p>
                            <p class="mt-2 text-sm">{{ __('សូមត្រលប់មកវិញនៅពេលក្រោយដើម្បីមើលការផ្តល់ជូនមុខវិជ្ជាថ្មីៗ។') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach ($courseOfferings as $offering)
                                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 flex flex-col justify-between hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-start mb-6">
                                        <div class="flex-shrink-0 w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10 9 9 9 8 9"></polyline>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-bold text-gray-900 leading-tight">{{ $offering->course->title_km ?? 'N/A' }}</h4>
                                            <p class="text-base text-gray-500 mt-1">{{ $offering->course->title_en ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2 mb-6">
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('លេខកូដមុខវិជ្ជា') }}</span>: <span class="text-gray-600">{{ $offering->course->code ?? 'N/A' }}</span></p>
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('ឆ្នាំសិក្សា') }}</span>: <span class="text-gray-600">{{ $offering->academic_year ?? 'N/A' }}</span></p>
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('ឆមាស') }}</span>: <span class="text-gray-600">{{ $offering->semester ?? 'N/A' }}</span></p>
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('ផ្នែក') }}</span>: <span class="text-gray-600">{{ $offering->section ?? 'N/A' }}</span></p>
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('គ្រូបង្រៀន') }}</span>: <span class="text-gray-600">{{ $offering->lecturer->name ?? 'N/A' }}</span></p>
                                    </div>
                                    {{-- <div class="flex justify-end space-x-3 mt-auto">
                                        <a href="{{ route('professor.view-all-course-offerings', $offering->id) }}" class="p-3 bg-gray-100 rounded-full text-emerald-600 hover:bg-gray-200 transition duration-150 ease-in-out" title="{{ __('មើល') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </div> --}}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                @if ($courseOfferings->hasPages())
                    <div class="mt-8">
                        {{ $courseOfferings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
