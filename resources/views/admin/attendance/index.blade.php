<x-app-layout>
    <div class="min-h-screen bg-gray-100 font-sans text-gray-900">
        <div class="bg-slate-900 text-white pb-32 pt-12 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold tracking-tight text-white">ទិន្នន័យវត្តមាន</h2>
                <p class="text-slate-400 mt-2 max-w-2xl text-sm leading-relaxed">តាមដានវត្តមានសិស្សក្នុងមុខវិជ្ជាផ្សេងៗ</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-10">
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-5 mb-8">
                <form action="{{ route('admin.attendance.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end">
                    <div class="md:col-span-4">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5 block">ស្វែងរកមុខវិជ្ជា/សាស្ត្រាចារ្យ</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ស្វែងរក..." class="block w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white sm:text-sm py-2.5 shadow-sm">
                    </div>
                    <div class="md:col-span-3">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5 block">កម្មវិធីសិក្សា</label>
                        <select name="program_id" class="block w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white sm:text-sm py-2.5">
                            <option value="">បង្ហាញទាំងអស់</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name_km }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5 block">ឆមាស</label>
                        <select name="semester" class="block w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white sm:text-sm py-2.5">
                            <option value="">ទាំងអស់</option>
                            <option value="ឆមាសទី១" {{ request('semester') == 'ឆមាសទី១' ? 'selected' : '' }}>ឆមាសទី១</option>
                            <option value="ឆមាសទី២" {{ request('semester') == 'ឆមាសទី២' ? 'selected' : '' }}>ឆមាសទី២</option>
                        </select>
                    </div>
                    <div class="md:col-span-3 flex justify-end gap-2">
                        <a href="{{ route('admin.attendance.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition-colors font-bold text-sm">
                            <i class="fas fa-sync-alt mr-1"></i> Reset
                        </a>
                        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-6 py-2.5 rounded-lg transition-colors shadow-md text-sm">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase">មុខវិជ្ជា</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase">សាស្ត្រាចារ្យ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase">ឆមាស / ឆ្នាំ</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase">សិស្ស</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($courseOfferings as $offering)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $offering->course->title_km ?? $offering->course->title_en }}</div>
                                <div class="text-xs text-gray-500">{{ $offering->course->title_en }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium">{{ $offering->lecturer->name ?? 'Unassigned' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm">{{ $offering->semester }} / {{ $offering->academic_year }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold">{{ $offering->student_course_enrollments_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.attendance.show', $offering->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-colors" title="មើលវត្តមាន">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">មិនមានទិន្នន័យ</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $courseOfferings->links() }}</div>
        </div>
    </div>
</x-app-layout>
