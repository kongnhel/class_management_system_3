<x-app-layout>
    {{-- Dark Gradient Header --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-white/10 backdrop-blur rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">វត្តមានគ្រូបង្រៀន</h1>
                        <p class="mt-1 text-sm text-slate-400">កំណត់ត្រាវត្តមានគ្រូបង្រៀនសម្រាប់ការគណនាប្រាក់ខែ</p>
                    </div>
                </div>
                <a href="{{ route('admin.attendance.index') }}" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-white/20 transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    ត្រឡប់ក្រោយ
                </a>
            </div>
        </div>
    </div>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Section 1: Filters --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="h-8 w-8 bg-slate-900 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">①</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">តម្រង់ទិស</h3>
                        <p class="text-xs text-gray-400">ស្វែងរក និងច្រោះកំណត់ត្រាវត្តមាន</p>
                    </div>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.attendance.professorCheckins') }}" data-admin-realtime-filter class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            <div class="md:col-span-3">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">ស្វែងរក</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </span>
                                    <input type="text" name="search" value="{{ request('search', '') }}" placeholder="ឈ្មោះគ្រូ..." autocomplete="off"
                                           class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">គ្រូបង្រៀន</label>
                                <select name="professor_id"
                                        class="py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                                    <option value="">ទាំងអស់</option>
                                    @foreach($professors as $p)
                                        <option value="{{ $p->id }}" {{ request('professor_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">ឆមាស</label>
                                <select name="semester"
                                        class="py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                                    <option value="">ទាំងអស់</option>
                                    <option value="ឆមាសទី១" {{ request('semester') == 'ឆមាសទី១' ? 'selected' : '' }}>ឆមាសទី១</option>
                                    <option value="ឆមាសទី២" {{ request('semester') == 'ឆមាសទី២' ? 'selected' : '' }}>ឆមាសទី២</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">ពីថ្ងៃ</label>
                                <input type="date" name="date_from" value="{{ request('date_from', '') }}"
                                       class="py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">ដល់ថ្ងៃ</label>
                                <input type="date" name="date_to" value="{{ request('date_to', '') }}"
                                       class="py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-full transition-all">
                            </div>
                            <div class="md:col-span-1 flex items-end gap-2">
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 w-full bg-emerald-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Results container for realtime filtering --}}
            <div data-admin-results>

            {{-- Section 2: Summary Stats --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="h-8 w-8 bg-slate-900 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">②</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">សង្ខេប</h3>
                        <p class="text-xs text-gray-400">ទិន្នន័យវត្តមានគ្រូបង្រៀន</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="h-9 w-9 bg-blue-50 rounded-lg flex items-center justify-center">
                                    <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <span class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900">សរុប</p>
                            <p class="text-xs text-gray-400 mt-0.5">កំណត់ត្រាទាំងអស់</p>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="h-9 w-9 bg-emerald-50 rounded-lg flex items-center justify-center">
                                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-2xl font-bold text-gray-900">{{ $stats['this_month'] }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900">ខែនេះ</p>
                            <p class="text-xs text-gray-400 mt-0.5">កំណត់ត្រាខែបច្ចុប្បន្ន</p>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="h-9 w-9 bg-amber-50 rounded-lg flex items-center justify-center">
                                    <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-2xl font-bold text-gray-900">{{ $stats['this_week'] }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900">សប្តាហ៍នេះ</p>
                            <p class="text-xs text-gray-400 mt-0.5">កំណត់ត្រាសប្តាហ៍បច្ចុប្បន្ន</p>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="h-9 w-9 bg-violet-50 rounded-lg flex items-center justify-center">
                                    <svg class="h-4 w-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <span class="text-2xl font-bold text-gray-900">{{ $stats['unique_professors'] }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900">គ្រូបង្រៀន</p>
                            <p class="text-xs text-gray-400 mt-0.5">គ្រូដែលមានវត្តមានខែនេះ</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Records Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-slate-900 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold">③</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">កំណត់ត្រាវត្តមាន</h3>
                            <p class="text-xs text-gray-400">បញ្ជីវត្តមានគ្រូបង្រៀនទាំងអស់</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">{{ $checkins->total() }} កំណត់ត្រា</span>
                </div>

                @if($checkins->count() > 0)
                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr class="border-b border-gray-200">
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ល.រ</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">គ្រូបង្រៀន</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">មុខវិជ្ជា</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ឆមាស</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ឆ្នាំសិក្សា</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ថ្ងៃ</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ម៉ោងវត្តមាន</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-500 uppercase">ស្ថានភាព</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($checkins as $index => $checkin)
                                    @php
                                        $verifiedAt = \Carbon\Carbon::parse($checkin->verified_at);
                                        $courseName = $checkin->courseOffering?->course?->title_km ?? $checkin->courseOffering?->course?->title_en ?? 'N/A';
                                        $generation = $checkin->courseOffering?->targetPrograms?->first()?->pivot?->generation ?? $checkin->courseOffering?->generation ?? '-';
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3.5 text-sm text-gray-400">{{ ($checkins->currentPage() - 1) * $checkins->perPage() + $index + 1 }}</td>
                                        <td class="px-6 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-semibold text-xs">
                                                    {{ mb_substr($checkin->professor?->name ?? '?', 0, 1, 'UTF-8') }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $checkin->professor?->name ?? 'N/A' }}</p>
                                                    <p class="text-xs text-gray-400">{{ $checkin->professor?->email ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $courseName }}</td>
                                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $checkin->courseOffering?->semester ?? '-' }}</td>
                                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $checkin->courseOffering?->academic_year ?? '-' }}</td>
                                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $verifiedAt->format('d M Y') }}</td>
                                        <td class="px-6 py-3.5 text-sm text-gray-700 font-medium">{{ $verifiedAt->format('H:i') }}</td>
                                        <td class="px-6 py-3.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-600">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                វត្តមាន
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden divide-y divide-gray-100">
                        @foreach($checkins as $checkin)
                            @php
                                $verifiedAt = \Carbon\Carbon::parse($checkin->verified_at);
                                $courseName = $checkin->courseOffering?->course?->title_km ?? $checkin->courseOffering?->course?->title_en ?? 'N/A';
                            @endphp
                            <div class="px-4 py-3 hover:bg-gray-50 transition">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-semibold text-xs">
                                            {{ mb_substr($checkin->professor?->name ?? '?', 0, 1, 'UTF-8') }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $checkin->professor?->name ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-400">{{ $courseName }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        វត្តមាន
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-xs text-gray-400 ml-11">
                                    <span>{{ $verifiedAt->format('d M Y') }}</span>
                                    <span>{{ $verifiedAt->format('H:i') }}</span>
                                    <span>{{ $checkin->courseOffering?->semester ?? '-' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $checkins->links() }}
                    </div>
                @else
                    <div class="py-16 text-center">
                        <div class="h-16 w-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">មិនមានកំណត់ត្រា</h3>
                        <p class="text-sm text-gray-400 mt-1">មិនមានកំណត់ត្រាវត្តមានគ្រូបង្រៀនទេ។</p>
                    </div>
                @endif
            </div>

            </div><!-- /data-admin-results -->

        </div>
    </div>
</x-app-layout>
