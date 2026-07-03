<x-app-layout>
    <div class="bg-slate-50 min-h-screen font-['Battambang'] antialiased">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-6">

            {{-- Breadcrumb + Back --}}
            <div class="no-print flex items-center gap-2 text-xs font-semibold text-slate-400">
                <a href="{{ route('student.my-enrolled-courses') }}" class="hover:text-emerald-600 transition-colors">{{ __('មុខវិជ្ជារបស់ខ្ញុំ') }}</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span class="text-slate-600">{{ __('របាយការណ៍វត្តមាន') }}</span>
            </div>

            {{-- HERO Header --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-purple-700 text-white shadow-xl shadow-emerald-200/50 no-print">
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

                    {{-- mini meta row --}}
                    <div class="flex flex-wrap gap-4 mt-6 pt-5 border-t border-white/10">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-xs">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <p class="text-emerald-300 text-[10px] font-bold uppercase tracking-wider">{{ __('ឆ្នាំសិក្សា') }}</p>
                                <p class="text-sm font-bold">{{ $courseOffering->academic_year }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-xs">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div>
                                <p class="text-emerald-300 text-[10px] font-bold uppercase tracking-wider">{{ __('ឆមាស') }}</p>
                                <p class="text-sm font-bold">{{ $courseOffering->semester }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-xs">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p class="text-emerald-300 text-[10px] font-bold uppercase tracking-wider">{{ __('និស្សិត') }}</p>
                                <p class="text-sm font-bold">{{ $students->count() }} {{ __('នាក់') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SUMMARY STATS --}}
            @php
                $totalPresent = $students->sum('present_count');
                $totalAbsent  = $students->sum('absent_count');
                $totalLate    = $students->sum('late_count') ?? 0;
                $totalPerm    = $students->sum('permission_count');
                $grandTotal   = $totalPresent + $totalPerm + $totalAbsent + $totalLate;
                $overallRate  = $grandTotal > 0 ? round((($totalPresent + $totalPerm) / $grandTotal) * 100) : 0;
            @endphp
            <div class="grid grid-cols-3 lg:grid-cols-6 gap-3 no-print">
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

                {{-- Desktop Table --}}
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
                                            @php
                                                $pic = $data->studentProfile?->profile_picture_url
                                                    ?? $data->profile?->profile_picture_url
                                                    ?? $data->avatar;
                                            @endphp
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

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-slate-100">
                    @foreach($students as $index => $data)
                        @php
                            $total = $data->present_count + $data->permission_count + $data->absent_count + ($data->late_count ?? 0);
                            $percentage = $total > 0 ? round((($data->present_count + $data->permission_count) / $total) * 100) : 0;
                            $rowColor = $percentage < 75 ? 'rose' : 'emerald';
                        @endphp
                        <div class="p-4">
                            <div class="flex items-center gap-3 mb-3">
                                @php
                                    $pic = $data->studentProfile?->profile_picture_url
                                        ?? $data->profile?->profile_picture_url
                                        ?? $data->avatar;
                                @endphp
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

                {{-- Empty State --}}
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
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 no-print">
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

    <style>
        .overflow-x-auto { -webkit-overflow-scrolling: touch; }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .bg-slate-50 { background: white !important; }
            .rounded-3xl, .rounded-2xl { border-radius: 0 !important; }
            .shadow-sm, .shadow-xl { box-shadow: none !important; }
            table { width: 100% !important; border-collapse: collapse !important; font-size: 11px; }
            tr { page-break-inside: avoid; }
        }
    </style>
</x-app-layout>