<x-app-layout>
    <div class="bg-gray-50 min-h-screen" x-data="{
            open: false,
            attendanceId: '',
            studentUserId: '',
            date: '',
            status: '',
            remarks: '',
            updateRoute: '{{ route('professor.attendances.update', 0) }}',
            showDelete: false,
            deleteId: null,
            deleteStudentName: ''
        }"
        @open-edit-modal.window="
            open = true;
            attendanceId = $event.detail.id;
            studentUserId = $event.detail.studentUserId;
            date = $event.detail.date;
            status = $event.detail.status;
            remarks = $event.detail.remarks;
        ">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-clipboard-check text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">គ្រប់គ្រងវត្តមាន</h1>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $courseOffering->course?->title_km ?? $courseOffering->course?->title_en ?? 'N/A' }} · {{ $courseOffering->academic_year }} · {{ $courseOffering->semester }}</p>
                    </div>
                </div>
                <a href="{{ route('professor.my-course-offerings') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 shadow-sm transition-all">
                    <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
                </a>
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

            {{-- Add Record Form --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
                <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-emerald-500"></i> បន្ថែមកំណត់ត្រាថ្មី
                </h3>
                <form action="{{ route('professor.attendances.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    @csrf
                    <input type="hidden" name="course_offering_id" value="{{ $courseOffering->id }}">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">និស្សិត</label>
                        <select name="student_user_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                            <option value="">ជ្រើសរើសនិស្សិត</option>
                            @foreach($courseOffering->studentCourseEnrollments->unique('student_user_id') as $enrollment)
                                @if($enrollment->student)
                                <option value="{{ $enrollment->student->id }}">{{ $enrollment->student->studentProfile?->full_name_km ?? $enrollment->student->name }}</option>
                                @endif
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
                </form>
            </div>

            {{-- Records Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm">កំណត់ត្រាវត្តមាន</h3>
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-bold text-gray-500">{{ $attendanceRecords->count() }} កំណត់ត្រា</span>
                </div>

                @if($attendanceRecords->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase">និស្សិត</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase">កាលបរិច្ឆេទ</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase">ស្ថានភាព</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase">សកម្មភាព</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($attendanceRecords as $record)
                            @php $profilePic = $record->student?->studentProfile?->profile_picture_url ?? $record->student?->profile?->profile_picture_url ?? null; @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
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
                                <td class="px-5 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $colors = ['present' => 'green', 'absent' => 'red', 'late' => 'yellow', 'permission' => 'blue'];
                                        $labels = ['present' => 'មានវត្តមាន', 'absent' => 'អវត្តមាន', 'late' => 'មកយឺត', 'permission' => 'មានច្បាប់'];
                                        $color = $colors[$record->status] ?? 'gray';
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100">
                                        {{ $labels[$record->status] ?? $record->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button"
                                            class="edit-attendance text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 px-3 py-1.5 rounded-lg transition-colors"
                                            data-id="{{ $record->id }}"
                                            data-student-user-id="{{ $record->student_user_id }}"
                                            data-date="{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}"
                                            data-status="{{ $record->status }}"
                                            data-remarks="{{ $record->remarks ?? '' }}">
                                            <i class="fas fa-pen mr-1"></i> កែ
                                        </button>
                                        <form action="{{ route('professor.attendances.destroy', $record->id) }}" method="POST" id="delete-form-{{ $record->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button" @click="deleteId = {{ $record->id }}; deleteStudentName = '{{ $record->student?->studentProfile?->full_name_km ?? $record->student?->name ?? '' }}'; showDelete = true" class="text-xs font-bold text-red-500 hover:text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                                                <i class="fas fa-trash mr-1"></i> លុប
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $attendanceRecords->links() }}
                </div>
                @else
                <div class="px-5 py-16 text-center">
                    <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                    <p class="text-sm font-bold text-gray-400">មិនមានកំណត់ត្រា</p>
                    <p class="text-xs text-gray-300 mt-1">សូមប្រើទម្រង់ខាងលើដើម្បីបន្ថែម</p>
                </div>
                @endif
            </div>

            {{-- Modals --}}
                <div x-show="open" x-cloak style="display: none;"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">កែសម្រួលកំណត់ត្រា</h3>
                    <form :action="updateRoute.replace('0', attendanceId)" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="course_offering_id" value="{{ $courseOffering->id }}">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">និស្សិត</label>
                                <select name="student_user_id" x-model="studentUserId" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500">
                                    @foreach($courseOffering->studentCourseEnrollments->unique('student_user_id') as $enrollment)
                                        @if($enrollment->student)
                                        <option value="{{ $enrollment->student->id }}">{{ $enrollment->student->studentProfile?->full_name_km ?? $enrollment->student->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">កាលបរិច្ឆេទ</label>
                                <input type="date" name="date" x-model="date" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">ស្ថានភាព</label>
                                <select name="status" x-model="status" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500">
                                    <option value="present">មានវត្តមាន</option>
                                    <option value="absent">អវត្តមាន</option>
                                    <option value="permission">មានច្បាប់</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="open = false" class="px-4 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-bold text-sm transition-colors">បោះបង់</button>
                            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-md transition-all">រក្សាទុក</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Delete Confirm Modal --}}
            <div x-show="showDelete" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDelete = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-trash-alt text-red-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">តើអ្នកពិតជាចង់លុបមែនទេ?</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        កំណត់ត្រាវត្តមានរបស់ <span class="font-semibold text-gray-700" x-text="deleteStudentName"></span> នឹងត្រូវបានលុបចេញ។
                    </p>
                    <div class="flex gap-3">
                        <button @click="showDelete = false" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-sm transition-colors">បោះបង់</button>
                        <button @click="document.getElementById('delete-form-' + deleteId)?.submit()" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm shadow-md transition-all">លុប</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.edit-attendance').forEach(btn => {
            btn.addEventListener('click', () => {
                const d = btn.dataset;
                window.dispatchEvent(new CustomEvent('open-edit-modal', {
                    detail: { id: d.id, studentUserId: d.studentUserId, date: d.date, status: d.status, remarks: d.remarks || '' }
                }));
            });
        });
    </script>
</x-app-layout>
