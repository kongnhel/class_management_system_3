<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:p-8 mb-6">
                <div class="flex items-center gap-4">
                    <span class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">{{ __('មុខវិជ្ជាដែលអាចចុះឈ្មោះបាន') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('ជ្រើសរើសមុខវិជ្ជាដែលអ្នកចង់ចុះឈ្មោះចូលរៀន') }}</p>
                    </div>
                </div>
            </div>

            {{-- Course Cards --}}
            @if ($availableCourses->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="text-gray-500 font-bold text-lg">{{ __('បច្ចុប្បន្ននេះ គ្មានមុខវិជ្ជាដែលអាចចុះឈ្មោះបានទេ។') }}</p>
                    <p class="text-gray-400 text-sm mt-2">{{ __('សូមពិនិត្យមើលនៅពេលក្រោយ ឬទាក់ទងរដ្ឋបាល។') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($availableCourses as $courseOffering)
                        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                            <div class="mb-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-lg">
                                        {{ $courseOffering->semester }}
                                    </span>
                                    <span class="bg-gray-100 text-gray-500 text-[10px] font-bold px-2.5 py-1 rounded-lg">
                                        {{ $courseOffering->academic_year }}
                                    </span>
                                </div>
                                <h6 class="font-black text-gray-800 mb-2 text-lg leading-tight">
                                    {{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}
                                </h6>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500 italic">{{ $courseOffering->lecturer->name ?? 'មិនទាន់កំណត់' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-gray-400 mb-4 pb-4 border-b border-gray-100">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span>{{ $courseOffering->studentCourseEnrollments_count ?? $courseOffering->studentCourseEnrollments()->count() }} / {{ $courseOffering->capacity ?? '∞' }}</span>
                                </div>
                                @if ($courseOffering->start_date)
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <span>{{ $courseOffering->start_date->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            <form action="{{ route('student.enroll_self') }}" method="POST">
                                @csrf
                                <input type="hidden" name="course_offering_id" value="{{ $courseOffering->id }}">
                                <button class="w-full bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white py-3 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 group">
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                    {{ __('ចុះឈ្មោះចូលរៀន') }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
