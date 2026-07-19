<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

            {{-- Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-star text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">ពិន្ទុទាំងអស់</h1>
                    <p class="text-sm text-gray-500 mt-0.5">បញ្ជីពិន្ទុដែលអ្នកគ្រប់គ្រង</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <div class="text-xs font-bold text-gray-400 uppercase mb-1">សរុបកត់ត្រា</div>
                    <div class="text-2xl font-black text-gray-800">{{ $grades->total() }}</div>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm">បញ្ជីពិន្ទុ</h3>
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-bold text-gray-500">{{ $grades->total() }} កំណត់ត្រា</span>
                </div>

                @if($grades->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">សិស្ស</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">មុខវិជ្ជា</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">ប្រភេទ</th>
                                <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase">ពិន្ទុ</th>
                                <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase">អតិបរមា</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">កាលបរិច្ឆេទ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($grades as $grade)
                                @php
                                    $percent = $grade->max_score > 0 ? round(($grade->score / $grade->max_score) * 100) : 0;
                                    $typeLabels = ['exam' => 'ប្រឡង', 'assignment' => 'កិច្ចការ', 'quiz' => 'Quiz'];
                                    $typeColors = ['exam' => 'purple', 'assignment' => 'emerald', 'quiz' => 'amber'];
                                    $tColor = $typeColors[$grade->assessment_type] ?? 'gray';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center text-white font-bold text-[10px] shadow-sm shrink-0">
                                                @if($grade->profile_pic)
                                                    <img src="{{ $grade->profile_pic }}" class="w-full h-full rounded-full object-cover" alt="">
                                                @else
                                                    {{ mb_substr($grade->student_name ?? '?', 0, 1) }}
                                                @endif
                                            </div>
                                            <span class="text-sm font-semibold text-gray-800">{{ $grade->student_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $grade->course_title_km }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-{{ $tColor }}-50 text-{{ $tColor }}-700 border border-{{ $tColor }}-100">
                                            {{ $typeLabels[$grade->assessment_type] ?? $grade->assessment_type }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="text-sm font-bold {{ $percent >= 50 ? 'text-emerald-600' : 'text-red-500' }}">
                                            {{ $grade->score }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center text-sm text-gray-500">{{ $grade->max_score }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-400">{{ \Carbon\Carbon::parse($grade->date)->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                                        <p class="text-sm font-bold text-gray-400">មិនមានពិន្ទុ</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $grades->links('pagination::tailwind', ['pageName' => 'gradesPage']) }}
                </div>
                @else
                <div class="px-6 py-16 text-center">
                    <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                    <p class="text-sm font-bold text-gray-400">មិនមានពិន្ទុ</p>
                    <p class="text-xs text-gray-300 mt-1">ពិន្ទុនឹងបង្ហាញនៅទីនេះនៅពេលអ្នកបញ្ចូល</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
