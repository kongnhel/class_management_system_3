<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="text-center lg:text-left">
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight tracking-tight">
                        {{ __('ព័ត៌មានការដាក់ស្នើ') }}
                    </h2>
                    <div class="flex items-center justify-center lg:justify-start mt-1 text-slate-500 space-x-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $assignment->title_km ?? $assignment->title_en }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center lg:justify-end gap-3">
                    <a href="{{ route('professor.submissions.index', ['offering_id' => $courseOffering->id, 'assignment_id' => $assignment->id]) }}"
                        class="group inline-flex items-center justify-center px-4 py-2.5 bg-slate-600 hover:bg-slate-700 text-white rounded-2xl font-bold text-xs transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('ត្រឡប់ទៅបញ្ជី') }}
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-6" role="alert">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-green-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p class="font-semibold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6" role="alert">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-red-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="font-semibold">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Student Info Card --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ __('ព័ត៌មាននិស្សិត') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('ឈ្មោះ') }}</p>
                        <p class="font-semibold text-gray-900">{{ $submission->student->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('លេខសម្គាល់និស្សិត') }}</p>
                        <p class="font-semibold text-gray-900">{{ $submission->student->student_id_code ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('កាលបរិច្ឆេទដាក់ស្នើ') }}</p>
                        <p class="font-semibold text-gray-900">{{ $submission->submission_date->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('ស្ថានភាព') }}</p>
                        @if($submission->grade_received !== null)
                            <span class="px-2 py-1 text-xs font-bold rounded-lg bg-green-100 text-green-800">{{ __('បានពិន្ទុ') }}</span>
                        @else
                            <span class="px-2 py-1 text-xs font-bold rounded-lg bg-yellow-100 text-yellow-800">{{ __('មិនទាន់ពិន្ទុ') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Submitted File --}}
            @if($submission->file_path)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('ឯកសារដែលបានដាក់ស្នើ') }}
                    </h3>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ basename($submission->file_path) }}</p>
                                <p class="text-xs text-gray-500">{{ __('ឯកសារដែលបានដាក់ស្នើ') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('professor.submissions.download', ['offering_id' => $courseOffering->id, 'assignment_id' => $assignment->id, 'submission_id' => $submission->id]) }}"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            {{ __('ទាញយកឯកសារ') }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- Grading Form --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    {{ __('ដាក់ពិន្ទុ') }}
                </h3>
                <form action="{{ route('professor.submissions.grade', ['offering_id' => $courseOffering->id, 'assignment_id' => $assignment->id, 'submission_id' => $submission->id]) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="grade_received" class="block text-sm font-medium text-gray-700">{{ __('ពិន្ទុ') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1 flex items-center">
                                <input type="number" id="grade_received" name="grade_received" 
                                    value="{{ $submission->grade_received ?? '' }}" 
                                    min="0" max="{{ $assignment->max_score }}" required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-lg font-bold">
                                <span class="ml-3 text-gray-500 font-medium">/ {{ $assignment->max_score }}</span>
                            </div>
                            @error('grade_received')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="feedback" class="block text-sm font-medium text-gray-700">{{ __('មតិយោបល់') }}</label>
                        <textarea id="feedback" name="feedback" rows="4"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                            placeholder="{{ __('សូមបញ្ចូលមតិយោបល់សម្រាប់និស្សិត...') }}">{{ $submission->feedback ?? '' }}</textarea>
                        @error('feedback')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('professor.submissions.index', ['offering_id' => $courseOffering->id, 'assignment_id' => $assignment->id]) }}"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all">
                            {{ __('បោះបង់') }}
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-sm transition-all">
                            {{ __('រក្សាទុកពិន្ទុ') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
