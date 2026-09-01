<x-app-layout>
    {{-- Dark Gradient Header --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-white/10 backdrop-blur rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">ការឃ្លាំងមើលជំនាន់និស្សិត</h1>
                        <p class="mt-1 text-sm text-slate-400">គ្រប់គ្រង និងឃ្លាំមើលជំនាន់និស្សិតតាមកម្មវិធីសិក្សា</p>
                    </div>
                </div>
                <a href="{{ route('admin.manage-users') }}" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-white/20 transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    ត្រឡប់ក្រោយ
                </a>
            </div>
        </div>
    </div>

    <div class="py-8 bg-gray-50 min-h-screen" style="padding-bottom: 100px;">
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

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-red-400 hover:text-red-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            {{-- Section 1: Filters --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="h-8 w-8 bg-slate-900 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">①</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">តម្រង់ទិស</h3>
                        <p class="text-xs text-gray-400">ស្វែងរក និងច្រោះនិស្សិតតាមសាលា កម្មវិធី ជំនាន់ ឬកាលវិភាគ</p>
                    </div>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.progression.index') }}" data-admin-realtime-filter class="space-y-4">
                        {{-- Row 1 --}}
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            <div class="md:col-span-3">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">ស្វែងរក</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </span>
                                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="ឈ្មោះ ឬអត្តលេខ..." autocomplete="off"
                                           class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">សាលា</label>
                                <select name="faculty_id"
                                        class="py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                                    <option value="">ទាំងអស់</option>
                                    @foreach($faculties as $f)
                                        <option value="{{ $f->id }}" {{ ($filters['facultyId'] ?? '') == $f->id ? 'selected' : '' }}>
                                            {{ $f->name_km }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">កម្មវិធីសិក្សា</label>
                                <select name="program_id" id="progressionProgramFilter"
                                        class="py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                                    <option value="">ទាំងអស់</option>
                                    @foreach($programs as $p)
                                        <option value="{{ $p->id }}"
                                                data-faculty-id="{{ $p->department->faculty_id ?? '' }}"
                                                {{ $program->id == $p->id ? 'selected' : '' }}>
                                            {{ $p->name_km }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">ជំនាន់</label>
                                <select name="generation"
                                        class="py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                                    <option value="">ទាំងអស់</option>
                                    @foreach($generations as $gen)
                                        <option value="{{ $gen->name }}" {{ ($filters['generation'] ?? '') == $gen->name ? 'selected' : '' }}>
                                            ជំនាន់ទី{{ $gen->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2 flex items-end gap-2">
                                <a href="{{ route('admin.progression.index', ['program_id' => $program->id]) }}"
                                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-all flex-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    កំណត់វិញ
                                </a>
                            </div>
                        </div>
                        {{-- Row 2 --}}
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            <div class="md:col-span-3">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">មុខវិជ្ជា</label>
                                <select name="course_id"
                                        class="py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                                    <option value="">ទាំងអស់</option>
                                    @foreach($courseOfferings as $co)
                                        <option value="{{ $co->id }}" {{ ($filters['courseId'] ?? '') == $co->id ? 'selected' : '' }}>
                                            {{ $co->course->title_km ?? 'N/A' }} ({{ $co->section ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">ឆមាស</label>
                                <select name="semester"
                                        class="py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                                    <option value="">ទាំងអស់</option>
                                    <option value="ឆមាសទី១" {{ ($filters['semester'] ?? '') == 'ឆមាសទី១' ? 'selected' : '' }}>ឆមាសទី១</option>
                                    <option value="ឆមាសទី២" {{ ($filters['semester'] ?? '') == 'ឆមាសទី២' ? 'selected' : '' }}>ឆមាសទី២</option>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">កាលវិភាគសិក្សា</label>
                                <select name="schedule_group"
                                        class="py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                                    <option value="">ទាំងអស់</option>
                                    <option value="mon_fri" {{ ($filters['scheduleGroup'] ?? '') == 'mon_fri' ? 'selected' : '' }}>ចន្ទ – សុក្រ</option>
                                    <option value="sat_sun" {{ ($filters['scheduleGroup'] ?? '') == 'sat_sun' ? 'selected' : '' }}>សៅរ៍ – អាទិត្យ</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 w-full bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    ស្វែងរក
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Results container for realtime filtering --}}
            <div data-admin-results>

            @php
                $genBase = 2006;
                $currentYearStart = \App\Models\AcademicYear::getCurrent()
                    ? (int) preg_replace('/\D/', '', substr(\App\Models\AcademicYear::getCurrent()->name, 0, 4))
                    : (int) date('Y');
                $yearColors = [
                    1 => ['icon_bg' => 'bg-emerald-50', 'icon_text' => 'text-emerald-500', 'border' => 'border-emerald-100', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                    2 => ['icon_bg' => 'bg-amber-50', 'icon_text' => 'text-amber-500', 'border' => 'border-amber-100', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
                    3 => ['icon_bg' => 'bg-violet-50', 'icon_text' => 'text-violet-500', 'border' => 'border-violet-100', 'bg' => 'bg-violet-50', 'text' => 'text-violet-600'],
                    4 => ['icon_bg' => 'bg-emerald-50', 'icon_text' => 'text-emerald-500', 'border' => 'border-emerald-100', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                ];
                $studentAvatarColors = ['bg-emerald-50 text-emerald-600', 'bg-amber-50 text-amber-600', 'bg-violet-50 text-violet-600', 'bg-emerald-50 text-emerald-600'];
            @endphp

            {{-- Section 2: Year Level Summary --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="h-8 w-8 bg-slate-900 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">②</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">សិស្សតាមឆ្នាំសិក្សា</h3>
                        <p class="text-xs text-gray-400">{{ $program->name_km }} — រយៈពេល {{ $program->duration_years }} ឆ្នាំ</p>
                    </div>
                </div>
                <div class="p-6">
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
                </div>
            </div>

            {{-- Section 3: Student Lists with Checkboxes --}}
            <form method="POST" action="{{ route('admin.progression.executeAdvance') }}" id="advanceForm">
                @csrf
                <input type="hidden" name="program_id" value="{{ $program->id }}">

                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 bg-slate-900 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xs font-bold">③</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">បញ្ជីនិស្សិត</h3>
                                <p class="text-xs text-gray-400">រើសនិស្សិតដែលចង់ជំរុញទៅឆ្នាំបន្ទាប់</p>
                            </div>
                        </div>
                        <button type="button" onclick="selectAllStudents()" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            ជ្រើសរើសទាំងអស់
                        </button>
                    </div>
                    <div class="p-6 space-y-6">

                        @for($year = 1; $year <= $program->duration_years; $year++)
                            @php
                                $gen = $currentYearStart - $genBase - $year + 1;
                                $yc = $yearColors[$year] ?? $yearColors[4];
                            @endphp
                            @if($summary[$year]['count'] > 0)
                                <div>
                                    <div class="flex items-center gap-3 mb-4">
                                        <input type="checkbox" data-year-check="{{ $year }}" onchange="toggleYear({{ $year }}, this)"
                                               class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold {{ $yc['bg'] }} {{ $yc['text'] }}">
                                            ឆ្នាំទី{{ $year }}
                                        </span>
                                        <span class="text-xs text-gray-400">ជំនាន់ទី{{ $gen }}</span>
                                        <span class="text-xs text-gray-400">· {{ $summary[$year]['count'] }} និស្សិត</span>
                                        <div class="flex-1 h-px bg-gradient-to-r from-gray-200 to-transparent"></div>
                                    </div>

                                    {{-- Desktop Table --}}
                                    <div class="hidden md:block overflow-x-auto border border-gray-200 rounded-xl">
                                        <table class="w-full">
                                            <thead class="bg-gray-50">
                                                <tr class="border-b border-gray-200">
                                                    <th class="px-4 py-3 text-center w-10">
                                                        <input type="checkbox" data-year-check="{{ $year }}" onchange="toggleYear({{ $year }}, this)"
                                                               class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ល.រ</th>
                                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ឈ្មោះ</th>
                                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">អត្តលេខ</th>
                                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ជំនាន់</th>
                                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ស្ថានភាព</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($summary[$year]['students'] as $index => $student)
                                                    @php $avatarColor = $studentAvatarColors[$year % 4]; @endphp
                                                    <tr class="hover:bg-gray-50 transition">
                                                        <td class="px-4 py-3.5 text-center">
                                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                                                   data-year="{{ $year }}" class="student-cb rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
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
                                                        <td class="px-6 py-3.5">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $yc['bg'] }} {{ $yc['text'] }}">
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
                                    <div class="md:hidden border border-gray-200 rounded-xl divide-y divide-gray-100">
                                        @foreach($summary[$year]['students'] as $student)
                                            @php $avatarColor = $studentAvatarColors[$year % 4]; @endphp
                                            <div class="px-4 py-3 hover:bg-gray-50 transition">
                                                <div class="flex items-center gap-3">
                                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                                           data-year="{{ $year }}" class="student-cb rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                                    <div class="h-8 w-8 rounded-lg {{ $avatarColor }} flex items-center justify-center font-semibold text-xs">
                                                        {{ mb_substr($student->name, 0, 1, 'UTF-8') }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                                        <p class="text-xs text-gray-400">{{ $student->student_id_code ?? '-' }} · ឆ្នាំទី{{ $year }} · ជំនាន់{{ $student->generation }}</p>
                                                    </div>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-teal-50 text-teal-600 flex-shrink-0">
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

                        {{-- Graduated Students (no checkboxes) --}}
                        @if($summary['graduated']['count'] > 0)
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-teal-50 text-teal-600">
                                        បញ្ចប់ការសិក្សា
                                    </span>
                                    <span class="text-xs text-gray-400">· {{ $summary['graduated']['count'] }} និស្សិត</span>
                                    <div class="flex-1 h-px bg-gradient-to-r from-gray-200 to-transparent"></div>
                                </div>

                                <div class="hidden md:block overflow-x-auto border border-gray-200 rounded-xl">
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr class="border-b border-gray-200">
                                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ល.រ</th>
                                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ឈ្មោះ</th>
                                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">អត្តលេខ</th>
                                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ថ្ងៃបញ្ចប់</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
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
                                <div class="md:hidden border border-gray-200 rounded-xl divide-y divide-gray-100">
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

                        {{-- Empty State --}}
                        @php
                            $totalStudents = 0;
                            for($y = 1; $y <= $program->duration_years; $y++) { $totalStudents += $summary[$y]['count']; }
                        @endphp
                        @if($totalStudents === 0 && $summary['graduated']['count'] === 0)
                            <div class="py-16 text-center">
                                <div class="h-16 w-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900">មិនមាននិស្សិត</h3>
                                <p class="text-sm text-gray-400 mt-1">មិនមាននិស្សិតសម្រាប់កម្មវិធីនេះទេ។</p>
                            </div>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Section 4: Actions --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="h-8 w-8 bg-slate-900 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">④</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">សកម្មភាព</h3>
                        <p class="text-xs text-gray-400">បញ្ចប់ការសិក្សាដោយស្វ័យប្រវត្តិសម្រាប់និស្សិតឆ្នាំចុងក្រោយ</p>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.progression.autoGraduate') }}" method="POST" class="inline"
                          onsubmit="return confirm('តើអ្នកប្រាកដទេ? និស្សិតឆ្នាំចុងក្រោយដែលមិនមាន F នឹងត្រូវបញ្ចប់ការសិក្សា។')">
                        @csrf
                        <input type="hidden" name="program_id" value="{{ $program->id }}">
                        <button type="submit" class="inline-flex items-center gap-2 bg-amber-50 text-amber-700 border border-amber-200 px-6 py-3 rounded-xl font-medium text-sm hover:bg-amber-100 transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            បញ្ចប់ការសិក្សាដោយស្វ័យប្រវត្តិ
                        </button>
                    </form>
                </div>
            </div>

            </div>{{-- /data-admin-results --}}

        </div>
    </div>

    {{-- Sticky Bottom Action Bar --}}
    <div x-data="{ selectedCount: 0 }" x-init="
            window.updateSelectedCount = function() {
                selectedCount = document.querySelectorAll('.student-cb:checked').length;
            }
         "
         x-show="selectedCount > 0" x-transition
         class="fixed bottom-0 inset-x-0 z-50 bg-white border-t border-gray-200 shadow-lg shadow-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-700">
                    <span x-text="selectedCount">0</span> និស្សិតត្រូវបានជ្រើសរើស
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="clearAllStudents()" class="inline-flex items-center gap-2 bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-200 transition-all">
                    សម្អាត
                </button>
                <button type="submit" form="advanceForm"
                        class="inline-flex items-center gap-2 bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-medium text-sm hover:bg-emerald-700 shadow-sm shadow-emerald-200 transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    ជំរុញនិស្សិតដែលបានជ្រើសរើស
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var facultySelect = document.querySelector('select[name="faculty_id"]');
            var programSelect = document.getElementById('progressionProgramFilter');
            if (!facultySelect || !programSelect) return;

            function filterPrograms() {
                var facultyId = facultySelect.value;
                var options = programSelect.querySelectorAll('option[value]');
                var selectedStillVisible = false;

                options.forEach(function (opt) {
                    if (opt.value === '') return;
                    var match = !facultyId || opt.dataset.facultyId === facultyId;
                    opt.hidden = !match;
                    opt.disabled = !match;
                    if (match && opt.value === programSelect.value) selectedStillVisible = true;
                });

                if (!selectedStillVisible) programSelect.value = '';
            }

            facultySelect.addEventListener('change', filterPrograms);
            filterPrograms();
        })();

        function toggleYear(year, source) {
            document.querySelectorAll('.student-cb[data-year="' + year + '"]').forEach(function (cb) {
                cb.checked = source.checked;
            });
            updateCount();
        }

        function selectAllStudents() {
            document.querySelectorAll('.student-cb').forEach(function (cb) {
                cb.checked = true;
            });
            document.querySelectorAll('[data-year-check]').forEach(function (cb) {
                cb.checked = true;
            });
            updateCount();
        }

        function clearAllStudents() {
            document.querySelectorAll('.student-cb').forEach(function (cb) {
                cb.checked = false;
            });
            document.querySelectorAll('[data-year-check]').forEach(function (cb) {
                cb.checked = false;
            });
            updateCount();
        }

        function updateCount() {
            var count = document.querySelectorAll('.student-cb:checked').length;
            document.querySelectorAll('[data-year-check]').forEach(function (yearCheck) {
                var year = yearCheck.dataset.yearCheck;
                var yearCbs = document.querySelectorAll('.student-cb[data-year="' + year + '"]');
                var allChecked = yearCbs.length > 0 && Array.from(yearCbs).every(function (c) { return c.checked; });
                yearCheck.checked = allChecked;
            });
            if (typeof window.updateSelectedCount === 'function') {
                window.updateSelectedCount();
            }
        }

        document.querySelectorAll('.student-cb').forEach(function (cb) {
            cb.addEventListener('change', updateCount);
        });
    </script>
</x-app-layout>
