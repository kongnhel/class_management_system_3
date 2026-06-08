<x-app-layout>
    <div class="min-h-screen bg-gray-100 font-sans text-gray-900">
        <div class="bg-slate-900 text-white pb-32 pt-12 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</h2>
                        <p class="text-slate-400 mt-2 max-w-2xl text-sm leading-relaxed">ពិន្ទុសិស្សក្នុងមុខវិជ្ជា {{ $courseOffering->semester }} / {{ $courseOffering->academic_year }}</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.grades.export', $courseOffering->id) }}" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 rounded-lg font-bold shadow-lg transition-all">
                            <i class="fas fa-download"></i> នាំចេញ CSV
                        </a>
                        <a href="{{ route('admin.grades.index') }}" class="flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white px-4 py-2.5 rounded-lg font-bold shadow-lg transition-all">
                            <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-10">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-gray-900">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">សិស្សសរុប</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-blue-600">{{ $stats['graded'] }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">មានពិន្ទុ</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-emerald-600">{{ number_format($stats['avg_grade'] ?? 0, 1) }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">មធ្យមភាគ</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-emerald-600">{{ number_format($stats['max_grade'] ?? 0, 1) }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">ខ្ពស់បំផុត</div>
                </div>
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4">
                    <div class="text-2xl font-extrabold text-rose-600">{{ number_format($stats['min_grade'] ?? 0, 1) }}</div>
                    <div class="text-xs text-gray-500 font-bold uppercase">ទាបបំផុត</div>
                </div>
            </div>

            {{-- Grade Table --}}
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-bold text-gray-900">បញ្ជីពិន្ទុសិស្ស</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">ឈ្មោះ</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">លេខសិស្ស</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-600 uppercase">ពិន្ទុ</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-600 uppercase">ស្ថានភាព</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($enrollments as $index => $enrollment)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $enrollment->student->name ?? '-' }}</div>
                                @if($enrollment->student->profile)
                                <div class="text-xs text-gray-500">{{ $enrollment->student->profile->full_name_km ?? '' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $enrollment->student->student_id_code ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($enrollment->final_grade !== null)
                                <span class="text-lg font-bold {{ $enrollment->final_grade >= 50 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ number_format($enrollment->final_grade, 1) }}
                                </span>
                                @else
                                <span class="text-gray-400 italic">មិនទាន់ដាក់ពិន្ទុ</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($enrollment->final_grade !== null)
                                    @if($enrollment->final_grade >= 50)
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">ជាប់</span>
                                    @else
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-700">មិនជាប់</span>
                                    @endif
                                @else
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">រង់ចាំ</span>
                                @endif
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
</x-app-layout>
