<x-app-layout>
    <div class="bg-gray-50 min-h-screen font-sans text-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-calendar-check text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</h1>
                        <p class="text-gray-500 mt-0.5">វត្តមានសិស្សក្នុងមុខវិជ្ជា {{ $courseOffering->semester }} / {{ $courseOffering->academic_year }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.attendance.index') }}" class="flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-xl font-bold shadow-sm border border-gray-200 transition-all text-sm">
                    <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
                </a>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-users text-gray-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">សិស្សសរុប</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_students'] }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-list text-emerald-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">កំណត់ត្រាសរុប</span>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $stats['total_records'] }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">មានវត្តមាន</span>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $stats['present_total'] }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                            <i class="fas fa-times-circle text-rose-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">អវត្តមាន</span>
                    </div>
                    <div class="text-2xl font-bold text-rose-600">{{ $stats['absent_total'] }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                            <i class="fas fa-clock text-amber-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">យឺត</span>
                    </div>
                    <div class="text-2xl font-bold text-amber-600">{{ $stats['late_total'] }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-percentage text-emerald-500"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase">អត្រាវត្តមាន</span>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $stats['overall_rate'] }}%</div>
                </div>
            </div>

            {{-- Attendance Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">វត្តមានសិស្ស</h3>
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-bold text-gray-500">{{ $studentAttendance->count() }} នាក់</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ឈ្មោះ</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">សរុប</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">មានវត្តមាន</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">អវត្តមាន</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">យឺត</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">អនុគ្រោះ</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">អត្រា</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($studentAttendance as $index => $data)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-400 font-bold">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                            <span class="text-white font-bold text-xs">{{ mb_substr($data['student']->name ?? 'S', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-sm text-gray-900">{{ $data['student']->name ?? '-' }}</div>
                                            @if($data['student']->studentProfile)
                                                <div class="text-xs text-gray-400">{{ $data['student']->studentProfile->full_name_km ?? '' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">{{ $data['total_days'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg bg-emerald-50 text-emerald-600 font-bold text-xs">
                                        {{ $data['present_days'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg bg-rose-50 text-rose-600 font-bold text-xs">
                                        {{ $data['absent_days'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg bg-amber-50 text-amber-600 font-bold text-xs">
                                        {{ $data['late_days'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg bg-emerald-50 text-emerald-600 font-bold text-xs">
                                        {{ $data['permission_days'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $rate = $data['attendance_rate'];
                                        if ($rate >= 80) {
                                            $color = 'emerald';
                                        } elseif ($rate >= 60) {
                                            $color = 'amber';
                                        } else {
                                            $color = 'rose';
                                        }
                                    @endphp
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full
                                                {{ $color === 'emerald' ? 'bg-emerald-500' : ($color === 'amber' ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                style="width: {{ $rate }}%"></div>
                                        </div>
                                        <span class="text-sm font-bold
                                            {{ $color === 'emerald' ? 'text-emerald-600' : ($color === 'amber' ? 'text-amber-600' : 'text-rose-600') }}">
                                            {{ $rate }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-bold text-gray-400">មិនមានទិន្នន័យវត្តមាន</p>
                                        <p class="text-xs text-gray-300">សូមពិនិត្យមើលកំណត់ត្រាវត្តមាននៅក្នុងផ្នែកគ្រប់គ្រង</p>
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
</x-app-layout>
