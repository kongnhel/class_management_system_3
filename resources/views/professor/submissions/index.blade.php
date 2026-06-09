<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="text-center lg:text-left">
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight tracking-tight">
                        {{ __('ការដាក់ស្នើសម្រាប់') }} {{ $assignment->title_km ?? $assignment->title_en }}
                    </h2>
                    <div class="flex items-center justify-center lg:justify-start mt-1 text-slate-500 space-x-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center lg:justify-end gap-3">
                    <a href="{{ route('professor.manage-assignments', ['offering_id' => $courseOffering->id]) }}"
                        class="group inline-flex items-center justify-center px-4 py-2.5 bg-slate-600 hover:bg-slate-700 text-white rounded-2xl font-bold text-xs transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('ត្រឡប់ទៅបញ្ជីកិច្ចការ') }}
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">{{ __('សរុបការដាក់ស្នើ') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $submissions->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">{{ __('បានពិន្ទុ') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $submissions->where('grade_received', '!=', null)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">{{ __('មិនទាន់ពិន្ទុ') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $submissions->where('grade_received', null)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search and Filter --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
                <form action="{{ route('professor.submissions.index', ['offering_id' => $courseOffering->id, 'assignment_id' => $assignment->id]) }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ស្វែងរកនិស្សិត...') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>
                    <div class="w-full md:w-48">
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="">{{ __('ទាំងអស់') }}</option>
                            <option value="graded" {{ request('status') === 'graded' ? 'selected' : '' }}>{{ __('បានពិន្ទុ') }}</option>
                            <option value="ungraded" {{ request('status') === 'ungraded' ? 'selected' : '' }}>{{ __('មិនទាន់ពិន្ទុ') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition-all">
                        {{ __('ស្វែងរក') }}
                    </button>
                </form>
            </div>

            {{-- Submissions Table --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ល.រ') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('និស្សិត') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('កាលបរិច្ឆេទដាក់ស្នើ') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ឯកសារ') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ពិន្ទុ') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ស្ថានភាព') }}</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('សកម្មភាព') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($submissions as $index => $submission)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $submissions->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-3">
                                                <p class="text-sm font-semibold text-gray-900">{{ $submission->student->name ?? 'N/A' }}</p>
                                                <p class="text-xs text-gray-500">{{ $submission->student->student_id_code ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $submission->submission_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($submission->file_path)
                                            <a href="{{ route('professor.submissions.download', ['offering_id' => $courseOffering->id, 'assignment_id' => $assignment->id, 'submission_id' => $submission->id]) }}"
                                                class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-xs font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                                {{ __('ទាញយក') }}
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-xs">---</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($submission->grade_received !== null)
                                            <span class="px-2 py-1 text-xs font-bold rounded-lg bg-green-100 text-green-800">
                                                {{ $submission->grade_received }}/{{ $assignment->max_score }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">---</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($submission->grade_received !== null)
                                            <span class="px-2 py-1 text-xs font-bold rounded-lg bg-green-100 text-green-800">{{ __('បានពិន្ទុ') }}</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-bold rounded-lg bg-yellow-100 text-yellow-800">{{ __('មិនទាន់ពិន្ទុ') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('professor.submissions.show', ['offering_id' => $courseOffering->id, 'assignment_id' => $assignment->id, 'submission_id' => $submission->id]) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-xs font-medium">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            {{ __('មើល') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-gray-500 font-medium">{{ __('មិនមានការដាក់ស្នើទេ') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($submissions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $submissions->appends(['search' => request('search'), 'status' => request('status')])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
