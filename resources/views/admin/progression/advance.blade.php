<x-app-layout>
    {{-- Dark Gradient Header --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-white/10 backdrop-blur rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ __('advance_students_title') }} - {{ $department->name_km }}</h1>
                        <p class="mt-1 text-sm text-slate-400">{{ __('student_list_desc') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.progression.index', ['department_id' => $department->id]) }}" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-white/20 transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('back_2') }}
                </a>
            </div>
        </div>
    </div>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @php
                $yearColors = [
                    1 => 'bg-emerald-50 text-emerald-600',
                    2 => 'bg-amber-50 text-amber-600',
                    3 => 'bg-violet-50 text-violet-600',
                    4 => 'bg-emerald-50 text-emerald-600',
                ];
                $avatarColors = ['bg-emerald-50 text-emerald-600', 'bg-amber-50 text-amber-600', 'bg-violet-50 text-violet-600', 'bg-emerald-50 text-emerald-600'];
            @endphp

            {{-- Section 1: Info --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="h-8 w-8 bg-slate-900 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">①</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('general_info') }}</h3>
                        <p class="text-xs text-gray-400">{{ __('advance_or_graduate') }}</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-3">
                        <div class="h-9 w-9 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('advance_or_graduate') }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ __('final_year_graduates_others_advance') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Eligible Students --}}
            @if($eligibleStudents->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 bg-slate-900 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xs font-bold">②</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">{{ __('eligible_to_advance') }} <span class="text-gray-400 font-normal">· {{ $eligibleStudents->count() }} {{ __('student') }}</span></h3>
                                <p class="text-xs text-gray-400">{{ __('no_f_in_any_assessment') }}</p>
                            </div>
                        </div>
                        <button onclick="selectAll()" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            {{ __('select_all') }}
                        </button>
                    </div>

                    <form id="advanceForm" action="{{ route('admin.progression.executeAdvance') }}" method="POST">
                        @csrf
                        <input type="hidden" name="department_id" value="{{ $department->id }}">

                        {{-- Desktop Table --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr class="border-b border-gray-200">
                                        <th class="px-6 py-3 text-center w-12">
                                            <input type="checkbox" id="checkAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                        </th>
                                        <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">{{ __('no') }}</th>
                                        <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">{{ __('name') }}</th>
                                        <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">{{ __('student_id_3') }}</th>
                                        <th class="px-6 py-3 text-center text-[11px] font-bold text-gray-500 uppercase">{{ __('current_year_2') }}</th>
                                        <th class="px-6 py-3 text-center text-[11px] font-bold text-gray-500 uppercase">{{ __('actions_2') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
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
                                                        {{ __('graduated') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-600">
                                                        {{ __('advance_to_year') }}{{ $nextYr }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Cards --}}
                        <div class="md:hidden divide-y divide-gray-100">
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
                                                    {{ __('year_label') }}{{ $yr }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($isFinalYear)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-600">
                                                {{ __('graduated') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-600">
                                                {{ __('advance') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Submit Button --}}
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500">
                                    <span id="selectedCount">0</span> {{ __('students_selected') }}
                                </p>
                                <button type="submit" id="submitBtn"
                                    class="inline-flex items-center gap-2 bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-medium text-sm shadow-sm shadow-emerald-200 hover:bg-emerald-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    {{ __('advance_selected') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Section 3: Held Back Students --}}
            @if($heldBackStudents->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="h-8 w-8 bg-slate-900 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold">③</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('cannot_advance') }} <span class="text-gray-400 font-normal">· {{ $heldBackStudents->count() }} {{ __('student') }}</span></h3>
                            <p class="text-xs text-gray-400">{{ __('has_f_in_some_assessment') }}</p>
                        </div>
                    </div>

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr class="border-b border-gray-200">
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">{{ __('no') }}</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">{{ __('name') }}</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">{{ __('student_id_3') }}</th>
                                    <th class="px-6 py-3 text-center text-[11px] font-bold text-gray-500 uppercase">{{ __('current_year_2') }}</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">{{ __('status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
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
                                                {{ __('has_f_grade') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden divide-y divide-gray-100">
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
                                            <p class="text-xs text-gray-400">{{ $student->student_id_code ?? '-' }} · {{ __('year_label') }}{{ $student->computed_year_level }}</p>
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
                <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
                    <div class="h-16 w-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('no_students_2') }}</h3>
                    <p class="text-sm text-gray-400 mt-1">{{ __('no_students_for_program') }}</p>
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
