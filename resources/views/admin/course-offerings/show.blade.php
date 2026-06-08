<x-app-layout>
    <div class="min-h-screen bg-gray-100 font-sans text-gray-900">
        <div class="bg-slate-900 text-white pb-32 pt-12 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 text-xs font-bold uppercase tracking-wider border border-emerald-500/30">
                                {{ $courseOffering->academic_year }}
                            </span>
                            @php
                                $today = now()->startOfDay();
                                $isActive = $today->between($courseOffering->start_date, $courseOffering->end_date);
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider border {{ $isActive ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-rose-50 text-rose-600 border-rose-200' }}">
                                {{ $isActive ? 'Active' : 'Expired' }}
                            </span>
                        </div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</h2>
                        <p class="text-slate-400 mt-2 max-w-2xl text-sm leading-relaxed">ព័ត៌មានលម្អិតនៃការផ្តល់ជូនមុខវិជ្ជា</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.edit-course-offering', $courseOffering->id) }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-lg font-bold shadow-lg transition-all">
                            <i class="fas fa-edit"></i> កែប្រែ
                        </a>
                        <a href="{{ route('admin.manage-course-offerings') }}" class="flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white px-5 py-2.5 rounded-lg font-bold shadow-lg transition-all">
                            <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Course Info --}}
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-book text-blue-500"></i> ព័ត៌មានមុខវិជ្ជា
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">ឈ្មោះមុខវិជ្ជា</label>
                            <p class="text-gray-900 font-semibold">{{ $courseOffering->course->title_km }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">ឆមាស</label>
                            <p class="text-gray-900">{{ $courseOffering->semester }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">ឆ្នាំសិក្សា</label>
                            <p class="text-gray-900">{{ $courseOffering->academic_year }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">កាលបរិច្ឆេទចាប់ផ្តើម</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($courseOffering->start_date)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">កាលបរិច្ឆេទបញ្ចប់</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($courseOffering->end_date)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">សមត្ថភាព</label>
                            <p class="text-gray-900">{{ $courseOffering->capacity }} សិស្ស</p>
                        </div>
                    </div>
                </div>

                {{-- Lecturer Info --}}
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-tie text-emerald-500"></i> សាស្ត្រាចារ្យ
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">ឈ្មោះ</label>
                            <p class="text-gray-900 font-semibold">{{ $courseOffering->lecturer->name ?? 'មិនទាន់កំណត់' }}</p>
                        </div>
                        @if($courseOffering->lecturer->profile)
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">ឈ្មោះខ្មែរ</label>
                            <p class="text-gray-900">{{ $courseOffering->lecturer->profile->full_name_km ?? '-' }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">អ៊ីមែល</label>
                            <p class="text-gray-900">{{ $courseOffering->lecturer->email ?? '-' }}</p>
                        </div>
                    </div>

                    <h4 class="text-md font-bold text-gray-900 mt-6 mb-3 flex items-center gap-2">
                        <i class="fas fa-calendar text-purple-500"></i> កាលវិភាគ
                    </h4>
                    <div class="space-y-2">
                        @forelse($courseOffering->schedules as $schedule)
                        <div class="flex justify-between items-center text-sm bg-slate-50 p-3 rounded-xl">
                            <span class="font-bold text-slate-800">{{ $schedule->day_of_week }}</span>
                            <span class="text-slate-500 font-medium">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                            </span>
                            <span class="text-emerald-600 font-bold bg-white px-2 py-0.5 rounded-md border border-emerald-100">
                                Rm: {{ $schedule->room->room_number ?? '-' }}
                            </span>
                        </div>
                        @empty
                        <p class="text-gray-400 text-sm italic">មិនទាន់មានកាលវិភាគ</p>
                        @endforelse
                    </div>
                </div>

                {{-- Target Programs --}}
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-graduation-cap text-orange-500"></i> ជំនាញដែលបំណង
                    </h3>
                    <div class="space-y-2">
                        @forelse($courseOffering->targetPrograms as $program)
                        <div class="flex items-center justify-between bg-blue-50 p-3 rounded-xl border border-blue-100">
                            <span class="font-semibold text-blue-800">{{ $program->name_km }}</span>
                            <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded-full font-bold">
                                G{{ $program->pivot->generation }}
                            </span>
                        </div>
                        @empty
                        <p class="text-gray-400 text-sm italic">មិនទាន់មានជំនាញ</p>
                        @endforelse
                    </div>

                    <h4 class="text-md font-bold text-gray-900 mt-6 mb-3 flex items-center gap-2">
                        <i class="fas fa-users text-cyan-500"></i> សិស្សចុះឈ្មោះ
                    </h4>
                    <div class="text-3xl font-extrabold text-gray-900">
                        {{ $courseOffering->studentCourseEnrollments->count() }}
                        <span class="text-sm font-normal text-gray-500">សិស្ស</span>
                    </div>
                </div>
            </div>

            {{-- Student List --}}
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 mt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-list text-indigo-500"></i> បញ្ជីសិស្ស
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">ឈ្មោះ</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">អ៊ីមែល</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">ថ្ងៃចុះឈ្មោះ</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">ស្ថានភាព</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($courseOffering->studentCourseEnrollments as $index => $enrollment)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $enrollment->student->name ?? '-' }}</div>
                                    @if($enrollment->student->profile)
                                    <div class="text-xs text-gray-500">{{ $enrollment->student->profile->full_name_km ?? '' }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $enrollment->student->email ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($enrollment->enrollment_date)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full {{ $enrollment->status === 'enrolled' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $enrollment->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">មិនមានសិស្សចុះឈ្មោះ</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
