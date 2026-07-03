<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap" rel="stylesheet">

    @php
        $totalStudents = $students->count();
        $totalSessions = $students->sum(function($s) { return ($s->present_count ?? 0) + ($s->permission_count ?? 0) + ($s->absent_count ?? 0) + ($s->late_count ?? 0); });
        $totalPresent = $students->sum('present_count');
        $totalPermission = $students->sum('permission_count');
        $totalAbsent = $students->sum('absent_count');
        $totalLate = $students->sum('late_count');
        $allSessions = $totalPresent + $totalPermission + $totalAbsent + $totalLate;
        $avgAttendance = $allSessions > 0 ? (($totalPresent + $totalLate) / $allSessions) * 100 : 0;
    @endphp

    <div class="py-6 md:py-10 bg-gray-50 min-h-screen font-['Battambang']">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-400 mb-1">
                        <a href="{{ route('professor.my-course-offerings') }}" class="hover:text-green-600 transition-colors">{{ __('វគ្គសិក្សា') }}</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-gray-600 font-bold">{{ $courseOffering->course->name_km }}</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800">
                        {{ __('របាយការណ៍វត្តមានសរុប') }}
                    </h1>
                </div>
                <button onclick="window.print()" 
                        class="no-print inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    {{ __('បោះពុម្ព') }}
                </button>
            </div>

            {{-- Print Header --}}
            <div class="hidden print:block text-center mb-8">
                <h1 class="text-lg font-bold mb-0.5">ព្រះរាជាណាចក្រកម្ពុជា</h1>
                <h2 class="text-base font-bold text-gray-600">ជាតិ សាសនា ព្រះមហាក្សត្រ</h2>
                <div class="w-20 h-[1px] bg-black mx-auto mt-2 mb-6"></div>
                <div class="flex justify-between text-left text-sm mt-6 px-4">
                    <div>
                        <p class="font-bold">គ្រឹះស្ថានសិក្សា៖ ............................................</p>
                        <p class="font-bold mt-1">មុខវិជ្ជា៖ {{ $courseOffering->course->name_km }}</p>
                        <p class="font-bold mt-1">ឆ្នាំសិក្សា៖ {{ $courseOffering->academic_year }} | {{ $courseOffering->semester }}</p>
                    </div>
                    <div class="text-right">
                        <p>កាលបរិច្ឆេទ៖ {{ date('d/m/Y') }}</p>
                    </div>
                </div>
                <h3 class="text-lg font-bold mt-8 underline decoration-double">របាយការណ៍វត្តមានសរុប</h3>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 no-print">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">និស្សិតសរុប</p>
                            <p class="text-2xl font-extrabold text-gray-800">{{ $totalStudents }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">វត្តមាន</p>
                            <p class="text-2xl font-extrabold text-emerald-600">{{ $totalPresent + $totalLate }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">សុំច្បាប់</p>
                            <p class="text-2xl font-extrabold text-amber-600">{{ $totalPermission }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">អវត្តមាន</p>
                            <p class="text-2xl font-extrabold text-rose-600">{{ $totalAbsent }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course Info Bar --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6 shadow-sm no-print">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-green-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">{{ $courseOffering->course->name_km }}</h3>
                            <div class="flex items-center gap-3 text-xs text-gray-400 font-medium mt-0.5">
                                <span>{{ $courseOffering->academic_year }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <span>{{ $courseOffering->semester }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <span>{{ $totalStudents }} និស្សិត</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">អត្រាវត្តមានជាមធ្យម</p>
                            <p class="text-xl font-extrabold {{ $avgAttendance >= 75 ? 'text-emerald-600' : ($avgAttendance >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                                {{ number_format($avgAttendance, 1) }}%
                            </p>
                        </div>
                        <div class="w-16 h-16 relative">
                            <svg class="w-16 h-16 -rotate-90" viewBox="0 0 36 36">
                                <path class="text-gray-100" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="{{ $avgAttendance >= 75 ? 'text-emerald-500' : ($avgAttendance >= 50 ? 'text-amber-500' : 'text-rose-500') }}" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="{{ $avgAttendance }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Students Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between no-print">
                    <h3 class="font-bold text-gray-700">បញ្ជីឈ្មោះនិស្សិត</h3>
                    <span class="text-xs text-gray-400 font-medium bg-gray-50 px-3 py-1 rounded-full">{{ $totalStudents }} នាក់</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">ឈ្មោះនិស្សិត</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-emerald-500 uppercase tracking-wider">P</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-amber-500 uppercase tracking-wider">L</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-rose-500 uppercase tracking-wider">A</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">សរុប</th>
                                <th class="px-6 py-3 text-right text-[11px] font-bold text-gray-400 uppercase tracking-wider">ភាគរយ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($students as $index => $data)
                                @php
                                    $total = ($data->present_count ?? 0) + ($data->permission_count ?? 0) + ($data->absent_count ?? 0) + ($data->late_count ?? 0);
                                    $percentage = $total > 0 ? (($data->present_count + ($data->late_count ?? 0)) / $total) * 100 : 0;
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3.5 text-sm font-bold text-gray-300">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $profilePic = $data->userProfile?->profile_picture_url ?? $data->studentProfile?->profile_picture_url;
                                            @endphp
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                @if($profilePic)
                                                    <img src="{{ $profilePic }}?tr=w-80,h-80,fo-face" class="w-full h-full object-cover" alt="">
                                                @else
                                                    <span class="text-white font-bold text-xs">{{ mb_substr($data->studentProfile->full_name_km ?? $data->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-700">{{ $data->studentProfile->full_name_km ?? $data->name }}</p>
                                                <p class="text-[11px] text-gray-400 font-medium">{{ $data->student_id_code ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg bg-emerald-50 text-emerald-600 font-bold text-sm">{{ $data->present_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg bg-amber-50 text-amber-600 font-bold text-sm">{{ $data->permission_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg bg-rose-50 text-rose-600 font-bold text-sm">{{ $data->absent_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-sm font-bold text-gray-500">{{ $total }}</td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center justify-end gap-3">
                                            <div class="w-20 bg-gray-100 h-2 rounded-full overflow-hidden no-print">
                                                <div class="h-full rounded-full transition-all duration-500 {{ $percentage >= 75 ? 'bg-emerald-500' : ($percentage >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-sm font-extrabold min-w-[42px] text-right {{ $percentage >= 75 ? 'text-emerald-600' : ($percentage >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                                                {{ number_format($percentage, 0) }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-400">មិនទាន់មានទិន្នន័យវត្តមាន</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Signature Section --}}
                <div class="px-6 py-8 grid grid-cols-2 gap-8 border-t border-gray-100 bg-gray-50/30 print:bg-white print:mt-8">
                    <div class="text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-14 print:mb-20">ហត្ថលេខាសាស្ត្រាចារ្យ</p>
                        <div class="w-32 h-px bg-gray-300 mx-auto print:bg-black"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-14 print:mb-20">ការិយាល័យសិក្សា</p>
                        <div class="w-32 h-px bg-gray-300 mx-auto print:bg-black"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; -webkit-print-color-adjust: exact; }
            .py-6, .py-10 { padding: 0 !important; margin: 0 !important; }
            .max-w-6xl { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
            table { width: 100% !important; }
            .print\:text-black { color: black !important; }
            .rounded-2xl, .rounded-3xl { border-radius: 0 !important; }
            .shadow-sm { box-shadow: none !important; }
            @page { margin: 1.5cm; }
        }
    </style>
</x-app-layout>
