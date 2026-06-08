<x-app-layout>
    <div class="min-h-screen bg-gray-100 font-sans text-gray-900">
        <div class="bg-slate-900 text-white pb-32 pt-12 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</h2>
                        <p class="text-slate-400 mt-2 max-w-2xl text-sm leading-relaxed">វត្តមានសិស្សក្នុងមុខវិជ្ជា {{ $courseOffering->semester }} / {{ $courseOffering->academic_year }}</p>
                    </div>
                    <a href="{{ route('admin.attendance.index') }}" class="flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white px-4 py-2.5 rounded-lg font-bold shadow-lg transition-all">
                        <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-10">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-gray-900">{{ $stats['total_students'] }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">សិស្សសរុប</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-blue-600">{{ $stats['total_records'] }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">កំណត់ត្រាសរុប</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-emerald-600">{{ $stats['present_total'] }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">មានវត្តមាន</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-rose-600">{{ $stats['absent_total'] }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">អវត្តមាន</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-amber-600">{{ $stats['late_total'] }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">យឺត</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-indigo-600">{{ $stats['overall_rate'] }}%</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">អត្រាវត្តមាន</div>
                </div>
            </div>

            {{-- Attendance Table --}}
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-bold text-gray-900">វត្តមានសិស្ស</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">ឈ្មោះ</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-600 uppercase">សរុប</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-600 uppercase">មានវត្តមាន</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-600 uppercase">អវត្តមាន</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-600 uppercase">យឺត</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-600 uppercase">អនុគ្រោះ</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-600 uppercase">អត្រា</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($studentAttendance as $index => $data)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $data['student']->name ?? '-' }}</div>
                                @if($data['student']->profile)
                                <div class="text-xs text-gray-500">{{ $data['student']->profile->full_name_km ?? '' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-bold">{{ $data['total_days'] }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-emerald-600">{{ $data['present_days'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-rose-600">{{ $data['absent_days'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-amber-600">{{ $data['late_days'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-indigo-600">{{ $data['permission_days'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $rate = $data['attendance_rate'];
                                    $color = $rate >= 80 ? 'emerald' : ($rate >= 60 ? 'amber' : 'rose');
                                @endphp
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $color }}-500 h-2 rounded-full" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold text-{{ $color }}-600">{{ $rate }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">មិនមានទិន្នន័យវត្តមាន</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
