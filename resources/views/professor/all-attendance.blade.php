<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

            {{-- Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">វត្តមានទាំងអស់</h1>
                    <p class="text-sm text-gray-500 mt-0.5">ពិនិត្យ និងកត់ត្រាវត្តមានសម្រាប់មុខវិជ្ជារបស់អ្នក</p>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium">
                    <i class="fas fa-check-circle text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-medium">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Add Form --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
                <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-emerald-500"></i> បន្ថែមកំណត់ត្រាថ្មី
                </h3>
                <form action="{{ route('professor.attendances.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">វគ្គសិក្សា</label>
                            <select name="course_offering_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">ជ្រើសរើសវគ្គសិក្សា</option>
                                @foreach($professorCourseOfferings as $offering)
                                    <option value="{{ $offering->id }}">{{ $offering->course?->title_km ?? $offering->course?->title_en ?? 'N/A' }} ({{ $offering->academic_year }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">និស្សិត</label>
                            <select name="student_user_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="">ជ្រើសរើសនិស្សិត</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->profile?->full_name_km ?? $student->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">កាលបរិច្ឆេទ</label>
                            <input type="date" name="date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">ស្ថានភាព</label>
                            <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                                <option value="present">មានវត្តមាន</option>
                                <option value="absent">អវត្តមាន</option>
                                <option value="permission">មានច្បាប់</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl font-bold text-sm shadow-md transition-all active:scale-95">
                            <i class="fas fa-save"></i> រក្សាទុក
                        </button>
                    </div>
                </form>
            </div>

            {{-- Records Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm">កំណត់ត្រាវត្តមាន</h3>
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-bold text-gray-500">{{ $attendances->total() }} កំណត់ត្រា</span>
                </div>

                @if($attendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">និស្សិត</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">មុខវិជ្ជា</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">កាលបរិច្ឆេទ</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">ស្ថានភាព</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">កំណត់ចំណាំ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($attendances as $record)
                                @php
                                    $colors = ['present' => 'green', 'absent' => 'red', 'late' => 'yellow', 'permission' => 'blue'];
                                    $color = $colors[$record->status] ?? 'gray';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            @php $profilePic = $record->student?->studentProfile?->profile_picture_url ?? $record->student?->profile?->profile_picture_url ?? null; @endphp
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center text-white font-bold text-[10px] shadow-sm shrink-0">
                                                @if($profilePic)
                                                    <img src="{{ $profilePic }}" class="w-full h-full rounded-full object-cover" alt="">
                                                @else
                                                    {{ mb_substr($record->student?->studentProfile?->full_name_km ?? $record->student?->name ?? '?', 0, 1) }}
                                                @endif
                                            </div>
                                            <span class="text-sm font-semibold text-gray-800">{{ $record->student?->studentProfile?->full_name_km ?? $record->student?->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $record->courseOffering?->course?->title_km ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-400">{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100">
                                            {{ $record->status_km }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-400">{{ $record->remarks ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                                        <p class="text-sm font-bold text-gray-400">មិនមានកំណត់ត្រា</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $attendances->links('pagination::tailwind') }}
                </div>
                @else
                <div class="px-6 py-16 text-center">
                    <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                    <p class="text-sm font-bold text-gray-400">មិនមានកំណត់ត្រា</p>
                    <p class="text-xs text-gray-300 mt-1">សូមប្រើទម្រង់ខាងលើដើម្បីបន្ថែម</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
