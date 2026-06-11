<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between px-4 md:px-6 lg:px-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 leading-tight flex items-center gap-3">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-arrow-right text-purple-600 text-xl"></i>
                    </div>
                    ជំរុញនិស្សិត — {{ $program->name_km }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 ml-12">
                    ជំនាន់ទី{{ $currentYear }} → {{ $willGraduate ? 'បញ្ចប់ការសិក្សា' : 'ជំនាន់ទី'.$nextYear }}
                </p>
            </div>
            <a href="{{ route('admin.progression.index', ['program_id' => $program->id]) }}" class="mt-4 md:mt-0 inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left"></i>
                <span>ត្រឡប់ក្រោយ</span>
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Toast --}}
            @if (session('success') || session('error'))
                <div x-data="{ show: true, progress: 100 }" x-init="let i=setInterval(()=>{progress-=1;if(progress<=0){show=false;clearInterval(i)}},30)" x-show="show" x-transition class="fixed top-6 right-6 z-[9999] w-full max-w-sm">
                    <div class="relative overflow-hidden bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-2xl p-4 ring-1 ring-black/5">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                @if(session('success'))
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-500/10 text-green-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-500/10 text-red-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 pt-0.5">
                                <p class="text-sm font-bold text-gray-900 leading-tight">{{ session('success') ? 'ជោគជ័យ' : 'កំហុស' }}</p>
                                <p class="mt-1 text-sm text-gray-600 leading-relaxed">{{ session('success') ?? session('error') }}</p>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 h-1 bg-green-500 rounded-b-2xl transition-all duration-100" :style="{ width: progress + '%' }"></div>
                    </div>
                </div>
            @endif

            {{-- Info Banner --}}
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl p-6 text-white shadow-lg shadow-purple-500/25">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/20 rounded-xl">
                        <i class="fas fa-info-circle text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">
                            {{ $willGraduate ? 'ការបញ្ចប់ការសិក្សា' : 'ជំរុញទៅជំនាន់ទី'.$nextYear }}
                        </h3>
                        <p class="text-sm text-purple-100 mt-1">
                            {{ $willGraduate
                                ? 'និស្សិតខាងក្រោមបានបញ្ចប់ជំនាន់ចុងក្រោយ និងត្រូវបានបញ្ចប់ការសិក្សា។'
                                : 'និស្សិតខាងក្រោមអាចជំរុញទៅជំនាន់ទី'.$nextYear.'។ និស្សិតដែលមានបរិយាកាស F មិនត្រូវបានជំរុញទេ។'
                            }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Eligible Students --}}
            @if($eligibleStudents->isNotEmpty())
                <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-2xl overflow-hidden ring-1 ring-black/5">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-transparent">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                និស្សិតមានសិទ្ធិជំរុញ — {{ $eligibleStudents->count() }} នាក់
                            </h3>
                            <button onclick="selectAll()" class="text-sm text-green-600 hover:text-green-700 font-bold">
                                <i class="fas fa-check-double mr-1"></i> ជ្រើសរើសទាំងអស់
                            </button>
                        </div>
                    </div>

                    <form id="advanceForm" action="{{ route('admin.progression.executeAdvance') }}" method="POST">
                        @csrf
                        <input type="hidden" name="program_id" value="{{ $program->id }}">

                        {{-- Desktop Table --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50/80">
                                    <tr>
                                        <th class="px-6 py-3 text-center w-12">
                                            <input type="checkbox" id="checkAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ល.រ</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ឈ្មោះ</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">អត្តលេខ</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ជំនាន់</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ស្ថានភាព</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($eligibleStudents as $index => $student)
                                        <tr class="hover:bg-green-50/50 transition">
                                            <td class="px-6 py-4 text-center">
                                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-cb rounded border-gray-300 text-green-600 focus:ring-green-500">
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                        {{ substr($student->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-800">{{ $student->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $student->email ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $student->student_id_code ?? '-' }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                                    <i class="fas fa-graduation-cap"></i>
                                                    {{ $currentYear }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                    <i class="fas fa-check-circle"></i>
                                                    {{ $willGraduate ? 'បញ្ចប់' : 'អាចជំរុញ' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Cards --}}
                        <div class="md:hidden divide-y divide-gray-100">
                            @foreach($eligibleStudents as $student)
                                <div class="px-4 py-3 hover:bg-green-50/50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-cb rounded border-gray-300 text-green-600 focus:ring-green-500">
                                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800">{{ $student->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $student->student_id_code ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Submit Button --}}
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500">
                                    <span id="selectedCount">0</span> និស្សិតត្រូវបានជ្រើសរើស
                                </p>
                                <button type="submit" id="submitBtn"
                                    class="inline-flex items-center gap-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-green-500/25 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0"
                                    disabled>
                                    <i class="fas fa-arrow-right"></i>
                                    {{ $willGraduate ? 'បញ្ចប់ការសិក្សា' : 'ជំរុញទៅជំនាន់ទី'.$nextYear }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Held Back Students --}}
            @if($heldBackStudents->isNotEmpty())
                <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-2xl overflow-hidden ring-1 ring-black/5">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-transparent">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-red-500"></i>
                            និស្សិតមិនអាចជំរុញ — {{ $heldBackStudents->count() }} នាក់
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">និស្សិតទាំងនេះមានបរិយាកាស F ក្នុងវិញ្ញាសារខ្លះ ហើយមិនត្រូវបានជំរុញទេ។</p>
                    </div>

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ល.រ</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ឈ្មោះ</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">អត្តលេខ</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ស្ថានភាព</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($heldBackStudents as $index => $student)
                                    <tr class="hover:bg-red-50/50 transition">
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                    {{ substr($student->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-800">{{ $student->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $student->email ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $student->student_id_code ?? '-' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                                <i class="fas fa-exclamation-circle"></i>
                                                មានបរិយាកាស F
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
                            <div class="px-4 py-3 hover:bg-red-50/50 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $student->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $student->student_id_code ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- No students --}}
            @if($eligibleStudents->isEmpty() && $heldBackStudents->isEmpty())
                <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-2xl p-12 ring-1 ring-black/5 text-center">
                    <div class="p-4 bg-gray-100 rounded-2xl inline-block mb-4">
                        <i class="fas fa-users text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-600">មិនមាននិស្សិត</h3>
                    <p class="text-sm text-gray-400 mt-1">មិនមាននិស្សិតក្នុងជំនាន់នេះសម្រាប់កម្មវិធីនេះទេ។</p>
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
