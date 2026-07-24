<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&display=swap" rel="stylesheet">

    <style>
        :root { --font-header: 'Moul', serif; --font-body: 'Battambang', system-ui, sans-serif; }
        #printable-schedule-container { display: none; }
        @media print {
            @page { size: A4 landscape; margin: 5mm; }
            body { background: white !important; -webkit-print-color-adjust: exact; margin: 0; padding: 0; font-family: 'Battambang', system-ui !important; zoom: 90%; }
            .no-print { display: none !important; }
            #printable-schedule-container { display: flex !important; flex-direction: column; width: 100% !important; height: 98vh; justify-content: space-between; }
            .header-print-layout { display: grid; grid-template-columns: 1fr 1fr 1fr; align-items: start; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
            .header-left { text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; }
            .header-center { text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; }
            .header-right { text-align: right; }
            .font-moul { font-family: 'Moul', serif !important; font-weight: normal !important; }
            .uni-logo-text h3 { font-size: 11pt; color: #2a58ad; margin: 3px 0; line-height: 1.4; }
            .uni-logo-text img { width: 85px; height: auto; margin-bottom: 5px; }
            .kingdom-header h2 { font-size: 12pt; margin: 3px 0; color: black; line-height: 1.4; }
            .kingdom-header img { width: 110px; height: auto; margin-top: 5px; }
            .schedule-title-block { text-align: center; margin-bottom: 20px; }
            .schedule-title-block h1 { font-size: 13pt; margin: 5px 0; color: black; }
            .schedule-title-block p { font-size: 10pt; font-weight: bold; margin: 0; }
            .table-wrapper { flex-grow: 1; display: flex; flex-direction: column; gap: 20px; }
            .matrix-table { width: 100%; border-collapse: collapse; border: 1.5pt solid black; }
            .matrix-table th, .matrix-table td { border: 1pt solid black; padding: 8px; text-align: center; vertical-align: middle; font-size: 9.5pt; line-height: 1.4; }
            .matrix-table th { font-size: 9.5pt; background-color: #f1f5f9 !important; height: 35px; color: black; }
            .cell-subject { font-weight: bold; display: block; font-size: 9.5pt; margin-bottom: 4px; }
            .cell-lecturer { display: block; font-size: 9pt; color: #000; }
            .cell-room { display: block; font-weight: bold; font-size: 9pt; color: #000000; }
            .f-sigs { page-break-inside: avoid; display: flex; justify-content: space-between; margin-top: 20px; }
            .sig-block { text-align: center; width: 35%; }
            .sig-title-top { font-size: 10pt; margin-bottom: 10px; }
            .sig-role { font-size: 10pt; margin: 0; }
            .sig-spacer { height: 80px; }
            .sig-name { font-size: 11pt; font-weight: bold; color: #2a58ad; }
            .sig-date { font-size: 9pt; margin-bottom: 5px; }
        }
    </style>

    {{-- SCREEN VIEW --}}
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900 no-print">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-28 pt-10 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                            <i class="fas fa-book-open text-emerald-300 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight">{{ __('ការផ្តល់ជូនមុខវិជ្ជា') }}</h2>
                            <p class="text-slate-400 mt-1 text-sm">{{ __('គ្រប់គ្រងការបែងចែកមុខវិជ្ជាទៅតាមជំនាញ និងកាលវិភាគ') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <button onclick="printOrExport('word')" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl font-bold transition-all text-sm backdrop-blur-sm">
                            <i class="fas fa-file-word"></i> <span>Word</span>
                        </button>
                        <button onclick="printOrExport('print')" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl font-bold transition-all text-sm backdrop-blur-sm">
                            <i class="fas fa-print"></i> <span>{{ __('បោះពុម្ព') }}</span>
                        </button>
                        <a href="{{ route('admin.create-course-offering') }}" class="flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg transition-all text-sm">
                            <i class="fas fa-plus"></i> <span>{{ __('បន្ថែមថ្មី') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 pb-12 relative z-10">
            {{-- Filter Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
                <form action="{{ route('admin.manage-course-offerings') }}" method="GET" class="space-y-4">
                    {{-- Row 1: Search --}}
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('ស្វែងរកមុខវិជ្ជា / សាស្ត្រាចារ្យ') }}</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('វាយឈ្មោះមុខវិជ្ជា ឬសាស្ត្រាចារ្យ...') }}" class="w-full pl-10 pr-4 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                    </div>

                    {{-- Row 2: Filters --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('កម្មវិធីសិក្សា') }}</label>
                            <select name="program_id" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('ទាំងអស់') }}</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name_km }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('ជំនាន់') }}</label>
                            <input type="text" name="generation" value="{{ request('generation') }}" placeholder="Ex: 17" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('វេនសិក្សា') }}</label>
                            <select name="shift" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('ទាំងអស់') }}</option>
                                <option value="weekday" {{ request('shift') == 'weekday' ? 'selected' : '' }}>{{ __('ចន្ទ-សុក្រ') }}</option>
                                <option value="weekend" {{ request('shift') == 'weekend' ? 'selected' : '' }}>{{ __('សៅរ៍-អាទិត្យ') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('ឆមាស') }}</label>
                            <select name="semester" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('ទាំងអស់') }}</option>
                                <option value="ឆមាសទី១" {{ request('semester') == 'ឆមាសទី១' ? 'selected' : '' }}>{{ __('ឆមាសទី១') }}</option>
                                <option value="ឆមាសទី២" {{ request('semester') == 'ឆមាសទី២' ? 'selected' : '' }}>{{ __('ឆមាសទី២') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('ឆ្នាំសិក្សា') }}</label>
                            <select name="academic_year" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('ទាំងអស់') }}</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->name }}" {{ request('academic_year') == $year->name ? 'selected' : '' }}>{{ $year->name }} {{ $year->is_current ? '('.__('បច្ចុប្បន្ន').')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('សាស្ត្រាចារ្យ') }}</label>
                            <select name="lecturer_id" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('ទាំងអស់') }}</option>
                                @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}" {{ request('lecturer_id') == $lecturer->id ? 'selected' : '' }}>{{ $lecturer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 3: Actions --}}
                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.manage-course-offerings') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold text-sm transition-colors">
                            <i class="fas fa-undo"></i> <span>{{ __('កំណត់ឡើងវិញ') }}</span>
                        </a>
                        <button type="submit" class="flex items-center gap-2 bg-gradient-to-r from-slate-800 to-slate-700 hover:from-slate-700 hover:to-slate-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition-all">
                            <i class="fas fa-filter"></i> <span>{{ __('ត្រងទិន្នន័យ') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Content --}}
            <div x-data="{ viewMode: '{{ request('view', 'grid') }}' }" @view-changed.window="viewMode = $event.detail">

                {{-- GRID VIEW --}}
                <div x-show="viewMode === 'grid'" x-cloak>
                    @if($courseOfferings->isEmpty())
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                            <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-5">
                                <i class="fas fa-book-open text-gray-300 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-600 mb-2">{{ __('មិនមានការផ្តល់ជូនមុខវិជ្ជា') }}</h3>
                            <p class="text-gray-400 text-sm mb-6">{{ __('សូមព្យាយាមកំណត់ឡើងវិញ ឬបន្ថែមការផ្តល់ជូនថ្មី') }}</p>
                            <a href="{{ route('admin.create-course-offering') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all">
                                <i class="fas fa-plus"></i> <span>{{ __('បន្ថែមថ្មី') }}</span>
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach ($courseOfferings as $offering)
                                @php
                                    $today = now()->startOfDay();
                                    $isActive = $today->between($offering->start_date, $offering->end_date);
                                    $enrollmentCount = $offering->studentCourseEnrollments->count();
                                @endphp
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-200 group">
                                    {{-- Header --}}
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="space-y-2">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold {{ $isActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                                {{ $isActive ? 'សកម្ម' : 'ផុតកំណត់' }}
                                            </span>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($offering->targetPrograms as $p)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                        {{ $p->name_km }} (G{{ $p->pivot->generation }})
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('admin.edit-course-offering', $offering->id) }}" class="p-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-colors text-xs">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button onclick="openDeleteModal({{ $offering->id }})" class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-colors text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Course Title --}}
                                    <h4 class="font-bold text-gray-900 text-lg mb-1 leading-tight">
                                        {{ $offering->course?->title_km ?? $offering->course?->title_en ?? 'N/A' }}
                                    </h4>
                                    <p class="text-xs text-gray-400 font-medium mb-3">
                                        {{ $offering->semester }} / {{ $offering->academic_year }}
                                    </p>

                                    {{-- Lecturer --}}
                                    <div class="flex items-center gap-2.5 mb-4 pb-4 border-b border-gray-100">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                                            <i class="fas fa-user-tie text-xs"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700">{{ $offering->lecturer?->name ?? 'មិនទាន់កំណត់' }}</span>
                                    </div>

                                    {{-- Enrollment Count --}}
                                    <div class="flex items-center gap-2.5 mb-4 pb-4 border-b border-gray-100">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                                            <i class="fas fa-users text-xs"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700">{{ $enrollmentCount }} សិស្សចុះឈ្មោះ</span>
                                    </div>

                                    {{-- Schedules --}}
                                    <div class="space-y-2">
                                        @forelse($offering->schedules as $s)
                                            <div class="flex items-center justify-between text-xs bg-gray-50 px-3 py-2 rounded-xl">
                                                <span class="font-bold text-gray-700">{{ substr($s->day_of_week, 0, 3) }}</span>
                                                <span class="text-gray-500 font-medium">
                                                    {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}
                                                </span>
                                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                                    {{ $s->room->room_number ?? '-' }}
                                                </span>
                                            </div>
                                        @empty
                                            <p class="text-xs text-gray-400 italic">{{ __('មិនទាន់មានកាលវិភាគ') }}</p>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- TABLE VIEW --}}
                <div x-show="viewMode === 'table'" x-cloak>
                    @if($courseOfferings->isEmpty())
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                            <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-5">
                                <i class="fas fa-book-open text-gray-300 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-600 mb-2">{{ __('មិនមានការផ្តល់ជូនមុខវិជ្ជា') }}</h3>
                            <p class="text-gray-400 text-sm mb-6">{{ __('សូមព្យាយាមកំណត់ឡើងវិញ ឬបន្ថែមការផ្តល់ជូនថ្មី') }}</p>
                            <a href="{{ route('admin.create-course-offering') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all">
                                <i class="fas fa-plus"></i> <span>{{ __('បន្ថែមថ្មី') }}</span>
                            </a>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('មុខវិជ្ជា') }}</th>
                                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('សាស្ត្រាចារ្យ') }}</th>
                                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ឆមាស / ឆ្នាំ') }}</th>
                                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ជំនាញ') }}</th>
                                            <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('សិស្ស') }}</th>
                                            <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('កាលវិភាគ') }}</th>
                                            <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ស្ថានភាព') }}</th>
                                            <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('សកម្មភាព') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($courseOfferings as $offering)
                                            @php
                                                $today = now()->startOfDay();
                                                $isActive = $today->between($offering->start_date, $offering->end_date);
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-5 py-4">
                                                    <div class="font-semibold text-gray-900 text-sm">{{ $offering->course?->title_km ?? $offering->course?->title_en ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <span class="text-sm text-gray-700">{{ $offering->lecturer?->name ?? 'មិនទាន់កំណត់' }}</span>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <span class="text-sm text-gray-600">{{ $offering->semester }} / {{ $offering->academic_year }}</span>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($offering->targetPrograms as $p)
                                                            <span class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md font-bold border border-emerald-100">
                                                                {{ $p->name_km }} (G{{ $p->pivot->generation }})
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700">
                                                        {{ $offering->studentCourseEnrollments->count() }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    @foreach($offering->schedules as $s)
                                                        <div class="mb-1 last:mb-0">
                                                            <span class="text-xs font-bold text-gray-700">{{ substr($s->day_of_week, 0, 3) }}</span>
                                                            <span class="text-xs text-gray-500 mx-1">{{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}</span>
                                                            <span class="text-xs text-emerald-600 font-bold">{{ $s->room->room_number ?? '-' }}</span>
                                                        </div>
                                                    @endforeach
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold {{ $isActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                                        {{ $isActive ? 'សកម្ម' : 'ផុតកំណត់' }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    <div class="flex justify-center gap-1.5">
                                                        <a href="{{ route('admin.edit-course-offering', $offering->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-bold hover:bg-emerald-600 hover:text-white transition-colors">
                                                            <i class="fas fa-pen"></i> <span>{{ __('កែ') }}</span>
                                                        </a>
                                                        <button onclick="openDeleteModal({{ $offering->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-600 hover:text-white transition-colors">
                                                            <i class="fas fa-trash"></i> <span>{{ __('លុប') }}</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Pagination --}}
                <div class="mt-8 no-print">{{ $courseOfferings->links() }}</div>
            </div>
        </div>
    </div>

    {{-- PRINTABLE AREA --}}
    <div id="printable-schedule-container">
        @php
            $allSchedules = collect();
            foreach($courseOfferings as $off) { foreach($off->schedules as $s) { $allSchedules->push($s); } }
            $weekdayMap = ['Monday' => 'ចន្ទ/Mon', 'Tuesday' => 'អង្គារ/Tue', 'Wednesday' => 'ពុធ/Wed', 'Thursday' => 'ព្រហស្បតិ៍/Thu', 'Friday' => 'សុក្រ/Fri'];
            $weekendMap = ['Saturday' => 'សៅរ៍/Sat', 'Sunday' => 'អាទិត្យ/Sun'];
            $weekdaySchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekdayMap));
            $weekendSchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekendMap));
            $weekdayRows = $weekdaySchedules->groupBy(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->sortKeys();
            $weekendTimeSlots = $weekendSchedules->map(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->unique()->sort();

            $currentProgramName = "ជំនាញ គ្រប់គ្រងបច្ចេកវិទ្យាព័ត៌មានវិទ្យា"; 
            if(request('program_id')){
                $prog = $programs->firstWhere('id', request('program_id'));
                if($prog) $currentProgramName = $prog->name_km;
            } elseif($courseOfferings->isNotEmpty()) {
                $first = $courseOfferings->first();
                if($first->targetPrograms->isNotEmpty()){
                    $currentProgramName = $first->targetPrograms->first()->name_km;
                }
            }
            $generation = request('generation');
            $genText = $generation ? "(G$generation)" : "";
        @endphp

        <div>
            <div class="header-print-layout">
                <div class="header-left uni-logo-text">
                    <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo">
                    <h3 class="font-moul">សាកលវិទ្យាល័យជាតិមានជ័យ</h3>
                    <h3 class="font-moul">{{ __('ការិយាល័យសិក្សា') }}</h3>
                </div>
                <div class="header-center kingdom-header">
                    <h2 class="font-moul">ព្រះរាជាណាចក្រកម្ពុជា</h2>
                    <h2 class="font-moul">ជាតិ សាសនា ព្រះមហាក្សត្រ</h2>
                    <img src="{{ asset('assets/image/2.png') }}" alt="Line">
                </div>
                <div class="header-right"></div> 
            </div>

            <div class="schedule-title-block">
                <h1 class="font-moul">តារាងវិភាគកម្មធម៌ឆមាសទី{{ request('semester') == 'ឆមាសទី២' ? '២' : '១' }} / Timetable Semester {{ request('semester') == 'ឆមាសទី២' ? '2' : '1' }}</h1>
                <p>ជំនាន់ទី{{ request('generation', '...') }} ថ្នាក់បរិញ្ញាបត្រវិទ្យាសាស្ត្រ និងបច្ចេកវិទ្យា ឆ្នាំសិក្សា {{ date('Y') }}-{{ date('Y')+1 }}</p>
                <p style="font-weight: normal; font-size: 10pt;">ចាប់ផ្តើមពីថ្ងៃចន្ទ ១២ កើត ខែអស្សុជ ឆ្នាំរោង ឆស័ក ព.ស ២៥៦៨ ត្រូវនឹងថ្ងៃទី១៤ ខែតុលា ឆ្នាំ២០២៤ ដល់សប្តាហ៍</p>
            </div>
        </div>

        <div class="table-wrapper">
            @if($weekdayRows->isNotEmpty())
                <div style="text-align: left; font-weight: bold; font-family: 'Battambang'; text-decoration: underline; font-size: 10pt; margin-bottom: 5px;">ជំនាញ {{ $currentProgramName }} {{ $genText }} (Mon-Fri)</div>
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th class="font-moul" style="width: 12%;">ម៉ោងសិក្សា</th>
                            @foreach($weekdayMap as $label) <th class="font-moul">{{ $label }}</th> @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weekdayRows as $slot => $slots)
                        <tr>
                            <td style="font-weight: bold;">{{ $slot }}</td>
                            @foreach($weekdayMap as $dayKey => $label)
                                <td>
                                    @php $class = $slots->where('day_of_week', $dayKey)->first(); @endphp
                                    @if($class)
                                        <span class="cell-subject">{{ $class->courseOffering->course->title_km ?? 'N/A' }}</span>
                                        <span class="cell-lecturer">លោក {{ $class->courseOffering->lecturer->name ?? 'N/A' }}</span>
                                        <span class="cell-room">បន្ទប់ {{ $class->room->room_number ?? '-' }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if($weekendSchedules->isNotEmpty())
                <div style="text-align: left; font-weight: bold; font-family: 'Battambang'; text-decoration: underline; font-size: 10pt; margin-bottom: 5px;">ជំនាញ {{ $currentProgramName }} {{ $genText }} (Sat-Sun)</div>
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th class="font-moul" style="width: 12%;">{{ __('ថ្ងៃសិក្សា') }}</th>
                            @foreach($weekendTimeSlots as $timeSlot) <th class="font-moul">{{ $timeSlot }}</th> @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weekendMap as $dayKey => $dayLabel)
                        <tr>
                            <td class="font-moul" style="background-color: #f8fafc;">{{ $dayLabel }}</td>
                            @foreach($weekendTimeSlots as $time)
                                <td>
                                    @php 
                                        $class = $weekendSchedules->filter(function($s) use ($dayKey, $time) {
                                            $slot = \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i');
                                            return $s->day_of_week === $dayKey && $slot === $time;
                                        })->first();
                                    @endphp
                                    @if($class)
                                        <span class="cell-subject">{{ $class->courseOffering->course->title_km ?? 'N/A' }}</span>
                                        <span class="cell-lecturer">លោក {{ $class->courseOffering->lecturer->name ?? 'N/A' }}</span>
                                        <span class="cell-room">បន្ទប់ {{ $class->room->room_number ?? '-' }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="f-sigs">
            <div class="sig-block" style="text-align: left; padding-left: 20px;">
                <div class="sig-title-top font-moul">បានឃើញ និងឯកភាព</div>
                <div class="sig-role font-moul">ជ. សាកលវិទ្យាធិការ</div>
                <div class="sig-role font-moul">សាកលវិទ្យាធិការរង</div>
                <div class="sig-spacer"></div>
            </div>
            @php
                function toKhmerNumber($number) {
                    $khmerNumbers = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
                    return str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $khmerNumbers, $number);
                }
                $now = now();
                $khmerMonths = [
                    1 => 'មករា', 2 => 'កុម្ភៈ', 3 => 'មីនា', 4 => 'មេសា', 5 => 'ឧសភា', 6 => 'មិថុនា',
                    7 => 'កក្កដា', 8 => 'សីហា', 9 => 'កញ្ញា', 10 => 'តុលា', 11 => 'វិច្ឆិកា', 12 => 'ធ្នូ'
                ];
                $beYear = $now->year + 543; 
                $day = toKhmerNumber($now->format('d'));
                $month = $khmerMonths[$now->month];
                $year = toKhmerNumber($now->year);
                $beYearKh = toKhmerNumber($beYear);
            @endphp
            <div class="sig-block" style="text-align: right; padding-right: 20px;">
                <div class="sig-date">
                    ថ្ងៃទី{{ $day }} ខែ{{ $month }} ឆ្នាំ{{ $year }} ព.ស {{ $beYearKh }}
                </div>
                <div class="sig-date">បន្ទាយមានជ័យ ថ្ងៃទី............. ខែ............. ឆ្នាំ២០......</div>
                <div class="sig-title-top font-moul" style="margin-top: 5px;">ប្រធានការិយាល័យសិក្សា</div>
                <div class="sig-spacer"></div>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div id="delete-modal" class="relative z-50 hidden no-print" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl sm:w-full sm:max-w-md border border-gray-200">
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-5">
                            <i class="fas fa-trash-alt text-red-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('លុបការផ្តល់ជូនមុខវិជ្ជា?') }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ __('តើអ្នកប្រាកដទេថាចង់លុបទិន្នន័យនេះ? ប្រតិបត្តិការនេះមិនអាចត្រឡប់ថយក្រោយវិញបានឡើយ។') }}</p>
                    </div>
                    <div class="bg-gray-50 px-8 py-5 flex justify-center gap-3 rounded-b-2xl">
                        <button onclick="closeDeleteModal()" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-100 transition-colors">{{ __('បោះបង់') }}</button>
                        <form id="delete-form" method="POST" action=""> @csrf @method('DELETE')
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-red-500/25 hover:from-red-500 hover:to-red-400 transition-all">{{ __('យល់ព្រមលុប') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(id) {
            const form = document.getElementById('delete-form');
            form.action = '{{ route("admin.course-offerings.destroy", ":id") }}'.replace(':id', id);
            document.getElementById('delete-modal').classList.remove('hidden');
        }
        function closeDeleteModal() { document.getElementById('delete-modal').classList.add('hidden'); }

        function getBase64Image(img) {
            var canvas = document.createElement("canvas");
            canvas.width = img.naturalWidth; canvas.height = img.naturalHeight;
            var ctx = canvas.getContext("2d"); ctx.drawImage(img, 0, 0);
            return canvas.toDataURL("image/png");
        }

        function exportToWord() {
            const logo = document.querySelector('.uni-logo-text img');
            const line = document.querySelector('.kingdom-header img');
            let content = document.getElementById('printable-schedule-container').cloneNode(true);
            content.style.display = 'block';
            
            if(logo && logo.src) {
                const logoClone = content.querySelector('.uni-logo-text img');
                if(logoClone) logoClone.src = getBase64Image(logo);
            }
            if(line && line.src) {
                 const lineClone = content.querySelector('.kingdom-header img');
                 if(lineClone) lineClone.src = getBase64Image(line);
            }

            const htmlString = `
                <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
                <head><meta charset='utf-8'><style>
                    body { font-family: 'Battambang', Arial, sans-serif; }
                    .matrix-table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1pt solid black; padding: 5px; text-align: center; }
                    th { background-color: #f1f5f9; font-family: 'Moul', serif; font-size: 9pt; }
                    @@page { size: A4 landscape; margin: 1cm; }
                </style></head>
                <body>${content.innerHTML}</body></html>`;

            const blob = new Blob(['\ufeff', htmlString], { type: 'application/msword' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url; link.download = 'NMU-Academic-Schedule.doc';
            document.body.appendChild(link); link.click(); document.body.removeChild(link);
        }

        function showFilterAlert() {
            document.getElementById('filter-alert-modal').classList.remove('hidden');
        }
        function closeFilterAlert() {
            document.getElementById('filter-alert-modal').classList.add('hidden');
        }
        function printOrExport(action) {
            var progEl = document.querySelector('select[name=program_id]');
            var genEl = document.querySelector('input[name=generation]');
            var shiftEl = document.querySelector('select[name=shift]');
            var semEl = document.querySelector('select[name=semester]');
            var yearEl = document.querySelector('select[name=academic_year]');
            var lectEl = document.querySelector('select[name=lecturer_id]');
            var prog = progEl ? progEl.value : '';
            var gen = genEl ? genEl.value : '';
            var shift = shiftEl ? shiftEl.value : '';
            var sem = semEl ? semEl.value : '';
            var year = yearEl ? yearEl.value : '';
            var lect = lectEl ? lectEl.value : '';
            if (!prog && !gen && !shift && !sem && !year && !lect) {
                showFilterAlert();
                return;
            }
            if (action === 'print') {
                window.print();
            } else if (action === 'word') {
                exportToWord();
            }
        }
    </script>

    {{-- Filter Alert Modal --}}
    <div id="filter-alert-modal" class="hidden fixed inset-0 z-[9999] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeFilterAlert()"></div>
            <div class="inline-block w-full max-w-md p-6 text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl z-50">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-amber-100 rounded-full">
                    <i class="fas fa-filter text-amber-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-center text-gray-900">សូមជ្រើសរើសទិន្នន័យ</h3>
                <p class="mt-2 text-sm text-center text-gray-500">
                    សូមជ្រើសរើស <span class="font-black text-amber-600">កម្មវិធីសិក្សា</span> <span class="font-black text-amber-600">ជំនាន់</span> <span class="font-black text-amber-600">ឆមាស</span> <span class="font-black text-amber-600">ឆ្នាំសិក្សា</span> ឬ <span class="font-black text-amber-600">សាស្ត្រាចារ្យ</span> យ៉ាងតិចមួយមុនពេលបោះពុម្ព។
                </p>
                <div class="mt-6 flex justify-center">
                    <button type="button" onclick="closeFilterAlert()" class="px-6 py-2 text-sm font-bold text-white bg-amber-500 rounded-xl hover:bg-amber-600 shadow-lg shadow-amber-200 transition-all">
                        យល់ព្រម
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
