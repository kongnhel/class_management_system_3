<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between px-4 md:px-6 lg:px-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 leading-tight flex items-center gap-3">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                    </div>
                    ការឃ្លាំងមើលជំនាន់និស្សិត
                </h2>
                <p class="mt-1 text-sm text-gray-500 ml-12">គ្រប់គ្រង និងឃ្លាំងមើលជំនាន់និស្សិតតាមកម្មវិធីសិក្សា</p>
            </div>
            <a href="{{ route('admin.manage-users') }}" class="mt-4 md:mt-0 inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
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

            {{-- Program Selector --}}
            <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-2xl p-6 ring-1 ring-black/5">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-filter text-purple-500 mr-2"></i>
                    ជ្រើសរើសកម្មវិធីសិក្សា
                </h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($programs as $prog)
                        <a href="{{ route('admin.progression.index', ['program_id' => $prog->id]) }}"
                           class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 {{ $program->id === $prog->id ? 'bg-purple-600 text-white shadow-lg shadow-purple-500/25' : 'bg-gray-100 text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                            {{ $prog->name_km }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Year Level Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php $colors = ['bg-blue-500', 'bg-indigo-500', 'bg-purple-500', 'bg-pink-500']; @endphp
                @for($year = 1; $year <= $program->duration_years; $year++)
                    <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-2xl p-6 ring-1 ring-black/5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-xl {{ $colors[($year - 1) % 4] }}/10">
                                <i class="fas fa-graduation-cap {{ $colors[($year - 1) % 4] }} text-xl"></i>
                            </div>
                            <span class="text-3xl font-bold text-gray-800">{{ $summary[$year]['count'] }}</span>
                        </div>
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">ជំនាន់ទី{{ $year }}</h4>
                        <p class="text-xs text-gray-400 mt-1">{{ $summary[$year]['count'] }} និស្សិត</p>
                    </div>
                @endfor

                {{-- Graduated --}}
                <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-2xl p-6 ring-1 ring-black/5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl bg-green-500/10">
                            <i class="fas fa-user-check text-green-500 text-xl"></i>
                        </div>
                        <span class="text-3xl font-bold text-gray-800">{{ $summary['graduated']['count'] }}</span>
                    </div>
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">បញ្ចប់ការសិក្សា</h4>
                    <p class="text-xs text-gray-400 mt-1">{{ $summary['graduated']['count'] }} និស្សិត</p>
                </div>
            </div>

            {{-- Student Lists --}}
            @for($year = 1; $year <= $program->duration_years; $year++)
                @if($summary[$year]['count'] > 0)
                    <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-2xl overflow-hidden ring-1 ring-black/5">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-{{ ['blue', 'indigo', 'purple', 'pink'][($year - 1) % 4] }}-50 to-transparent">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-graduation-cap text-{{ ['blue', 'indigo', 'purple', 'pink'][($year - 1) % 4] }}-500"></i>
                                ជំនាន់ទី{{ $year }} — {{ $summary[$year]['count'] }} និស្សិត
                            </h3>
                        </div>

                        {{-- Desktop Table --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50/80">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ល.រ</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ឈ្មោះ</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">អត្តលេខ</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ជំនាន់</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ស្ថានភាព</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($summary[$year]['students'] as $index => $student)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                        {{ substr($student->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-800">{{ $student->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $student->email ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-bold text-gray-800">
                                                {{ $student->student_id_code ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-{{ ['blue', 'indigo', 'purple', 'pink'][($year - 1) % 4] }}-100 text-{{ ['blue', 'indigo', 'purple', 'pink'][($year - 1) % 4] }}-700">
                                                    <i class="fas fa-graduation-cap"></i>
                                                    {{ $year }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                    <i class="fas fa-check-circle"></i>
                                                    Active
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Cards --}}
                        <div class="md:hidden divide-y divide-gray-100">
                            @foreach($summary[$year]['students'] as $student)
                                <div class="px-4 py-3 hover:bg-gray-50/50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800">{{ $student->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $student->student_id_code ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle"></i>
                                            Active
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endfor

            {{-- Graduated Students --}}
            @if($summary['graduated']['count'] > 0)
                <div class="bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-2xl overflow-hidden ring-1 ring-black/5">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-transparent">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-user-check text-green-500"></i>
                            បញ្ចប់ការសិក្សា — {{ $summary['graduated']['count'] }} និស្សិត
                        </h3>
                    </div>
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ល.រ</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ឈ្មោះ</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">អត្តលេខ</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ថ្ងៃបញ្ចប់</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($summary['graduated']['students'] as $index => $student)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                    {{ substr($student->name, 0, 1) }}
                                                </div>
                                                <p class="text-sm font-bold text-gray-800">{{ $student->name }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $student->student_id_code ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $student->studentProgramEnrollments->firstWhere('status', 'graduated')?->graduation_date ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="md:hidden divide-y divide-gray-100">
                        @foreach($summary['graduated']['students'] as $student)
                            <div class="px-4 py-3 hover:bg-gray-50/50 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $student->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $student->student_id_code ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Action Button --}}
            <div class="flex justify-center">
                <a href="{{ route('admin.progression.advance', ['program_id' => $program->id]) }}"
                   class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-lg shadow-purple-500/25 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <i class="fas fa-arrow-right"></i>
                    ជំរុញនិស្សិតទៅជំនាន់ថ្មី
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
