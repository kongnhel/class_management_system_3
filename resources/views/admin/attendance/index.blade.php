<x-app-layout>
    <div class="bg-gray-50 min-h-screen font-sans text-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-calendar-check text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">ទិន្នន័យវត្តមាន</h1>
                    <p class="text-gray-500 mt-0.5">តាមដានវត្តមានសិស្សក្នុងមុខវិជ្ជាផ្សេងៗ</p>
                </div>
            </div>

            {{-- Filter Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
                <form action="{{ route('admin.attendance.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-4">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">ស្វែងរកមុខវិជ្ជា/សាស្ត្រាចារ្យ</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-search text-gray-400"></i>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="វាយបញ្ចូលឈ្មោះមុខវិជ្ជា ឬសាស្ត្រាចារ្យ..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">កម្មវិធីសិក្សា</label>
                            <select name="program_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">បង្ហាញទាំងអស់</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name_km }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">ឆមាស</label>
                            <select name="semester" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">ទាំងអស់</option>
                                <option value="ឆមាសទី១" {{ request('semester') == 'ឆមាសទី១' ? 'selected' : '' }}>ឆមាសទី១</option>
                                <option value="ឆមាសទី២" {{ request('semester') == 'ឆមាសទី២' ? 'selected' : '' }}>ឆមាសទី២</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 block">ឆ្នាំសិក្សា</label>
                            <input type="text" name="academic_year" value="{{ request('academic_year') }}" placeholder="ឧ. 2024-2025"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                        </div>
                        <div class="md:col-span-1 flex justify-end gap-2">
                            <a href="{{ route('admin.attendance.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors font-bold text-sm">
                                <i class="fas fa-sync-alt mr-1"></i> កំណត់ឡើងវិញ
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold px-6 py-2.5 rounded-xl transition-all shadow-md text-sm">
                                <i class="fas fa-filter mr-1"></i> តម្រង់ទិស
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Results Count --}}
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">រកឃើញ <span class="font-bold text-gray-700">{{ $courseOfferings->total() }}</span> មុខវិជ្ជា</p>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">មុខវិជ្ជា</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">សាស្ត្រាចារ្យ</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ឆមាស / ឆ្នាំសិក្សា</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">សិស្ស</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">សកម្មភាព</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($courseOfferings as $offering)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-book text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $offering->course->title_km ?? $offering->course->title_en }}</div>
                                            <div class="text-xs text-gray-400">{{ $offering->course->title_en }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">{{ $offering->lecturer->name ?? 'មិនទាន់កំណត់' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold">{{ $offering->semester }}</span>
                                        <span class="text-sm text-gray-500">{{ $offering->academic_year }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg bg-gray-100 text-gray-700 font-bold text-sm">
                                        {{ $offering->student_course_enrollments_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.attendance.show', $offering->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl transition-colors text-sm font-bold">
                                        <i class="fas fa-eye"></i> មើល
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-bold text-gray-400">មិនមានទិន្នន័យ</p>
                                        <p class="text-xs text-gray-300">សូមព្យាយាមស្វែងរកឡើងវិញ</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $courseOfferings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
