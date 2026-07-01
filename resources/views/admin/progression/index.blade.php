<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between px-4 md:px-6 lg:px-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">ការឃ្លាំងមើលជំនាន់និស្សិត</h2>
                <p class="mt-1 text-sm text-gray-400">គ្រប់គ្រង និងឃ្លាំងមើលជំនាន់និស្សិតតាមកម្មវិធីសិក្សា</p>
            </div>
            <a href="{{ route('admin.manage-users') }}" class="mt-4 md:mt-0 inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                ត្រឡប់ក្រោយ
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Toast --}}
            @if (session('success') || session('error'))
                <div x-data="{ show: true, progress: 100 }" x-init="let i=setInterval(()=>{progress-=1;if(progress<=0){show=false;clearInterval(i)}},30)" x-show="show" x-transition class="fixed top-6 right-6 z-[9999] w-full max-w-sm">
                    <div class="relative overflow-hidden bg-white border border-gray-200 shadow-lg rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            @if(session('success'))
                                <div class="h-8 w-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            @else
                                <div class="h-8 w-8 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ session('success') ? 'ជោគជ័យ' : 'កំហុស' }}</p>
                                <p class="mt-0.5 text-sm text-gray-500">{{ session('success') ?? session('error') }}</p>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 h-1 bg-blue-500 rounded-b-xl transition-all duration-100" :style="{ width: progress + '%' }"></div>
                    </div>
                </div>
            @endif

            {{-- Program Selector --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">កម្មវិធីសិក្សា</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($programs as $prog)
                        <a href="{{ route('admin.progression.index', ['program_id' => $prog->id]) }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $program->id === $prog->id ? 'bg-blue-500 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $prog->name_km }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Year Level Summary --}}
            @php
                $genBase = 2006;
                $currentYearStart = \App\Models\AcademicYear::getCurrent()
                    ? (int) preg_replace('/\D/', '', substr(\App\Models\AcademicYear::getCurrent()->name, 0, 4))
                    : (int) date('Y');
                $yearColors = [
                    1 => ['icon_bg' => 'bg-blue-50', 'icon_text' => 'text-blue-500', 'border' => 'border-blue-100'],
                    2 => ['icon_bg' => 'bg-amber-50', 'icon_text' => 'text-amber-500', 'border' => 'border-amber-100'],
                    3 => ['icon_bg' => 'bg-violet-50', 'icon_text' => 'text-violet-500', 'border' => 'border-violet-100'],
                    4 => ['icon_bg' => 'bg-emerald-50', 'icon_text' => 'text-emerald-500', 'border' => 'border-emerald-100'],
                ];
                $studentAvatarColors = ['bg-blue-50 text-blue-600', 'bg-amber-50 text-amber-600', 'bg-violet-50 text-violet-600', 'bg-emerald-50 text-emerald-600'];
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @for($year = 1; $year <= $program->duration_years; $year++)
                    @php
                        $gen = $currentYearStart - $genBase - $year + 1;
                        $yc = $yearColors[$year] ?? $yearColors[4];
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-sm transition">
                        <div class="flex items-center justify-between mb-3">
                            <div class="h-9 w-9 {{ $yc['icon_bg'] }} rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 {{ $yc['icon_text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-gray-900">{{ $summary[$year]['count'] }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">ឆ្នាំទី{{ $year }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">ជំនាន់ទី{{ $gen }}</p>
                    </div>
                @endfor

                {{-- Graduated --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-sm transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="h-9 w-9 bg-teal-50 rounded-lg flex items-center justify-center">
                            <svg class="h-4 w-4 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $summary['graduated']['count'] }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">បញ្ចប់ការសិក្សា</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $summary['graduated']['count'] }} និស្សិត</p>
                </div>
            </div>

            {{-- Student Lists --}}
            @for($year = 1; $year <= $program->duration_years; $year++)
                @php
                    $gen = $currentYearStart - $genBase - $year + 1;
                    $yc = $yearColors[$year] ?? $yearColors[4];
                @endphp
                @if($summary[$year]['count'] > 0)
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                            <div class="h-2 w-2 rounded-full {{ $yc['icon_bg'] }} ring-2 {{ $yc['border'] }}"></div>
                            <h3 class="text-sm font-semibold text-gray-900">ឆ្នាំទី{{ $year }} — ជំនាន់ទី{{ $gen }} <span class="text-gray-400 font-normal">· {{ $summary[$year]['count'] }} និស្សិត</span></h3>
                        </div>

                        {{-- Desktop Table --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ល.រ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ឈ្មោះ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">អត្តលេខ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ជំនាន់</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ស្ថានភាព</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($summary[$year]['students'] as $index => $student)
                                        @php $avatarColor = $studentAvatarColors[$year % 4]; @endphp
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
                                            <td class="px-6 py-3.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $yc['icon_bg'] }} {{ $yc['icon_text'] }}">
                                                    ឆ្នាំទី{{ $year }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-400 ml-1">
                                                    ជំនាន់{{ $student->generation }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3.5">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-teal-50 text-teal-600">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-teal-500"></span>
                                                    Active
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Cards --}}
                        <div class="md:hidden divide-y divide-gray-50">
                            @foreach($summary[$year]['students'] as $student)
                                @php $avatarColor = $studentAvatarColors[$year % 4]; @endphp
                                <div class="px-4 py-3 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg {{ $avatarColor }} flex items-center justify-center font-semibold text-xs">
                                                {{ mb_substr($student->name, 0, 1, 'UTF-8') }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $student->student_id_code ?? '-' }} · ឆ្នាំទី{{ $year }} · ជំនាន់{{ $student->generation }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-teal-50 text-teal-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-teal-500"></span>
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
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="h-2 w-2 rounded-full bg-teal-50 ring-2 ring-teal-100"></div>
                        <h3 class="text-sm font-semibold text-gray-900">បញ្ចប់ការសិក្សា <span class="text-gray-400 font-normal">· {{ $summary['graduated']['count'] }} និស្សិត</span></h3>
                    </div>
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ល.រ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ឈ្មោះ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">អត្តលេខ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400">ថ្ងៃបញ្ចប់</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($summary['graduated']['students'] as $index => $student)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3.5 text-sm text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-semibold text-xs">
                                                    {{ mb_substr($student->name, 0, 1, 'UTF-8') }}
                                                </div>
                                                <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3.5 text-sm text-gray-700 font-medium">{{ $student->student_id_code ?? '-' }}</td>
                                        <td class="px-6 py-3.5 text-sm text-gray-500">{{ $student->studentProgramEnrollments->firstWhere('status', 'graduated')?->graduation_date ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="md:hidden divide-y divide-gray-50">
                        @foreach($summary['graduated']['students'] as $student)
                            <div class="px-4 py-3 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-semibold text-xs">
                                        {{ mb_substr($student->name, 0, 1, 'UTF-8') }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $student->student_id_code ?? '-' }}</p>
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
                   class="inline-flex items-center gap-2 bg-blue-500 text-white px-6 py-3 rounded-xl font-medium text-sm hover:bg-blue-600 shadow-sm shadow-blue-200 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    ជំរុញនិស្សិតទៅជំនាន់ថ្មី
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
