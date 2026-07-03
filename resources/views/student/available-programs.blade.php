<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('កម្មវិធីសិក្សាសម្រាប់ចុះឈ្មោះ') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 border-b pb-3">
                    {{ __('កម្មវិធីសិក្សាដែលអាចចុះឈ្មោះបាន') }}
                </h3>

                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                        <p class="font-bold">{{ __('ជោគជ័យ!') }}</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                @if (session('info'))
                    <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-4 rounded" role="alert">
                        <p class="font-bold">{{ __('ព័ត៌មាន!') }}</p>
                        <p>{{ session('info') }}</p>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                        <p class="font-bold">{{ __('កំហុស!') }}</p>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if ($availablePrograms->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-inner">
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-xl font-medium">{{ __('បច្ចុប្បន្ននេះ គ្មានកម្មវិធីសិក្សាណាមួយត្រូវបានបើកសម្រាប់ការចុះឈ្មោះដោយខ្លួនឯងទេ។') }}</p>
                        <p class="text-base mt-2">{{ __('សូមពិនិត្យមើលនៅពេលក្រោយ ឬទាក់ទងការិយាល័យរដ្ឋបាលសម្រាប់ព័ត៌មានបន្ថែម។') }}</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($availablePrograms as $program) {{-- 💡 ឥឡូវនេះ loop លើ Programs --}}
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 transform transition-transform duration-200 hover:scale-102 hover:shadow-xl">
                                <h4 class="text-xl font-bold text-emerald-700 dark:text-emerald-400 mb-2">{{ $program->name_km ?? $program->name }}</h4> {{-- 💡 បង្ហាញឈ្មោះ Program --}}
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $program->description_km ?? $program->description }}
                                </p>
                                <p class="mt-4 text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">{{ __('មហាវិទ្យាល័យ៖') }}</span> {{ $program->faculty->name ?? 'N/A' }}
                                </p>
                                <p class="text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">{{ __('នាយកដ្ឋាន៖') }}</span> {{ $program->department->name ?? 'N/A' }}
                                </p>

                                <div class="mt-5 flex justify-end items-center">
                                    <form action="{{ route('student.enroll-program') }}" method="POST"> {{-- 💡 កែតម្រូវ route ទៅ enroll_program --}}
                                        @csrf
                                        <input type="hidden" name="program_id" value="{{ $program->id }}"> {{-- 💡 ប្រើ program_id --}}
                                        <x-primary-button type="submit" class="w-full justify-center">
                                            {{ __('ចុះឈ្មោះកម្មវិធីសិក្សា') }}
                                        </x-primary-button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $availablePrograms->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
