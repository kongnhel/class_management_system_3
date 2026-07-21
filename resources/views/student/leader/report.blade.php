<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&display=swap" rel="stylesheet">

    @php
        $totalPresent = $students->sum('present_count');
        $totalAbsent  = $students->sum('absent_count');
        $totalLate    = $students->sum('late_count') ?? 0;
        $totalPerm    = $students->sum('permission_count');
        $grandTotal   = $totalPresent + $totalPerm + $totalAbsent + $totalLate;
        $overallRate  = $grandTotal > 0 ? round((($totalPresent + $totalPerm) / $grandTotal) * 100) : 0;
    @endphp

    <style>
        #printable-report { display: none; }
        @media print {
            @page { size: A4 landscape; margin: 8mm; }
            body { margin: 0 !important; padding: 0 !important; background: #fff !important; font-family: 'Battambang', system-ui, sans-serif !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; zoom: 92%; }
            .no-print { display: none !important; }
            #printable-report { display: flex !important; flex-direction: column; width: 100% !important; }
            .font-moul { font-family: 'Moul', serif !important; font-weight: normal !important; }
            .pr-header { display: grid; grid-template-columns: 1fr 1fr 1fr; align-items: start; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
            .pr-header-left { text-align: center; display: flex; flex-direction: column; align-items: center; }
            .pr-header-left img { width: 80px; height: auto; margin-bottom: 5px; }
            .pr-header-left h3 { font-size: 10pt; color: #2a58ad; margin: 2px 0; line-height: 1.4; }
            .pr-header-center { text-align: center; display: flex; flex-direction: column; align-items: center; }
            .pr-header-center h2 { font-size: 11pt; margin: 2px 0; color: black; line-height: 1.4; }
            .pr-header-center img { width: 100px; height: auto; margin-top: 5px; }
            .pr-title { text-align: center; margin-bottom: 12px; }
            .pr-title h1 { font-size: 12pt; margin: 4px 0; color: black; }
            .pr-title p { font-size: 9pt; font-weight: bold; margin: 0; }
            .pr-summary { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin-bottom: 12px; border: 1pt solid #ccc; padding: 8px; }
            .pr-summary-item { text-align: center; padding: 4px; border: 1pt solid #ddd; }
            .pr-summary-item .label { font-size: 8pt; color: #555; text-transform: uppercase; font-weight: bold; }
            .pr-summary-item .value { font-size: 11pt; font-weight: bold; color: #000; }
            .pr-table { width: 100%; border-collapse: collapse; border: 1.5pt solid black; font-size: 9pt; }
            .pr-table th, .pr-table td { border: 1pt solid black; padding: 5px 8px; text-align: center; vertical-align: middle; }
            .pr-table th { background-color: #e8edf3 !important; font-size: 8pt; font-weight: bold; height: 28px; }
            .pr-table td:nth-child(2) { text-align: left; font-weight: bold; }
            .pr-sigs { display: flex; justify-content: space-between; margin-top: 25px; page-break-inside: avoid; }
            .pr-sig-block { text-align: center; width: 30%; }
            .pr-sig-title { font-size: 9pt; margin-bottom: 60px; }
            .pr-sig-role { font-size: 9pt; margin: 2px 0; }
            .pr-sig-date { font-size: 8pt; margin-bottom: 3px; }
        }
    </style>

    {{-- ═══ PRINT-ONLY: Professional Report ═══ --}}
    <div id="printable-report">
        <div>
            <div class="pr-header">
                <div class="pr-header-left">
                    <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo">
                    <h3 class="font-moul">សាកលវិទ្យាល័យជាតិមានជ័យ</h3>
                    <h3 class="font-moul">{{ __('ការិយាល័យសិក្សា') }}</h3>
                </div>
                <div class="pr-header-center">
                    <h2 class="font-moul">ព្រះរាជាណាចក្រកម្ពុជា</h2>
                    <h2 class="font-moul">ជាតិ សាសនា ព្រះមហាក្សត្រ</h2>
                    <img src="{{ asset('assets/image/2.png') }}" alt="Line">
                </div>
                <div></div>
            </div>

            <div class="pr-title">
                <h1 class="font-moul">របាយការណ៍វត្តមានសិស្ស — {{ $courseOffering->semester }} / {{ $courseOffering->academic_year }}</h1>
                <p>{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en ?? '' }} | {{ $courseOffering->lecturer->name ?? '' }}</p>
            </div>
        </div>

        <div class="pr-summary">
            <div class="pr-summary-item">
                <div class="label">{{ __('សិស្សសរុប') }}</div>
                <div class="value">{{ $students->count() }}</div>
            </div>
            <div class="pr-summary-item">
                <div class="label">{{ __('មក') }}</div>
                <div class="value">{{ $totalPresent }}</div>
            </div>
            <div class="pr-summary-item">
                <div class="label">{{ __('អវត្តមាន') }}</div>
                <div class="value">{{ $totalAbsent }}</div>
            </div>
            <div class="pr-summary-item">
                <div class="label">{{ __('ច្បាប់') }}</div>
                <div class="value">{{ $totalPerm }}</div>
            </div>
            <div class="pr-summary-item">
                <div class="label">{{ __('អត្រាវត្តមាន') }}</div>
                <div class="value">{{ $overallRate }}%</div>
            </div>
        </div>

        <table class="pr-table">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:30%">{{ __('និស្សិត') }}</th>
                    <th style="width:10%">{{ __('មក') }}</th>
                    <th style="width:10%">{{ __('យឺត') }}</th>
                    <th style="width:10%">{{ __('ច្បាប់') }}</th>
                    <th style="width:10%">{{ __('អវត្តមាន') }}</th>
                    <th style="width:10%">{{ __('ភាគរយ') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $data)
                    @php
                        $total = $data->present_count + $data->permission_count + $data->absent_count + ($data->late_count ?? 0);
                        $percentage = $total > 0 ? round((($data->present_count + $data->permission_count) / $total) * 100) : 0;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $data->studentProfile->full_name_km ?? $data->name }}</td>
                        <td>{{ $data->present_count }}</td>
                        <td>{{ $data->late_count ?? 0 }}</td>
                        <td>{{ $data->permission_count }}</td>
                        <td>{{ $data->absent_count }}</td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pr-sigs">
            <div class="pr-sig-block">
                <div class="pr-sig-title font-moul">រៀបចំដោយ</div>
                <div class="pr-sig-role font-moul">ប្រធានក្រុម</div>
            </div>
            <div class="pr-sig-block">
                <div class="pr-sig-date">
                    ថ្ងៃទី............. ខែ............. ឆ្នាំ២០......
                </div>
                <div class="pr-sig-title font-moul" style="margin-top:5px;">បានឃើញ និងឯកភាព</div>
                <div class="pr-sig-role font-moul">សាស្ត្រាចារ្យ</div>
            </div>
            <div class="pr-sig-block">
                <div class="pr-sig-title font-moul">ឯកភាពដោយ</div>
                <div class="pr-sig-role font-moul">ប្រធានការិយាល័យសិក្សា</div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════ SCREEN VIEW ═══════════════════ --}}
    <div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased no-print">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-6">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                <a href="{{ route('student.my-enrolled-courses') }}" class="hover:text-emerald-600 transition-colors">{{ __('មុខវិជ្ជារបស់ខ្ញុំ') }}</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span class="text-slate-600">{{ __('របាយការណ៍វត្តមាន') }}</span>
            </div>

            {{-- HERO Header --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-purple-700 text-white shadow-xl shadow-emerald-200/50">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-20 -left-10 w-72 h-72 bg-purple-400/10 rounded-full blur-3xl"></div>

                <div class="relative p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center">
                                <i class="fas fa-file-chart-column text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-xl sm:text-2xl font-black leading-tight">{{ __('របាយការណ៍វត្តមានរួម') }}</h1>
                                <p class="text-emerald-200 text-sm mt-1">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</p>
                                @if($courseOffering->lecturer)
                                    <p class="text-emerald-300 text-xs mt-0.5">{{ __('សាស្ត្រាចារ្យ') }}: {{ $courseOffering->lecturer->name }}</p>
                                @endif
                            </div>
                        </div>

                        <button onclick="window.print()"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-xl font-bold text-sm transition-all active:scale-95">
                            <i class="fas fa-print"></i>
                            <span>{{ __('បោះពុម្ព') }}</span>
                        </button>
                    </div>

                    {{-- Meta --}}
                    <div class="flex flex-wrap gap-4 mt-6 pt-5 border-t border-white/10">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-xs"><i class="fas fa-calendar-alt"></i></div>
                            <div>
                                <p class="text-emerald-300 text-[10px] font-bold uppercase tracking-wider">{{ __('ឆ្នាំសិក្សា') }}</p>
                                <p class="text-sm font-bold">{{ $courseOffering->academic_year }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-xs"><i class="fas fa-graduation-cap"></i></div>
                            <div>
                                <p class="text-emerald-300 text-[10px] font-bold uppercase tracking-wider">{{ __('ឆមាស') }}</p>
                                <p class="text-sm font-bold">{{ $courseOffering->semester }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-xs"><i class="fas fa-users"></i></div>
                            <div>
                                <p class="text-emerald-300 text-[10px] font-bold uppercase tracking-wider">{{ __('និស្សិត') }}</p>
                                <p class="text-sm font-bold">{{ $students->count() }} {{ __('នាក់') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SUMMARY STATS --}}
            <div class="grid grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fas fa-user-check"></i></div>
                    <div><p class="text-[10px] font-bold text-gray-400 uppercase">{{ __('មក') }}</p><h3 class="text-lg font-black text-gray-800">{{ $totalPresent }}</h3></div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-rose-100 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center"><i class="fas fa-user-times"></i></div>
                    <div><p class="text-[10px] font-bold text-gray-400 uppercase">{{ __('អវត្តមាន') }}</p><h3 class="text-lg font-black text-gray-800">{{ $totalAbsent }}</h3></div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-amber-100 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fas fa-clock"></i></div>
                    <div><p class="text-[10px] font-bold text-gray-400 uppercase">{{ __('មកយឺត') }}</p><h3 class="text-lg font-black text-gray-800">{{ $totalLate }}</h3></div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fas fa-file-contract"></i></div>
                    <div><p class="text-[10px] font-bold text-gray-400 uppercase">{{ __('ច្បាប់') }}</p><h3 class="text-lg font-black text-gray-800">{{ $totalPerm }}</h3></div>
                </div>
                <div class="col-span-3 lg:col-span-2 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">{{ __('អត្រាវត្តមានសរុប') }}</p>
                        @php $rateColor = $overallRate >= 75 ? 'emerald' : 'rose'; @endphp
                        <span class="text-sm font-black text-{{ $rateColor }}-600">{{ $overallRate }}%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $rateColor }}-500 rounded-full transition-all duration-700" style="width: {{ $overallRate }}%"></div>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm"><i class="fas fa-table"></i></div>
                        <h3 class="text-sm font-bold text-gray-800">{{ __('បញ្ជីវត្តមាននិស្សិត') }}</h3>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-50 text-slate-500">{{ $students->count() }} {{ __('នាក់') }}</span>
                </div>

                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-5 py-3.5 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider w-12">#</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('និស្សិត') }}</th>
                                <th class="px-3 py-3.5 text-center text-[11px] font-bold text-emerald-600 uppercase tracking-wider">{{ __('មក') }}</th>
                                <th class="px-3 py-3.5 text-center text-[11px] font-bold text-amber-600 uppercase tracking-wider">{{ __('មកយឺត') }}</th>
                                <th class="px-3 py-3.5 text-center text-[11px] font-bold text-emerald-600 uppercase tracking-wider">{{ __('ច្បាប់') }}</th>
                                <th class="px-3 py-3.5 text-center text-[11px] font-bold text-rose-600 uppercase tracking-wider">{{ __('អវត្តមាន') }}</th>
                                <th class="px-5 py-3.5 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('ភាគរយ') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($students as $index => $data)
                                @php
                                    $total = $data->present_count + $data->permission_count + $data->absent_count + ($data->late_count ?? 0);
                                    $percentage = $total > 0 ? round((($data->present_count + $data->permission_count) / $total) * 100) : 0;
                                    $rowColor = $percentage < 75 ? 'rose' : 'emerald';
                                @endphp
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 py-3.5 text-center text-xs font-bold text-slate-300">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            @php $pic = $data->studentProfile?->profile_picture_url ?? $data->profile?->profile_picture_url ?? $data->avatar; @endphp
                                            <div class="w-9 h-9 rounded-xl overflow-hidden flex items-center justify-center flex-shrink-0 {{ $pic ? '' : 'bg-emerald-50 text-emerald-600' }}">
                                                @if($pic)
                                                    <img src="{{ $pic }}" alt="{{ $data->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="font-black text-xs">{{ Str::substr($data->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-gray-800 truncate">{{ $data->studentProfile->full_name_km ?? $data->name }}</p>
                                                <p class="text-[10px] text-gray-400 font-semibold">{{ $data->student_id_code ?? '#' . $data->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3.5 text-center"><span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-black">{{ $data->present_count }}</span></td>
                                    <td class="px-3 py-3.5 text-center"><span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-700 text-xs font-black">{{ $data->late_count ?? 0 }}</span></td>
                                    <td class="px-3 py-3.5 text-center"><span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-black">{{ $data->permission_count }}</span></td>
                                    <td class="px-3 py-3.5 text-center"><span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-700 text-xs font-black">{{ $data->absent_count }}</span></td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5 justify-end">
                                            <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-{{ $rowColor }}-500 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-xs font-black text-{{ $rowColor }}-600 w-10 text-right">{{ $percentage }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile --}}
                <div class="md:hidden divide-y divide-slate-100">
                    @foreach($students as $index => $data)
                        @php
                            $total = $data->present_count + $data->permission_count + $data->absent_count + ($data->late_count ?? 0);
                            $percentage = $total > 0 ? round((($data->present_count + $data->permission_count) / $total) * 100) : 0;
                            $rowColor = $percentage < 75 ? 'rose' : 'emerald';
                        @endphp
                        <div class="p-4">
                            <div class="flex items-center gap-3 mb-3">
                                @php $pic = $data->studentProfile?->profile_picture_url ?? $data->profile?->profile_picture_url ?? $data->avatar; @endphp
                                <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center flex-shrink-0 {{ $pic ? '' : 'bg-emerald-50 text-emerald-600' }}">
                                    @if($pic)
                                        <img src="{{ $pic }}" alt="{{ $data->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="font-black text-sm">{{ Str::substr($data->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $data->studentProfile->full_name_km ?? $data->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-semibold">{{ $data->student_id_code ?? '#' . $data->id }}</p>
                                </div>
                                <span class="text-sm font-black text-{{ $rowColor }}-600">{{ $percentage }}%</span>
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                                <div class="text-center bg-emerald-50 rounded-lg py-1.5">
                                    <p class="text-[9px] font-bold text-emerald-500 uppercase">{{ __('មក') }}</p>
                                    <p class="text-sm font-black text-emerald-700">{{ $data->present_count }}</p>
                                </div>
                                <div class="text-center bg-amber-50 rounded-lg py-1.5">
                                    <p class="text-[9px] font-bold text-amber-500 uppercase">{{ __('យឺត') }}</p>
                                    <p class="text-sm font-black text-amber-700">{{ $data->late_count ?? 0 }}</p>
                                </div>
                                <div class="text-center bg-emerald-50 rounded-lg py-1.5">
                                    <p class="text-[9px] font-bold text-emerald-500 uppercase">{{ __('ច្បាប់') }}</p>
                                    <p class="text-sm font-black text-emerald-700">{{ $data->permission_count }}</p>
                                </div>
                                <div class="text-center bg-rose-50 rounded-lg py-1.5">
                                    <p class="text-[9px] font-bold text-rose-500 uppercase">{{ __('អវ') }}</p>
                                    <p class="text-sm font-black text-rose-700">{{ $data->absent_count }}</p>
                                </div>
                            </div>
                            <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-{{ $rowColor }}-500 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty --}}
                @if($students->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 bg-gray-50 text-gray-300 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-users-slash text-xl"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-400">{{ __('មិនមាននិស្សិតចុះឈ្មោះ') }}</p>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fas fa-info-circle"></i>
                    {{ __('វត្តមានតិចជាង ៧៥% បង្ហាញជាពណ៌ក្រហម') }}
                </p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    {{ __('កាលបរិច្ឆេទ៖') }} {{ now()->format('d M Y') }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
