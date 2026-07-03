<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        @php
            $today = now()->startOfDay();
            $isActive = $today->between($courseOffering->start_date, $courseOffering->end_date);
            $enrollmentCount = $courseOffering->studentCourseEnrollments->count();
        @endphp
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white pb-24 pt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.manage-course-offerings') }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                            <i class="fas fa-arrow-left text-white"></i>
                        </a>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                                <i class="fas fa-book-open text-emerald-300 text-xl"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h2 class="text-3xl font-bold tracking-tight">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</h2>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold {{ $isActive ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                                        {{ $isActive ? 'សកម្ម' : 'ផុតកំណត់' }}
                                    </span>
                                </div>
                                <p class="text-slate-400 text-sm">{{ $courseOffering->semester }} / {{ $courseOffering->academic_year }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.edit-course-offering', $courseOffering->id) }}" class="flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg transition-all">
                            <i class="fas fa-edit"></i> <span>{{ __('កែប្រែ') }}</span>
                        </a>
                        <a href="{{ route('admin.manage-course-offerings') }}" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all">
                            <i class="fas fa-arrow-left"></i> <span>{{ __('ត្រឡប់ក្រោយ') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-12 relative z-10">
            {{-- Stats Row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-users text-emerald-500"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $enrollmentCount }}</p>
                            <p class="text-xs text-gray-500">{{ __('សិស្សចុះឈ្មោះ') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fas fa-user-tie text-emerald-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 truncate max-w-[120px]">{{ $courseOffering->lecturer->name ?? 'មិនទាន់កំណត់' }}</p>
                            <p class="text-xs text-gray-500">{{ __('សាស្ត្រាចារ្យ') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                            <i class="fas fa-calendar text-amber-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $courseOffering->semester }}</p>
                            <p class="text-xs text-gray-500">{{ $courseOffering->academic_year }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                            <i class="fas fa-chair text-purple-500"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $courseOffering->capacity }}</p>
                            <p class="text-xs text-gray-500">{{ __('សមត្ថភាព') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Course Info + Lecturer --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Course Info --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <i class="fas fa-book text-emerald-500 text-sm"></i>
                            </div>
                            {{ __('ព័ត៌មានមុខវិជ្ជា') }}
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('ឈ្មោះមុខវិជ្ជា') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->course->title_km }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('ឆមាស') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->semester }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('ឆ្នាំសិក្សា') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->academic_year }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('សមត្ថភាព') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->capacity }} សិស្ស</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('កាលបរិច្ឆេទចាប់ផ្តើម') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ \Carbon\Carbon::parse($courseOffering->start_date)->format('d/m/Y') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('កាលបរិច្ឆេទបញ្ចប់') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ \Carbon\Carbon::parse($courseOffering->end_date)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Student List --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                                    <i class="fas fa-list text-emerald-500 text-sm"></i>
                                </div>
                                {{ __('បញ្ជីសិស្ស') }}
                            </h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700">{{ $enrollmentCount }} នាក់</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('ឈ្មោះ') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('អ៊ីមែល') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('ថ្ងៃចុះឈ្មោះ') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('ស្ថានភាព') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse($courseOffering->studentCourseEnrollments as $index => $enrollment)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-gray-900 text-sm">{{ $enrollment->student->name ?? '-' }}</div>
                                            @if($enrollment->student->profile)
                                            <div class="text-xs text-gray-500">{{ $enrollment->student->profile->full_name_km ?? '' }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $enrollment->student->email ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($enrollment->enrollment_date)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold {{ $enrollment->status === 'enrolled' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                                {{ $enrollment->status === 'enrolled' ? 'ចុះឈ្មោះ' : $enrollment->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center">
                                            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-user-slash text-gray-300 text-2xl"></i>
                                            </div>
                                            <p class="text-gray-400 text-sm">{{ __('មិនមានសិស្សចុះឈ្មោះ') }}</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Lecturer + Programs + Schedules --}}
                <div class="space-y-6">
                    {{-- Lecturer --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <i class="fas fa-user-tie text-emerald-500 text-sm"></i>
                            </div>
                            {{ __('សាស្ត្រាចារ្យ') }}
                        </h3>
                        <div class="space-y-3">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('ឈ្មោះ') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->lecturer->name ?? 'មិនទាន់កំណត់' }}</p>
                            </div>
                            @if($courseOffering->lecturer->profile)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('ឈ្មោះខ្មែរ') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->lecturer->profile->full_name_km ?? '-' }}</p>
                            </div>
                            @endif
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('អ៊ីមែល') }}</label>
                                <p class="text-gray-900 font-semibold mt-1">{{ $courseOffering->lecturer->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Target Programs --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-amber-500 text-sm"></i>
                            </div>
                            {{ __('ជំនាញដែលគោលដៅ') }}
                        </h3>
                        <div class="space-y-2">
                            @forelse($courseOffering->targetPrograms as $program)
                            <div class="flex items-center justify-between bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                                <span class="font-semibold text-emerald-800 text-sm">{{ $program->name_km }}</span>
                                <span class="text-xs bg-emerald-200 text-emerald-800 px-2.5 py-0.5 rounded-lg font-bold">G{{ $program->pivot->generation }}</span>
                            </div>
                            @empty
                            <p class="text-gray-400 text-sm italic text-center py-4">{{ __('មិនទាន់មានជំនាញ') }}</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Schedules --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-purple-500 text-sm"></i>
                            </div>
                            {{ __('កាលវិភាគសិក្សា') }}
                        </h3>
                        <div class="space-y-2">
                            @forelse($courseOffering->schedules as $schedule)
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-700">{{ substr($schedule->day_of_week, 0, 3) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $schedule->day_of_week }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                    {{ $schedule->room->room_number ?? '-' }}
                                </span>
                            </div>
                            @empty
                            <p class="text-gray-400 text-sm italic text-center py-4">{{ __('មិនទាន់មានកាលវិភាគ') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
