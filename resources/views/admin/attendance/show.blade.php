<x-app-layout>
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">

        {{-- Hero Header --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-28 pt-10 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-check text-emerald-300 text-xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">
                                    {{ $courseOffering->course->title_km ?? $courseOffering->course->title_en ?? 'N/A' }}
                                </h2>
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    {{ $courseOffering->semester }}
                                </span>
                            </div>
                            <p class="text-slate-400 mt-1 text-sm flex items-center gap-2 flex-wrap">
                                <span>{{ $courseOffering->academic_year }}</span>
                                @if($courseOffering->department)
                                    <span>·</span>
                                    <span>{{ $courseOffering->department->name_km }} @if($courseOffering->generation)(G{{ $courseOffering->generation }})@endif</span>
                                @endif
                                @if($courseOffering->lecturer)
                                    <span>·</span>
                                    <span class="text-slate-300"><i class="fas fa-user-tie text-xs mr-1 text-slate-400"></i> {{ $courseOffering->lecturer->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('admin.attendance.export', array_merge([$courseOffering->id], request()->only(['date_from', 'date_to']))) }}"
                           class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 rounded-xl font-bold transition-all text-sm shadow-md active:scale-95">
                            <i class="fas fa-file-excel"></i>
                            <span>{{ __('export_xlsx') }}</span>
                        </a>
                        <a href="{{ route('admin.attendance.index') }}"
                           class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl font-bold transition-all text-sm backdrop-blur-sm active:scale-95">
                            <i class="fas fa-arrow-left"></i>
                            <span>{{ __('go_back') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 pb-12 relative z-10">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 text-slate-600">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 truncate">{{ __('total_students') }}</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-600">
                        <i class="fas fa-list text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 truncate">{{ __('total_records') }}</p>
                        <p class="mt-1 text-2xl font-bold text-blue-600">{{ number_format($stats['total_records']) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0 text-emerald-600">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 truncate">{{ __('present') }}</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-600">{{ number_format($stats['present_total']) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0 text-rose-600">
                        <i class="fas fa-times-circle text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 truncate">{{ __('absent') }}</p>
                        <p class="mt-1 text-2xl font-bold text-rose-600">{{ number_format($stats['absent_total']) }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 col-span-2 lg:col-span-1 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center flex-shrink-0 text-teal-600">
                        <i class="fas fa-percentage text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 truncate">{{ __('attendance_rate') }}</p>
                        <p class="mt-1 text-2xl font-bold text-teal-600">{{ $stats['overall_rate'] }}%</p>
                    </div>
                </div>
            </div>

            {{-- Filter Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-8">
                <form method="GET" action="{{ route('admin.attendance.show', $courseOffering->id) }}" data-admin-realtime-filter>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                        <div class="lg:col-span-5">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('search') }}</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="{{ __('search_by_name_or_email') }}"
                                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all outline-none">
                            </div>
                        </div>
                        <div class="lg:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('status') }}</label>
                            <select name="attendance_status"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">{{ __('all_2') }}</option>
                                <option value="no_records" {{ request('attendance_status') === 'no_records' ? 'selected' : '' }}>{{ __('no_attendance_records') }}</option>
                                <option value="low_attendance" {{ request('attendance_status') === 'low_attendance' ? 'selected' : '' }}>{{ __('low_attendance') }}</option>
                                <option value="below_passing" {{ request('attendance_status') === 'below_passing' ? 'selected' : '' }}>{{ __('below_passing_score') }}</option>
                                <option value="good_attendance" {{ request('attendance_status') === 'good_attendance' ? 'selected' : '' }}>{{ __('good_attendance') }}</option>
                            </select>
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('from_date') }}</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" aria-label="From date" onchange="this.form.requestSubmit()"
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('to_date') }}</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" aria-label="To date" onchange="this.form.requestSubmit()"
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                        </div>
                    </div>
                </form>
            </div>

            <div data-admin-results>
                {{-- Attendance Table Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fas fa-user-check text-sm"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('student_attendance') }}</h3>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            {{ $studentAttendance->count() }} {{ __('students_2') }}
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50/80">
                                    <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                                    <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">{{ __('name') }}</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('student_id') }}</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider bg-slate-50/50">{{ __('total_2') }}</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-bold text-emerald-700 uppercase tracking-wider bg-emerald-50/40">{{ __('attendance_score') }}</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-bold text-emerald-700 uppercase tracking-wider">{{ __('present') }}</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-bold text-rose-600 uppercase tracking-wider">{{ __('absent') }}</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-bold text-amber-600 uppercase tracking-wider">{{ __('permission') }}</th>
                                    <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[140px]">{{ __('rate') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-sm">
                                @forelse($studentAttendance as $index => $data)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3.5 text-center font-bold text-gray-400 text-xs">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $profilePic = $data['student']?->studentProfile?->profile_picture_url ?? $data['student']?->profile?->profile_picture_url ?? null;
                                                $stName = $data['student']->name ?? '-';
                                                $kmName = $data['student']->studentProfile?->full_name_km ?? null;
                                            @endphp
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center flex-shrink-0 overflow-hidden shadow-sm">
                                                @if($profilePic)
                                                    <img src="{{ $profilePic }}?tr=w-80,h-80,fo-face" class="w-full h-full object-cover" alt="{{ $stName }}">
                                                @else
                                                    <span class="text-white font-bold text-sm">{{ mb_strtoupper(mb_substr($kmName ?: $stName, 0, 1, 'UTF-8'), 'UTF-8') }}</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-gray-900 truncate">{{ $kmName ?: $stName }}</div>
                                                @if($kmName && $stName !== $kmName)
                                                    <div class="text-xs text-gray-400 truncate">{{ $stName }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-mono text-xs font-bold text-gray-600">
                                        {{ $data['student']->student_id_code ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-bold text-gray-800 bg-slate-50/30">
                                        {{ $data['total_days'] }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center bg-emerald-50/20">
                                        @php
                                            $attScore = $data['attendance_score'] ?? 0;
                                            $attPass = $attScore >= 10;
                                        @endphp
                                        <span class="inline-flex items-center justify-center min-w-[50px] px-2.5 py-1 rounded-lg {{ $attPass ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }} font-black text-xs">
                                            {{ number_format($attScore, 1) }} <span class="text-gray-400 font-normal ml-0.5">/ 15</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] h-7 px-2 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-100">
                                            {{ $data['present_days'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] h-7 px-2 rounded-lg {{ $data['absent_days'] > 0 ? 'bg-rose-50 text-rose-700 border border-rose-100' : 'bg-gray-50 text-gray-500' }} font-bold text-xs">
                                            {{ $data['absent_days'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] h-7 px-2 rounded-lg {{ $data['permission_days'] > 0 ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-gray-50 text-gray-500' }} font-bold text-xs">
                                            {{ $data['permission_days'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @php
                                            $rate = $data['attendance_rate'];
                                            $barColor = match(true) {
                                                $rate >= 80 => 'bg-emerald-500',
                                                $rate >= 60 => 'bg-amber-500',
                                                default => 'bg-rose-500',
                                            };
                                            $textColor = match(true) {
                                                $rate >= 80 => 'text-emerald-700',
                                                $rate >= 60 => 'text-amber-700',
                                                default => 'text-rose-700',
                                            };
                                        @endphp
                                        <div class="flex items-center justify-center gap-2.5">
                                            <div class="w-16 bg-gray-100 rounded-full h-2 overflow-hidden border border-gray-200">
                                                <div class="h-2 rounded-full {{ $barColor }} transition-all duration-300" style="width: {{ $rate }}%"></div>
                                            </div>
                                            <span class="text-xs font-black {{ $textColor }} w-10 text-right">{{ $rate }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-16">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-300">
                                                <i class="fas fa-inbox text-2xl"></i>
                                            </div>
                                            <h4 class="text-base font-bold text-gray-600">{{ __('no_attendance_data') }}</h4>
                                            <p class="text-xs text-gray-400">{{ __('check_attendance_records_in_the_management_section') }}</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
