<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&display=swap" rel="stylesheet">

    <style>
        :root { --font-header: 'Moul', serif; --font-body: 'Battambang', system-ui, sans-serif; }
        #printable-schedule-container { display: none; }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { 
                background: white !important; 
                -webkit-print-color-adjust: exact; 
                margin: 0; padding: 0; 
                font-family: 'Battambang', system-ui !important; 
                zoom: 95%; 
            }
            .no-print { display: none !important; }
            
            #printable-schedule-container { 
                display: flex !important; 
                flex-direction: column;
                width: 100% !important;
                height: 95vh; /* Forces the container to perfectly fit one page height */
                color: black; 
                position: relative;
            }

            /* --- Header Layout --- */
            .header-print-layout { 
                display: flex; 
                align-items: flex-start;
                position: relative; 
                width: 100%; 
                margin-bottom: 15px; 
            }
            
            /* Left Logo & University Name */
            .uni-logo-text { 
                text-align: center; 
                display: flex;
                flex-direction: column;
                align-items: center;
                padding-left: 10px; 
            }
            .uni-logo-text img { 
                width: 95px; 
                height: auto; 
                margin: 0 auto 5px auto; 
            }
            .uni-logo-text h3 { 
                font-family: 'Moul', serif !important; 
                font-size: 11pt; 
                color: black; 
                margin: 2px 0; 
                line-height: 1.4; 
                font-weight: normal; 
                white-space: nowrap; 
            }
            
            /* Centered Kingdom Header */
            .kingdom-header { 
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                text-align: center; 
                top: 0;
            }
            .kingdom-header h2 { 
                font-family: 'Moul', serif !important; 
                font-size: 13pt; 
                margin: 2px 0; 
                color: black; 
                line-height: 1.4; 
                font-weight: normal; 
            }
            .kingdom-header img { 
                width: 130px; 
                height: auto; 
                margin: 4px auto 0 auto; 
                display: block; 
            }

            /* Schedule Title */
            .schedule-title-block { text-align: center; margin-top: 15px; margin-bottom: 20px; }
            .schedule-title-block h1 { font-family: 'Moul', serif !important; font-size: 12pt; margin: 5px 0; color: black; font-weight: normal; }
            .schedule-title-block p { font-size: 10.5pt; margin: 3px 0; color: black; line-height: 1.5; }

            /* Table Formatting */
            .table-wrapper {
                flex-grow: 1; /* Allows the table area to take up remaining space */
            }
            .specialty-title { text-align: left; font-weight: bold; font-family: 'Battambang', sans-serif; font-size: 11pt; margin-bottom: 6px; text-decoration: underline; text-underline-offset: 3px; }
            .matrix-table { width: 100%; border-collapse: collapse; border: 1.5pt solid black; margin-bottom: 20px; }
            .matrix-table th, .matrix-table td { border: 1pt solid black; padding: 6px 4px; text-align: center; vertical-align: middle; color: black; }
            .matrix-table th { font-size: 10pt; font-family: 'Battambang', sans-serif; font-weight: bold; background-color: transparent !important; }
            .matrix-table td { font-size: 9.5pt; line-height: 1.4; height: 45px; } /* Slightly reduced height to ensure 1-page fit */
            
            .cell-subject { font-weight: bold; display: block; margin-bottom: 2px; }
            .cell-lecturer { display: block; margin-bottom: 2px; }
            .cell-room { display: block; font-weight: bold; }

            /* Footer Signatures */
            .f-sigs { 
                display: flex; 
                justify-content: space-between; 
                margin-top: auto; /* Automatically pushes signatures to the very bottom */
                page-break-inside: avoid; 
                padding: 0 10px;
                padding-bottom: 15px;
            }
            .sig-block-left { text-align: center; width: 40%; }
            .sig-block-right { text-align: center; width: 45%; }
            .sig-title-moul { font-family: 'Moul', serif !important; font-size: 11pt; margin-bottom: 4px; font-weight: normal; }
            .sig-date-kh { font-size: 11pt; font-family: 'Battambang', sans-serif; margin-bottom: 4px; }
            .sig-spacer { height: 80px; }
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
                            <h2 class="text-3xl font-bold tracking-tight">{{ __('course_offering') }}</h2>
                            <p class="text-slate-400 mt-1 text-sm">{{ __('manage_course_allocation_by_program_and_schedule') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <button onclick="printOrExport('word')" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl font-bold transition-all text-sm backdrop-blur-sm">
                            <i class="fas fa-file-word"></i> <span>Word</span>
                        </button>
                        <button onclick="printOrExport('print')" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl font-bold transition-all text-sm backdrop-blur-sm">
                            <i class="fas fa-print"></i> <span>{{ __('print_2') }}</span>
                        </button>
                        <a href="{{ route('admin.create-course-offering') }}" class="flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg transition-all text-sm">
                            <i class="fas fa-plus"></i> <span>{{ __('add_new') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 pb-12 relative z-10">
            {{-- Filter Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
                <form action="{{ route('admin.manage-course-offerings') }}" method="GET" data-admin-realtime-filter data-dept-filter-container class="space-y-4">
                    <script type="application/json" data-dept-filter>
                        {!! $departments->map(fn($d) => ['id' => $d->id, 'name' => $d->name_km, 'faculty_id' => $d->faculty_id])->toJson() !!}
                    </script>
                    {{-- Row 1: Search --}}
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('search_course_lecturer') }}</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('type_a_course_or_lecturer_name_2') }}" class="w-full pl-10 pr-4 rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                    </div>

                    {{-- Row 2: Filters --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('faculty') }}</label>
                            <select name="faculty_id" id="faculty-filter" data-dept-faculty class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->name_km }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('department') }}</label>
                            <select name="department_id" id="department-filter" data-dept-department class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name_km }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('generation') }}</label>
                            <select name="generation" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($generations as $gen)
                                    <option value="{{ $gen }}" {{ request('generation') == $gen ? 'selected' : '' }}>G{{ $gen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('shift') }}</label>
                            <select name="shift" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                <option value="weekday" {{ request('shift') == 'weekday' ? 'selected' : '' }}>{{ __('mon_fri') }}</option>
                                <option value="weekend" {{ request('shift') == 'weekend' ? 'selected' : '' }}>{{ __('sat_sun') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('semester') }}</label>
                            <select name="semester" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                <option value="ឆមាសទី១" {{ request('semester') == 'ឆមាសទី១' ? 'selected' : '' }}>{{ __('semester_1') }}</option>
                                <option value="ឆមាសទី២" {{ request('semester') == 'ឆមាសទី២' ? 'selected' : '' }}>{{ __('semester_2') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('academic_year') }}</label>
                            <select name="academic_year" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->name }}" {{ request('academic_year') == $year->name ? 'selected' : '' }}>{{ $year->name }} {{ $year->is_current ? '('.__('current').')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">{{ __('professor') }}</label>
                            <select name="lecturer_id" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                                <option value="">{{ __('all_2') }}</option>
                                @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}" {{ request('lecturer_id') == $lecturer->id ? 'selected' : '' }}>{{ $lecturer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 3: Actions --}}
                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.manage-course-offerings') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold text-sm transition-colors">
                            <i class="fas fa-undo"></i> <span>{{ __('reset_2') }}</span>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Content --}}
            <div x-data="{ viewMode: '{{ request('view', 'grid') }}' }" @view-changed.window="viewMode = $event.detail">
                <div data-admin-results>

                {{-- GRID VIEW --}}
                <div x-show="viewMode === 'grid'" x-cloak>
                    @if($courseOfferings->isEmpty())
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                            <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-5">
                                <i class="fas fa-book-open text-gray-300 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-600 mb-2">{{ __('no_course_offerings') }}</h3>
                            <p class="text-gray-400 text-sm mb-6">{{ __('try_resetting_filters_or_add_a_new_offering') }}</p>
                            <a href="{{ route('admin.create-course-offering') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all">
                                <i class="fas fa-plus"></i> <span>{{ __('add_new') }}</span>
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach ($courseOfferings as $offering)
                                @php
                                    $today = now()->startOfDay();
                                    $status = match(true) {
                                        $today->lt($offering->start_date) => 'upcoming',
                                        $today->gt($offering->end_date) => 'expired',
                                        default => 'active',
                                    };
                                    $enrollmentCount = $offering->studentCourseEnrollments->count();
                                @endphp
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-200 group">
                                    {{-- Header --}}
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="space-y-2">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold {{ match($status) { 'active' => 'bg-emerald-50 text-emerald-700 border border-emerald-200', 'upcoming' => 'bg-amber-50 text-amber-700 border border-amber-200', default => 'bg-red-50 text-red-700 border border-red-200' } }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ match($status) { 'active' => 'bg-emerald-500', 'upcoming' => 'bg-amber-500', default => 'bg-red-500' } }}"></span>
                                                {{ match($status) { 'active' => __('active'), 'upcoming' => __('upcoming'), default => __('expired') } }}
                                            </span>
                                            <div class="flex flex-wrap gap-1.5">
                                                @if($offering->department)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                        {{ $offering->department->name_km }} (G{{ $offering->generation }})
                                                    </span>
                                                @endif
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
                                        <span class="text-sm font-semibold text-slate-700">{{ $offering->lecturer?->name ?? __('not_set') }}</span>
                                    </div>

                                    {{-- Enrollment Count --}}
                                    <div class="flex items-center gap-2.5 mb-4 pb-4 border-b border-gray-100">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                                            <i class="fas fa-users text-xs"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700">{{ $enrollmentCount }} {{ __('enrolled_students') }}</span>
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
                                            <p class="text-xs text-gray-400 italic">{{ __('no_schedule_yet') }}</p>
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
                            <h3 class="text-lg font-bold text-gray-600 mb-2">{{ __('no_course_offerings') }}</h3>
                            <p class="text-gray-400 text-sm mb-6">{{ __('try_resetting_filters_or_add_a_new_offering') }}</p>
                            <a href="{{ route('admin.create-course-offering') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all">
                                <i class="fas fa-plus"></i> <span>{{ __('add_new') }}</span>
                            </a>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('course') }}</th>
                                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('professor') }}</th>
                                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('semester_year') }}</th>
                                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('department_2') }}</th>
                                            <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('students') }}</th>
                                            <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('schedule') }}</th>
                                            <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('status') }}</th>
                                            <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('actions_2') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($courseOfferings as $offering)
                                            @php
                                                $today = now()->startOfDay();
                                                $status = match(true) {
                                                    $today->lt($offering->start_date) => 'upcoming',
                                                    $today->gt($offering->end_date) => 'expired',
                                                    default => 'active',
                                                };
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-5 py-4">
                                                    <div class="font-semibold text-gray-900 text-sm">{{ $offering->course?->title_km ?? $offering->course?->title_en ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <span class="text-sm text-gray-700">{{ $offering->lecturer?->name ?? __('not_set') }}</span>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <span class="text-sm text-gray-600">{{ $offering->semester }} / {{ $offering->academic_year }}</span>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <div class="flex flex-wrap gap-1">
                                                        @if($offering->department)
                                                            <span class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md font-bold border border-emerald-100">
                                                                {{ $offering->department->name_km }} (G{{ $offering->generation }})
                                                            </span>
                                                        @endif
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
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold {{ match($status) { 'active' => 'bg-emerald-50 text-emerald-700 border border-emerald-200', 'upcoming' => 'bg-amber-50 text-amber-700 border border-amber-200', default => 'bg-red-50 text-red-700 border border-red-200' } }}">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ match($status) { 'active' => 'bg-emerald-500', 'upcoming' => 'bg-amber-500', default => 'bg-red-500' } }}"></span>
                                                        {{ match($status) { 'active' => __('active'), 'upcoming' => __('upcoming'), default => __('expired') } }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    <div class="flex justify-center gap-1.5">
                                                        <a href="{{ route('admin.edit-course-offering', $offering->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-bold hover:bg-emerald-600 hover:text-white transition-colors">
                                                            <i class="fas fa-pen"></i> <span>{{ __('edit_3') }}</span>
                                                        </a>
                                                        <button onclick="openDeleteModal({{ $offering->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-600 hover:text-white transition-colors">
                                                            <i class="fas fa-trash"></i> <span>{{ __('delete_2') }}</span>
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
    </div>

    {{-- PRINTABLE AREA --}}
    {{-- PRINTABLE AREA --}}
    <div id="printable-schedule-container">
        @php
            function toKhmerNumber($number) {
                if(!$number) return '';
                $khmerNumbers = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
                return str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $khmerNumbers, (string)$number);
            }

            $allSchedules = collect();
            foreach($courseOfferings as $off) { foreach($off->schedules as $s) { $allSchedules->push($s); } }
            
            // Hardcoded bilingual headers exactly like the image
            $weekdayMap = [
                'Monday' => 'ចន្ទ/Monday', 
                'Tuesday' => 'អង្គារ/Tuesday', 
                'Wednesday' => 'ពុធ/Wednesday', 
                'Thursday' => 'ព្រហស្បតិ៍/Thursday', 
                'Friday' => 'សុក្រ/Friday'
            ];
            $weekendMap = ['Saturday' => 'សៅរ៍/Saturday', 'Sunday' => 'អាទិត្យ/Sunday'];
            
            $weekdaySchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekdayMap));
            $weekendSchedules = $allSchedules->filter(fn($s) => array_key_exists($s->day_of_week, $weekendMap));
            
            $shiftFilter = request('shift');
            if ($shiftFilter === 'weekday') { $weekendSchedules = collect(); } 
            elseif ($shiftFilter === 'weekend') { $weekdaySchedules = collect(); }
            
            $weekdayRows = $weekdaySchedules->groupBy(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->sortKeys();
            $weekendTimeSlots = $weekendSchedules->map(fn($s) => \Carbon\Carbon::parse($s->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($s->end_time)->format('H:i'))->unique()->sort();

            // Set Title Variables
            $currentDepartmentName = "គ្រប់គ្រងបណ្តាញកុំព្យូទ័រ"; // Default fallback
            if(request('department_id')){
                $dept = $departments->firstWhere('id', request('department_id'));
                if($dept) $currentDepartmentName = $dept->name_km;
            } elseif($courseOfferings->isNotEmpty()) {
                $first = $courseOfferings->first();
                if($first->department){ $currentDepartmentName = $first->department->name_km; }
            }

            $currentFacultyName = "មហាវិទ្យាល័យវិទ្យាសាស្ត្រ និងបច្ចេកវិទ្យា"; // Default fallback
            if(request('faculty_id')){
                $fac = $faculties->firstWhere('id', request('faculty_id'));
                if($fac) $currentFacultyName = $fac->name_km;
            } elseif($courseOfferings->isNotEmpty()) {
                $first = $courseOfferings->first();
                if($first->department && $first->department->faculty){ $currentFacultyName = $first->department->faculty->name_km; }
            }
        @endphp

        <div class="header-print-layout">
            <!-- Centered Kingdom Text -->
            <div class="kingdom-header">
                <h2>ព្រះរាជាណាចក្រកម្ពុជា</h2>
                <h2>ជាតិ សាសនា ព្រះមហាក្សត្រ</h2>
                <img src="{{ asset('assets/image/2.png') }}" alt="Line">
            </div>

            <!-- Absolute Left University Text -->
            <div class="uni-logo-text">
                <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo">
                <h3>សាកលវិទ្យាល័យជាតិមានជ័យ</h3>
                <h3>ការិយាល័យសិក្សា</h3>
            </div>
        </div>

        <div class="schedule-title-block">
            <h1>កាលវិភាគប្រចាំឆមាសទី{{ request('semester') == 'ឆមាសទី២' ? '២' : '១' }} / Timetable Semester {{ request('semester') == 'ឆមាសទី២' ? '2' : '1' }}</h1>
            <p>ជំនាន់ទី{{ toKhmerNumber(request('generation') ?? '១៦') }} ឆ្នាំទី៤ {{ $currentFacultyName }} ឆ្នាំសិក្សា {{ toKhmerNumber(request('academic_year') ?? '២០២៥-២០២៦') }}</p>
            <p>ចាប់ផ្តើមពីថ្ងៃ......................................................................................... វេនសិក្សា ចន្ទ-សុក្រ</p>
        </div>

        <div class="table-wrapper">
            @if($weekdayRows->isNotEmpty())
                <div class="specialty-title">ជំនាញ៖ {{ $currentDepartmentName }}</div>
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th style="width: 14%;">ម៉ោងសិក្សា</th>
                            @foreach($weekdayMap as $dayLabel) 
                                <th>{{ $dayLabel }}</th> 
                            @endforeach
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
                                        <span class="cell-subject">{{ $class->courseOffering->course->title_km ?? $class->courseOffering->course->title_en ?? 'N/A' }}</span>
                                        <span class="cell-lecturer">លោក {{ str_replace('Mr. ', '', $class->courseOffering->lecturer->name ?? '') }}</span>
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
                <div class="specialty-title">ជំនាញ៖ {{ $currentDepartmentName }} (សៅរ៍-អាទិត្យ)</div>
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th style="width: 14%;">ម៉ោងសិក្សា</th>
                            @foreach($weekendTimeSlots as $timeSlot) <th>{{ toKhmerNumber($timeSlot) }}</th> @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weekendMap as $dayKey => $dayLabel)
                        <tr>
                            <td style="font-weight: bold;">{{ $dayLabel }}</td>
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
                                        <span class="cell-lecturer">លោក {{ str_replace('Mr. ', '', $class->courseOffering->lecturer->name ?? '') }}</span>
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

        <!-- Footer Signatures exactly matching image blanks -->
        <div class="f-sigs">
            <div class="sig-block-left">
                <div class="sig-title-moul">បានឃើញ និងឯកភាព</div>
                <div class="sig-title-moul">ជ. សាកលវិទ្យាធិការ</div>
                <div class="sig-title-moul" style="margin-top: 0;">សាកលវិទ្យាធិការរង</div>
                <div class="sig-spacer"></div>
            </div>
            <div class="sig-block-right">
                <div class="sig-date-kh">ថ្ងៃ........................... ខែ...................... ឆ្នាំ...................... ព.ស ២៥៦...</div>
                <div class="sig-date-kh">បន្ទាយមានជ័យ ថ្ងៃទី........... ខែ........... ឆ្នាំ២០២...</div>
                <div class="sig-title-moul" style="margin-top: 8px;">ប្រធានការិយាល័យសិក្សា</div>
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
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('delete_course_offering') }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ __('are_you_sure_you_want_to_delete_this_data_this_action_cannot_be_undone') }}</p>
                    </div>
                    <div class="bg-gray-50 px-8 py-5 flex justify-center gap-3 rounded-b-2xl">
                        <button onclick="closeDeleteModal()" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-100 transition-colors">{{ __('cancel_2') }}</button>
                        <form id="delete-form" method="POST" action=""> @csrf @method('DELETE')
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-red-500/25 hover:from-red-500 hover:to-red-400 transition-all">{{ __('confirm_delete_2') }}</button>
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
            var facEl = document.querySelector('select[name=faculty_id]');
            var deptEl = document.querySelector('select[name=department_id]');
            var genEl = document.querySelector('select[name=generation]');
            var shiftEl = document.querySelector('select[name=shift]');
            var semEl = document.querySelector('select[name=semester]');
            var yearEl = document.querySelector('select[name=academic_year]');
            var lectEl = document.querySelector('select[name=lecturer_id]');
            var fac = facEl ? facEl.value : '';
            var dept = deptEl ? deptEl.value : '';
            var gen = genEl ? genEl.value : '';
            var shift = shiftEl ? shiftEl.value : '';
            var sem = semEl ? semEl.value : '';
            var year = yearEl ? yearEl.value : '';
            var lect = lectEl ? lectEl.value : '';
            if (!fac && !dept && !gen && !shift && !sem && !year && !lect) {
                showFilterAlert();
                return;
            }

            var printableContainer = document.getElementById('printable-schedule-container');
            var printBtn = document.querySelector('button[onclick*="printOrExport"]');

            fetch(window.location.href, {
                headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(response) { return response.text(); })
            .then(function(html) {
                var parsed = new DOMParser().parseFromString(html, 'text/html');
                var freshPrintable = parsed.querySelector('#printable-schedule-container');
                if (freshPrintable && printableContainer) {
                    printableContainer.innerHTML = freshPrintable.innerHTML;
                }

                if (action === 'print') {
                    window.print();
                } else if (action === 'word') {
                    exportToWord();
                }
            })
            .catch(function() {
                if (action === 'print') {
                    window.print();
                } else if (action === 'word') {
                    exportToWord();
                }
            });
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
                <h3 class="text-lg font-bold text-center text-gray-900">{{ __('please_select_data') }}</h3>
                <p class="mt-2 text-sm text-center text-gray-500">
                    {{ __('please_select') }} <span class="font-black text-amber-600">{{ __('study_program') }}</span> <span class="font-black text-amber-600">{{ __('generation') }}</span> <span class="font-black text-amber-600">{{ __('semester') }}</span> <span class="font-black text-amber-600">{{ __('academic_year') }}</span> {{ __('key_or') }} <span class="font-black text-amber-600">{{ __('professor') }}</span> {{ __('at_least_one_before_printing') }}
                </p>
                <div class="mt-6 flex justify-center">
                    <button type="button" onclick="closeFilterAlert()" class="px-6 py-2 text-sm font-bold text-white bg-amber-500 rounded-xl hover:bg-amber-600 shadow-lg shadow-amber-200 transition-all">
                        {{ __('confirm_2') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
