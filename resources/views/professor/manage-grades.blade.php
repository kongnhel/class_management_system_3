<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-star text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">គ្រប់គ្រងពិន្ទុ</h1>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en ?? 'N/A' }} · {{ $courseOffering->academic_year }} · {{ $courseOffering->semester }}</p>
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
            @if($errors->any() && !$errors->has('grades'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-medium">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <span>{{ $error }}</span>{{ $loop->last ? '' : ', ' }}
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Course Info --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-info-circle text-emerald-500"></i>
                    <h3 class="font-bold text-gray-800 text-sm">ព័ត៌មានវគ្គសិក្សា</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400 text-xs font-bold uppercase">គ្រូបង្រៀន</span>
                        <p class="font-semibold text-gray-800">{{ $courseOffering->lecturer?->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs font-bold uppercase">ចំនួនសិស្ស</span>
                        <p class="font-semibold text-gray-800">{{ $students->count() }} នាក់</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs font-bold uppercase">កិច្ចការ</span>
                        <p class="font-semibold text-gray-800">{{ $assignments->count() }} ប្រភេទ</p>
                    </div>
                </div>
            </div>

            {{-- Grades Form --}}
            <form method="POST" action="{{ route('grades.storeOrUpdate', $courseOffering->id) }}">
                @csrf

                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fas fa-edit text-emerald-500"></i> បញ្ចូលពិន្ទុ
                        </h3>
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl font-bold text-xs shadow-md transition-all active:scale-95">
                            <i class="fas fa-save"></i> រក្សាទុកពិន្ទុទាំងអស់
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase w-10">#</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">ឈ្មោះ</th>
                                    @foreach($assignments as $assignment)
                                        <th class="px-3 py-3 text-center text-[10px] font-bold text-gray-500 uppercase min-w-[100px]">
                                            <div>{{ $assignment->title_km ?? $assignment->title_en ?? 'N/A' }}</div>
                                            <div class="text-gray-400 font-normal normal-case">/ {{ $assignment->max_score ?? $assignment->max_points ?? 100 }}</div>
                                        </th>
                                    @endforeach
                                    <th class="px-4 py-3 text-center text-[10px] font-bold text-emerald-600 uppercase bg-emerald-50">សរុប</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($students as $student)
                                    @php $totalWeightedScore = 0; @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-xs font-bold text-gray-400">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                @php
                                                    $profilePic = $student->studentProfile?->profile_picture_url ?? $student->profile?->profile_picture_url ?? null;
                                                @endphp
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center text-white font-bold text-[10px] shadow-sm shrink-0">
                                                    @if($profilePic)
                                                        <img src="{{ $profilePic }}" class="w-full h-full rounded-full object-cover" alt="">
                                                    @else
                                                        {{ mb_substr($student->studentProfile?->full_name_km ?? $student->name ?? '?', 0, 1) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="text-sm font-semibold text-gray-800">{{ $student->studentProfile?->full_name_km ?? $student->name ?? 'N/A' }}</span>
                                                    <span class="text-[10px] text-gray-400 block">{{ $student->student_id_code ?? '' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($assignments as $assignment)
                                            @php
                                                $grade = $student->grades->where('assignment_id', $assignment->id)->first();
                                                $scoreReceived = $grade ? $grade->score_received : '';
                                                $errorKey = 'grades.' . $student->id . '.' . $assignment->id;
                                                $maxScore = $assignment->max_score ?? $assignment->max_points ?? 100;
                                                if($scoreReceived !== '' && $maxScore > 0) {
                                                    $normalizedScore = ($scoreReceived / $maxScore) * 100;
                                                    $totalWeightedScore += $normalizedScore * ($assignment->component->weight_percentage ?? 0) / 100;
                                                }
                                            @endphp
                                            <td class="px-2 py-3 text-center">
                                                <input type="number" name="grades[{{ $student->id }}][{{ $assignment->id }}]"
                                                    class="w-20 px-2 py-1.5 border rounded-lg text-center text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all
                                                    {{ $errors->has($errorKey) ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 focus:bg-white' }}"
                                                    value="{{ old($errorKey, $scoreReceived) }}"
                                                    min="0" max="{{ $maxScore }}" step="0.01" placeholder="0">
                                                @error($errorKey)
                                                    <p class="text-red-500 text-[9px] mt-0.5">{{ $message }}</p>
                                                @enderror
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-3 text-center bg-emerald-50">
                                            <span class="text-sm font-black {{ $totalWeightedScore >= 50 ? 'text-emerald-600' : 'text-red-500' }}">
                                                {{ number_format($totalWeightedScore, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + $assignments->count() }}" class="px-6 py-16 text-center">
                                            <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                                            <p class="text-sm font-bold text-gray-400">មិនមានទិន្នន័យពិន្ទុ</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-3 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl font-bold text-sm shadow-md transition-all active:scale-95">
                            <i class="fas fa-save"></i> រក្សាទុកពិន្ទុទាំងអស់
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
