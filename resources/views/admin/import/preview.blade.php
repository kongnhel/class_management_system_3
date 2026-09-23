<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.import.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 leading-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600">
                        <i class="fas fa-eye"></i>
                    </span>
                    {{ __('import_preview') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ count($previewData) }}</p>
                            <p class="text-sm text-gray-500">{{ __('total_data') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-emerald-600">{{ $validCount }}</p>
                            <p class="text-sm text-gray-500">{{ __('valid') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-red-600">{{ $errorCount }}</p>
                            <p class="text-sm text-gray-500">{{ __('has_errors') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Errors List --}}
            @if($errors->isNotEmpty())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                <h3 class="text-sm font-bold text-red-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ __('errors_to_fix') }}
                </h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($errors as $error)
                    <div class="flex items-start gap-2 text-sm text-red-600">
                        <span class="font-bold">Row {{ $error['row'] }}:</span>
                        <span>{{ implode(', ', $error['errors']) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Preview Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-table text-emerald-600"></i>
                        {{ __('found_data') }}
                    </h3>
                    <div class="flex items-center gap-2">
                        <button onclick="toggleAll(true)" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition">
                            {{ __('select_all') }}
                        </button>
                        <button onclick="toggleAll(false)" class="text-xs text-gray-500 hover:text-gray-700 font-semibold px-3 py-1.5 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                            {{ __('deselect_all') }}
                        </button>
                    </div>
                </div>

                <form id="importForm" action="{{ route('admin.import.users') }}" method="POST">
                    @csrf
                    <input type="hidden" name="import_token" value="{{ $previewToken }}">
                    <input type="hidden" name="role" value="{{ $role }}">
                    <input type="hidden" name="department_id" value="{{ $departmentId }}">
                    <input type="hidden" name="generation" value="{{ $generation }}">
                    <input type="hidden" name="duplicate_action" value="{{ $duplicateAction }}">
                    <input type="hidden" name="enroll_in_matching_courses" value="{{ $enrollInMatchingCourses ? 1 : 0 }}">

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-3 text-center w-10">
                                        <input type="checkbox" id="checkAll" onchange="toggleAll(this.checked)" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('no') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('name') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('email') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('khmer_name') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ __('gender') }}</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">{{ __('status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($previewData as $student)
                                <tr class="{{ $student['status'] === 'error' ? 'bg-red-50/50' : 'hover:bg-gray-50' }} transition">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" name="student_ids[]" value="{{ $student['row'] }}" 
                                            class="student-cb rounded border-gray-300 text-emerald-500 focus:ring-emerald-500"
                                            {{ $student['status'] === 'error' ? 'disabled' : '' }}>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $student['row'] }}</td>
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-gray-900">{{ $student['name'] }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $student['email'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $student['full_name_km'] ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $student['gender'] ?: '—' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($student['status'] === 'valid')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                 <i class="fas fa-check text-[10px]"></i> {{ __('valid') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                <i class="fas fa-times text-[10px]"></i> {{ __('errors') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Actions --}}
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                        <a href="{{ route('admin.import.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('back') }}
                        </a>
                        <div class="flex items-center gap-3">
                            <p class="text-sm text-gray-500">
                                {{ __('import') }} <span id="selectedCount" class="font-bold text-emerald-600">0</span> {{ __('people') }}
                            </p>
                            <button type="submit" id="submitBtn" disabled
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-600 rounded-xl font-bold text-white hover:from-emerald-700 hover:to-emerald-700 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-file-import"></i> {{ __('import') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleAll(checked) {
            document.querySelectorAll('.student-cb:not(:disabled)').forEach(cb => cb.checked = checked);
            updateCount();
        }

        function updateCount() {
            const count = document.querySelectorAll('.student-cb:checked').length;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('submitBtn').disabled = count === 0;
        }

        document.querySelectorAll('.student-cb').forEach(cb => {
            cb.addEventListener('change', updateCount);
        });

        // Initial count
        updateCount();
    </script>
</x-app-layout>
