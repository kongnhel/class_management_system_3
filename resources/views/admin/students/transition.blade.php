<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-200">

                <div class="mb-8 pb-4 border-b border-gray-200">
                    <h2 class="font-extrabold text-4xl text-gray-900 leading-tight">
                        {{ __('ផ្ទេរសិស្សទៅកម្មវិធីសិក្សាបរិញ្ញាបត្រ') }}
                    </h2>
                    <p class="mt-2 text-lg text-gray-500">
                        {{ __('ផ្ទេរសិស្ស') }} <span class="font-medium text-gray-700">{{ $student->name }}</span> {{ __('ពីកម្មវិធីសិក្សាបរិញ្ញាបត្ររងទៅកម្មវិធីសិក្សាបរិញ្ញាបត្រ។') }}
                    </p>
                </div>

                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl relative mb-8 flex items-center space-x-3 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="block sm:inline font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl relative mb-8 flex items-center space-x-3 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586l-1.293-1.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="block sm:inline font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Student Info --}}
                <div class="bg-gray-50 rounded-2xl p-6 mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('ព័ត៌មានសិស្ស') }}</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">{{ __('ឈ្មោះ') }}:</span>
                            <span class="font-medium text-gray-800">{{ $student->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('កូដសិស្ស') }}:</span>
                            <span class="font-medium text-gray-800">{{ $student->student_id_code }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('កម្មវិធីសិក្សាបច្ចុប្បន្ន') }}:</span>
                            <span class="font-medium text-gray-800">{{ $student->program->name_km ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('ជំនាន់') }}:</span>
                            <span class="font-medium text-gray-800">{{ $student->generation }}</span>
                        </div>
                    </div>
                </div>

                {{-- Transition Form --}}
                @if($transitionPrograms->isEmpty())
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-6 py-4 rounded-2xl mb-8">
                        <p>{{ __('មិនមានកម្មវិធីសិក្សាផ្លូវបន្តសម្រាប់សិស្សនេះទេ។ សូមពិនិត្យមើលថាកម្មវិធីសិក្សាបច្ចុប្បន្នមានកំណត់ផ្លូវបន្ត (pathway) ទៅកម្មវិធីសិក្សាបរិញ្ញាបត្រ។') }}</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.students.transition.store', $student->id) }}">
                        @csrf

                        <div class="mb-6">
                            <label for="bachelor_program_id" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('ជ្រើសរើសកម្មវិធីសិក្សាបរិញ្ញាបត្រ') }}</label>
                            <select id="bachelor_program_id" name="bachelor_program_id" class="form-select w-full rounded-xl border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 transition duration-150 ease-in-out" required>
                                <option value="">{{ __('ជ្រើសរើសកម្មវិធីសិក្សា') }}</option>
                                @foreach ($transitionPrograms as $program)
                                    <option value="{{ $program->id }}" {{ old('bachelor_program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name_km }} ({{ $program->name_en }}) - {{ $program->duration_years }} ឆ្នាំ
                                    </option>
                                @endforeach
                            </select>
                            @error('bachelor_program_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-8">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div class="text-sm text-blue-700">
                                    <p class="font-semibold">{{ __('ព័ត៌មានសំខាន់៖') }}</p>
                                    <ul class="mt-1 list-disc list-inside space-y-1">
                                        <li>{{ __('សិស្សនឹងចាប់ផ្តើមពីឆ្នាំទី ៣ ក្នុងកម្មវិធីសិក្សាបរិញ្ញាបត្រ។') }}</li>
                                        <li>{{ __('កម្មវិធីសិក្សាបរិញ្ញាបត្ររងបច្ចុប្បន្ននឹងត្រូវបញ្ចប់។') }}</li>
                                        <li>{{ __('សិស្សនឹងត្រូវបានផ្ទេរទៅកម្មវិធីសិក្សាថ្មីដោយស្វ័យប្រវត្តិ។') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <a href="{{ route('admin.show-user', $student->id) }}" class="px-6 py-3 text-gray-600 font-semibold rounded-full hover:bg-gray-200 transition duration-300">
                                {{ __('ត្រឡប់ក្រោយ') }}
                            </a>

                            <button type="submit" onclick="return confirm('{{ __('តើអ្នកប្រាកដជាចង់ផ្ទេរសិស្សនេះមែនទេ?') }}')" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold rounded-full shadow-lg hover:from-blue-600 hover:to-blue-700 transition duration-300 transform hover:scale-105 flex items-center space-x-2">
                                <span>{{ __('ផ្ទេរសិស្ស') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
