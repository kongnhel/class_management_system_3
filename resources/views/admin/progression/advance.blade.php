<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between px-4 md:px-6 lg:px-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">ជំរុញនិស្សិត — {{ $program->name_km }}</h2>
                <p class="mt-1 text-sm text-gray-400">ជ្រើសរើសនិស្សិតដែលចង់ជំរុញ ឬបញ្ចប់ការសិក្សា</p>
            </div>
            <a href="{{ route('admin.progression.index', ['program_id' => $program->id]) }}" class="mt-4 md:mt-0 inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                ត្រឡប់ក្រោយ
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Info Banner --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="h-9 w-9 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">ជំរុញ ឬបញ្ចប់ការសិក្សា</h3>
                        <p class="text-sm text-gray-500 mt-1">និស្សិតឆ្នាំទី{{ $maxYear }}នឹងត្រូវបញ្ចប់ការសិក្សា។ និស្សិតឆ្នាំផ្សេងទៀតនឹងត្រូវជំរុញទៅឆ្នាំបន្ទាប់។</p>
                    </div>
                </div>
            </div>

            {{-- Eligible Students --}}
            @php
                $yearColors = [
                    1 => 'bg-emerald-50 text-emerald-600',
                    2 => 'bg-amber-50 text-amber-600',
                    3 => 'bg-violet-50 text-violet-600',
                    4 => 'bg-emerald-50 text-emerald-600',
                ];
                $avatarColors = ['bg-emerald-50 text-emerald-600', 'bg-amber-50 text-amber-600', 'bg-violet-50 text-violet-600', 'bg-emerald-50 text-emerald-600'];
            @endphp
            @if($eligibleStudents->isNotEmpty())
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">និស្សិតមានសិទ្ធិជំរុញ <span class="text-gray-400 font-normal">· {{ $eligibleStudents->count() }} នាក់</span></h3>
                            <button onclick="selectAll()" class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">
                                ជ្រើសរើសទាំងអស់
                            </button>
                        </div>
                    </div>

                    <form id="advanceForm" action="{{ route('admin.progression.executeAdvance') }}" method="POST">
                        @csrf
                        <input type="hidden" name="program_id" value="{{ $program->id }}">

                        {{-- Desktop Table --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="px-6 py-3 text-center w-12">
                                            <input type="checkbox" id="checkAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ល.រ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ឈ្មោះ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">អត្តលេខ</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-400">ឆ្នាំបច្ចុប្បន្ន</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-400">សកម្មភាព</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($eligibleStudents as $index => $student)
                                        @php
                                            $yr = $student->computed_year_level;
                                            $isFinalYear = $yr >= $maxYear;
                                            $nextYr = $isFinalYear ? null : $yr + 1;
                                            $yc = $yearColors[$yr] ?? $yearColors[4];
                                            $avatarColor = $avatarColors[$yr % 4];
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-3.5 text-center">
                                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-cb rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                            </td>
                                            <td class="px-6 py-3.5 text-sm text-gray-400">{{ $index + 1 }}</td>
                                            <td class="px-6 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded-lg {{ $avatarColor }} flex items-center justify-center font-semibold text-xs">
                                                        {{ mb_substr($student->name, 0, 1, 'UTF-8') }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                                        <p class="text-xs text-gray-400">{{ $student->email ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-3.5 text-sm text-gray-700 font-medium">{{ $student->student_id_code ?? '-' }}</td>
                                            <td class="px-6 py-3.5 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $yc }}">
                                                    {{ $yr }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3.5 text-center">
                                                @if($isFinalYear)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-600">
                                                        បញ្ចប់ការសិក្សា
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-600">
                                                        ជំរុញទៅឆ្នាំទី{{ $nextYr }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Cards --}}
                        <div class="md:hidden divide-y divide-gray-50">
                            @foreach($eligibleStudents as $student)
                                @php
                                    $yr = $student->computed_year_level;
                                    $isFinalYear = $yr >= $maxYear;
                                    $nextYr = $isFinalYear ? null : $yr + 1;
                                    $yc = $yearColors[$yr] ?? $yearColors[4];
                                    $avatarColor = $avatarColors[$yr % 4];
                                @endphp
                                <div class="px-4 py-3 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-cb rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                            <div class="h-8 w-8 rounded-lg {{ $avatarColor }} flex items-center justify-center font-semibold text-xs">
                                                {{ mb_substr($student->name, 0, 1, 'UTF-8') }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $student->student_id_code ?? '-' }}</p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium {{ $yc }} mt-1">
                                                    ឆ្នាំទី{{ $yr }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($isFinalYear)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-600">
                                                បញ្ចប់
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-600">
                                                ជំរុញ
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Submit Button --}}
                        <div class="px-6 py-4 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500">
                                    <span id="selectedCount">0</span> និស្សិតត្រូវបានជ្រើសរើស
                                </p>
                                <button type="submit" id="submitBtn"
                                    class="inline-flex items-center gap-2 bg-emerald-500 text-white px-6 py-2.5 rounded-xl font-medium text-sm shadow-sm shadow-emerald-200 hover:bg-emerald-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    ជំរុញនិស្សិតដែលបានជ្រើសរើស
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Held Back Students --}}
            @if($heldBackStudents->isNotEmpty())
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900">និស្សិតមិនអាចជំរុញ <span class="text-gray-400 font-normal">· {{ $heldBackStudents->count() }} នាក់</span></h3>
                        <p class="text-xs text-gray-400 mt-0.5">និស្សិតទាំងនេះមានបរិយាកាស F ក្នុងវិញ្ញាសារខ្លះ</p>
                    </div>

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ល.រ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ឈ្មោះ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">អត្តលេខ</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-400">ឆ្នាំបច្ចុប្បន្ន</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ស្ថានភាព</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($heldBackStudents as $index => $student)
                                    @php
                                        $yr = $student->computed_year_level;
                                        $avatarColor = $avatarColors[$yr % 4];
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3.5 text-sm text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-lg {{ $avatarColor }} flex items-center justify-center font-semibold text-xs">
                                                    {{ mb_substr($student->name, 0, 1, 'UTF-8') }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                                    <p class="text-xs text-gray-400">{{ $student->email ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3.5 text-sm text-gray-700 font-medium">{{ $student->student_id_code ?? '-' }}</td>
                                        <td class="px-6 py-3.5 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-600">
                                                {{ $yr }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-600">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                                មានបរិយាកាស F
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden divide-y divide-gray-50">
                        @foreach($heldBackStudents as $student)
                            @php $avatarColor = $avatarColors[$student->computed_year_level % 4]; @endphp
                            <div class="px-4 py-3 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-lg {{ $avatarColor }} flex items-center justify-center font-semibold text-xs">
                                            {{ mb_substr($student->name, 0, 1, 'UTF-8') }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $student->student_id_code ?? '-' }} · ឆ្នាំទី{{ $student->computed_year_level }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-600">
                                        F
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- No students --}}
            @if($eligibleStudents->isEmpty() && $heldBackStudents->isEmpty())
                <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center">
                    <div class="h-12 w-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">មិនមាននិស្សិត</h3>
                    <p class="text-sm text-gray-400 mt-1">មិនមាននិស្សិតសម្រាប់កម្មវិធីនេះទេ។</p>
                </div>
            @endif

        </div>
    </div>

    <script>
        function toggleAll(source) {
            document.querySelectorAll('.student-cb').forEach(cb => cb.checked = source.checked);
            updateCount();
        }
        function selectAll() {
            document.querySelectorAll('.student-cb').forEach(cb => cb.checked = true);
            document.getElementById('checkAll').checked = true;
            updateCount();
        }
        function updateCount() {
            const count = document.querySelectorAll('.student-cb:checked').length;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('submitBtn').disabled = count === 0;
        }
        document.querySelectorAll('.student-cb').forEach(cb => cb.addEventListener('change', updateCount));
    </script>
</x-app-layout>
