<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-800 leading-tight">
            {{ __('គ្រប់គ្រងមុខវិជ្ជា') }}
        </h2>
        <p class="mt-1 text-lg text-gray-500">{{ __('បញ្ជីមុខវិជ្ជាទាំងអស់នៅក្នុងប្រព័ន្ធ') }}</p>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-200 transition-all duration-300 transform hover:shadow-3xl">

                <div class="flex flex-col md:flex-row items-center justify-between mb-10 pb-4 border-b border-gray-200">
                    <div>
                        <h2 class="font-extrabold text-4xl text-gray-900 leading-tight">
                            {{ __('បញ្ជីមុខវិជ្ជា') }}
                        </h2>
                        <p class="mt-2 text-lg text-gray-500">{{ __('បញ្ជីឈ្មោះមុខវិជ្ជាទាំងអស់នៅក្នុងប្រព័ន្ធ') }}</p>
                    </div>
                    {{-- <div class="mt-4 md:mt-0">
                        <a href="{{ route('admin.create-course') }}" class="px-8 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white font-bold rounded-full shadow-lg hover:from-green-600 hover:to-teal-700 transition duration-300 transform hover:scale-105 flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ __('បន្ថែមមុខវិជ្ជាថ្មី') }}</span>
                        </a>
                    </div> --}}
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
                    @if ($courses->isEmpty())
                        <div class="bg-white p-12 rounded-3xl text-center text-gray-400 shadow-xl border border-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-20 w-20 mb-4 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <p class="font-semibold text-lg">{{ __('មិនទាន់មានមុខវិជ្ជាណាមួយត្រូវបានបង្កើតនៅឡើយទេ។') }}</p>
                            <p class="mt-2 text-sm">{{ __('ចាប់ផ្តើមដោយបន្ថែមមុខវិជ្ជាដំបូងរបស់អ្នកដើម្បីគ្រប់គ្រងកម្មវិធីសិក្សា។') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach ($courses as $course)
                                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 flex flex-col justify-between hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex flex-col items-start mb-6">
                                        <div class="flex-shrink-0 w-14 h-14 rounded-full bg-green-50 text-green-600 flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10 9 9 9 8 9"></polyline>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-bold text-gray-900 leading-tight">{{ $course->title_km ?? 'N/A' }}</h4>
                                            <p class="text-base text-gray-500 mt-1">{{ $course->title_en ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2 mb-6">
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('លេខកូដ') }}</span>: <span class="text-gray-600">{{ $course->code ?? 'N/A' }}</span></p>
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('ក្រេឌីត') }}</span>: <span class="text-gray-600">{{ $course->credits ?? 'N/A' }}</span></p>
                                        <p class="text-gray-700 font-medium"><span class="font-bold text-gray-800">{{ __('ដេប៉ាតឺម៉ង់') }}</span>: <span class="text-gray-600">{{ $course->department->name_km ?? 'N/A' }}</span></p>
                                    </div>
                                    <div class="flex justify-end space-x-3 mt-auto">
                                        {{-- <a href="{{ route('professor.view-courses', $course->id) }}" class="p-3 bg-gray-100 rounded-full text-emerald-600 hover:bg-gray-200 transition duration-150 ease-in-out" title="{{ __('មើល') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a> --}}
                                        {{-- <a href="{{ route('admin.edit-course', $course->id) }}" class="p-3 bg-gray-100 rounded-full text-yellow-600 hover:bg-gray-200 transition duration-150 ease-in-out" title="{{ __('កែប្រែ') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.delete-course', $course->id) }}" method="POST" onsubmit="return confirm('{{ __('តើអ្នកពិតជាចង់លុបមុខវិជ្ជានេះមែនទេ? វានឹងលុបកម្មវិធីសិក្សាដែលពាក់ព័ន្ធទាំងអស់។') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-3 bg-gray-100 rounded-full text-red-600 hover:bg-gray-200 transition duration-150 ease-in-out" title="{{ __('លុប') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form> --}}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                @if ($courses->hasPages())
                    <div class="mt-8">
                        {{ $courses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
