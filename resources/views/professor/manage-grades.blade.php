<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('គ្រប់គ្រងពិន្ទុសម្រាប់មុខវិជ្ជា') }} {{ $courseOffering->course->course_name_km ?? 'N/A' }}
        </h2>
    </x-slot>
    
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
    @if ($errors->any() && !$errors->has('grades')) {{-- Display general validation errors (if any, excluding grade-specific ones) --}}
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mx-6 mt-6 mb-0" role="alert">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-red-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="font-semibold">{{ __('បរាជ័យក្នុងការរក្សាទុក!') }}</p>
                    <p class="text-sm mt-1">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
                <h3 class="text-3xl font-bold text-gray-800 mb-8 flex items-center">
                    <i class="fas fa-star mr-3 text-red-600"></i>{{ __('គ្រប់គ្រងពិន្ទុសម្រាប់') }} {{ $courseOffering->course->course_name_km ?? $courseOffering->course->course_name_en ?? 'N/A' }} ({{ $courseOffering->academic_year }} - {{ $courseOffering->semester }})
                </h3>

                <div class="mb-6 bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                    <p class="text-lg font-medium text-emerald-800">{{ __('ព័ត៌មានវគ្គសិក្សា:') }}</p>
                    <ul class="list-disc list-inside text-gray-700 mt-2">
                        <li>{{ __('លេខកូដមុខវិជ្ជា:') }} <span class="font-semibold">{{ $courseOffering->course->course_code ?? 'N/A' }}</span></li>
                        <li>{{ __('គ្រូបង្រៀន:') }} <span class="font-semibold">{{ $courseOffering->lecturer->full_name_km ?? $courseOffering->lecturer->name ?? 'N/A' }}</span></li>
                        <li>{{ __('ចំនួនសិស្ស:') }} <span class="font-semibold">{{ $courseOffering->studentCourseEnrollments->count() }}</span></li>
                    </ul>
                </div>

                <!-- GRADES SUBMISSION FORM -->
                <form method="POST" action="{{ route('grades.storeOrUpdate', $courseOffering->id) }}">
                    @csrf
                    <h4 class="text-2xl font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-edit mr-2 text-emerald-600"></i>{{ __('បញ្ចូល/កែសម្រួលពិន្ទុតាមកិច្ចការ') }}
                    </h4>
                    
                    <div class="flex justify-end mb-4">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-colors duration-200">
                            {{ __('រក្សាទុកពិន្ទុទាំងអស់') }}
                        </button>
                    </div>

                    <div class="overflow-x-auto bg-gray-50 rounded-lg shadow-inner mb-6">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr class="bg-gray-200 text-gray-700 uppercase text-xs font-semibold">
                                    <th class="py-3 px-4 text-left rounded-tl-lg w-16">{{ __('លេខរៀង') }}</th>
                                    <th class="py-3 px-4 text-left w-1/4">{{ __('ឈ្មោះសិស្ស') }}</th>
                                    
                                    {{-- Dynamically generate columns for each assignment --}}
                                    @foreach ($assignments as $assignment)
                                        <th class="py-3 px-4 text-center border-l border-gray-300 min-w-[120px]">
                                            <div class="font-bold text-sm text-emerald-600">{{ $assignment->assignment_name }}</div>
                                            <div class="text-gray-500 font-normal">({{ $assignment->max_points }} pts)</div>
                                            <div class="text-red-500 font-normal">({{ $assignment->component->weight_percentage }}%)</div>
                                        </th>
                                    @endforeach

                                    <th class="py-3 px-4 text-center bg-gray-300 text-gray-900 rounded-tr-lg w-24">
                                        {{ __('ពិន្ទុចុងក្រោយ') }} (%)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $student) {{-- Assuming controller provides an array of students with pre-loaded grades --}}
                                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="py-3 px-4 text-gray-800 font-medium">{{ $loop->iteration }}</td>
                                        <td class="py-3 px-4 text-gray-600 whitespace-nowrap">
                                            {{ $student->full_name_km ?? $student->full_name_en ?? $student->name ?? 'N/A' }}
                                        </td>
                                        
                                        {{-- Dynamic Score Input for Each Assignment --}}
                                        @php
                                            // Calculate the student's current final score percentage dynamically for display
                                            $totalWeightedScore = 0;
                                        @endphp

                                        @foreach ($assignments as $assignment)
                                            @php
                                                // Check if the grade for this student/assignment exists
                                                // The 'grades' relation is pre-loaded in the controller
                                                $grade = $student->grades->where('assignment_id', $assignment->id)->first();
                                                $scoreReceived = $grade ? $grade->score_received : '';
                                                
                                                // Get validation error key for this specific field
                                                $errorKey = 'grades.' . $student->id . '.' . $assignment->id;

                                                // Calculate for Final Grade Display
                                                if($scoreReceived !== '' && $assignment->max_points > 0) {
                                                    $normalizedScore = ($scoreReceived / $assignment->max_points) * 100;
                                                    $totalWeightedScore += $normalizedScore * ($assignment->component->weight_percentage / 100);
                                                }
                                            @endphp
                                            <td class="py-3 px-2 text-center border-l border-gray-200">
                                                <input
                                                    type="number"
                                                    name="grades[{{ $student->id }}][{{ $assignment->id }}]"
                                                    class="w-full p-1 border border-gray-300 rounded-md shadow-sm text-center text-sm focus:ring-emerald-500 focus:border-emerald-500 
                                                        @error($errorKey) border-red-500 bg-red-50 @enderror" {{-- Highlight input on error --}}
                                                    placeholder="0.00"
                                                    value="{{ old($errorKey, $scoreReceived) }}" {{-- Use old() to retain input data on validation failure --}}
                                                    min="0"
                                                    max="{{ $assignment->max_points }}"
                                                    step="0.01"
                                                >
                                                {{-- Display specific validation error for this input --}}
                                                @error($errorKey)
                                                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                                @enderror
                                                {{-- Optional: Show max points below the input for clarity --}}
                                                <p class="text-xs text-gray-400 mt-1">/ {{ $assignment->max_points }}</p>
                                            </td>
                                        @endforeach
                                        
                                        {{-- FINAL GRADE COLUMN --}}
                                        <td class="py-3 px-4 text-center bg-gray-100 text-lg font-extrabold text-emerald-600">
                                            {{ number_format($totalWeightedScore, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + $assignments->count() }}" class="py-4 px-6 text-center text-gray-500">
                                            {{ __('មិនទាន់មានសិស្សចុះឈ្មោះ ឬកិច្ចការវាយតម្លៃសម្រាប់វគ្គសិក្សានេះនៅឡើយទេ។') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-colors duration-200">
                            {{ __('រក្សាទុកពិន្ទុទាំងអស់') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
