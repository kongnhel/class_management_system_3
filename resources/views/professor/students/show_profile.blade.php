<x-app-layout>
    <x-slot name="header">
        <div class="px-4 sm:px-6">
            <h2 class="font-medium text-lg text-gray-900 leading-tight">
                {{ __('ប្រវត្តិរូបនិស្សិត') }}
            </h2>
            <p class="mt-0.5 text-sm text-gray-400">{{ __('ព័ត៌មានលម្អិត និងទំនាក់ទំនងរបស់និស្សិត') }}</p>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 space-y-3">

            {{-- Hero Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-4 px-5 py-5 border-b border-gray-100">
                    @php
                        $pic = $student->studentProfile->profile_picture_url ?? null;
                        $av = $student->avatar ?? null;
                        $hasPic = (!empty($pic) && $pic !== 'null') || (!empty($av) && $av !== 'null');
                        $profilePic = $hasPic ? (($pic && $pic !== 'null') ? $pic : $av) : null;
                    @endphp
                    @if($profilePic)
                        <img src="{{ $profilePic }}" alt="{{ $student->name }}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                             class="w-16 h-16 rounded-full object-cover border-2 border-green-300 flex-shrink-0">
                        <div class="w-16 h-16 rounded-full bg-green-50 border-2 border-green-200 items-center justify-center text-green-700 text-2xl font-medium flex-shrink-0 hidden">
                            {{ Str::upper(Str::substr($student->studentProfile->full_name_km ?? $student->name, 0, 1)) }}
                        </div>
                    @else
                        <div class="w-16 h-16 rounded-full bg-green-50 border-2 border-green-200 flex items-center justify-center text-green-700 text-2xl font-medium flex-shrink-0">
                            {{ Str::upper(Str::substr($student->studentProfile->full_name_km ?? $student->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h3 class="text-base font-medium text-gray-900 truncate">
                            {{ $student->studentProfile->full_name_km ?? $student->name }}
                        </h3>
                        <p class="text-sm text-gray-400 truncate mt-0.5">{{ $student->email }}</p>
                        <span class="inline-flex items-center gap-1.5 mt-2 text-xs font-medium text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/></svg>
                            {{ $student->student_id_code ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Info Card --}}
            @if($student->studentProfile)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <p class="px-5 pt-4 pb-2 text-xs font-medium text-gray-400 tracking-widest uppercase">{{ __('ព័ត៌មានផ្ទាល់ខ្លួន') }}</p>

                @php
                $fields = [
                    ['icon' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0', 'label' => __('ឈ្មោះពេញ (ខ្មែរ)'), 'value' => $student->studentProfile->full_name_km ?? 'N/A'],
                    ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => __('ឈ្មោះពេញ (អង់គ្លេស)'), 'value' => $student->studentProfile->full_name_en ?? 'N/A'],
                    ['icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'label' => __('ភេទ'), 'value' => $student->studentProfile->gender ?? 'N/A'],
                    ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => __('ថ្ងៃខែឆ្នាំកំណើត'), 'value' => $student->studentProfile->date_of_birth ? \Carbon\Carbon::parse($student->studentProfile->date_of_birth)->format('d-M-Y') : 'N/A'],
                    ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => __('លេខទូរស័ព្ទ'), 'value' => $student->studentProfile->phone_number ?? 'N/A'],
                    ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => __('អាសយដ្ឋាន'), 'value' => $student->studentProfile->address ?? 'N/A'],
                ];
                @endphp

                @foreach($fields as $i => $field)
                <div class="flex items-start gap-3 px-5 py-3.5 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $field['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ $field['label'] }}</p>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $field['value'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Program Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <p class="px-5 pt-4 pb-2 text-xs font-medium text-gray-400 tracking-widest uppercase">{{ __('កម្មវិធីសិក្សា') }}</p>
                <div class="flex items-center gap-3 px-5 py-3.5">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ __('ឯកទេសសិក្សា') }}</p>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $student->program->name_km ?? $student->program->name_en ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            @else
            <div class="bg-white rounded-2xl border border-gray-100 px-5 py-10 text-center">
                <p class="text-sm text-gray-400">{{ __('មិនទាន់មានព័ត៌មាន Profile ទេ។') }}</p>
            </div>
            @endif

            {{-- Back Button --}}
            {{-- <a href="{{ route('professor.course-offerings.students.index', ['courseOffering' => $courseOffering->id]) }}"
               class="flex items-center justify-center gap-2 w-full py-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('ត្រឡប់ទៅបញ្ជីនិស្សិត') }}
            </a> --}}

        </div>
    </div>
</x-app-layout>