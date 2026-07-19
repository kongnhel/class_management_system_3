<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            
            {{-- Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">ស្រង់វត្តមាន</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $courseOffering->course?->title_km ?? $courseOffering->course?->title_en }}</p>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium">
                    <i class="fas fa-check-circle text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('student.leader.attendance.store', $courseOffering->id) }}" method="POST">
                @csrf
                
                {{-- Date + Submit --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6 flex flex-col sm:flex-row items-end sm:items-center justify-between gap-4">
                    <div class="w-full sm:w-64">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">កាលបរិច្ឆេទ</label>
                        <input type="date" name="attendance_date" value="{{ $today }}" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all">
                    </div>
                    <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl font-bold text-sm shadow-md transition-all active:scale-95">
                        <i class="fas fa-check"></i> បញ្ជូនវត្តមាន
                    </button>
                </div>

                {{-- Student List --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800 text-sm">បញ្ជីឈ្មោះសិស្ស</h3>
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-bold text-gray-500">
                            {{ $students->count() - 1 }} នាក់
                        </span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($students as $student)
                            @if($student->id == $leaderId) @continue @endif
                            @php
                                $profilePic = $student->studentProfile?->profile_picture_url ?? $student->profile?->profile_picture_url ?? $student->avatar ?? null;
                            @endphp
                            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="relative shrink-0">
                                        @if($profilePic)
                                            <img src="{{ $profilePic }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm" alt="">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                                {{ mb_substr($student->studentProfile?->full_name_km ?? $student->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-800">{{ $student->studentProfile?->full_name_km ?? $student->name }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $student->student_id_code ?? '' }}</div>
                                    </div>
                                </div>
                                <div class="flex bg-gray-100 p-1 rounded-xl gap-0.5">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="present" checked class="hidden peer">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold text-gray-500 peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm transition-all whitespace-nowrap">
                                            មក
                                        </span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="permission" class="hidden peer">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold text-gray-500 peer-checked:bg-white peer-checked:text-amber-500 peer-checked:shadow-sm transition-all whitespace-nowrap">
                                            ច្បាប់
                                        </span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="absent" class="hidden peer">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold text-gray-500 peer-checked:bg-white peer-checked:text-rose-500 peer-checked:shadow-sm transition-all whitespace-nowrap">
                                            អវត្តមាន
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
