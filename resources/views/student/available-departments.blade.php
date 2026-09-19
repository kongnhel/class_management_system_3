<x-app-layout>
    <div class="py-12 bg-[#f8fafc] min-h-screen font-['Battambang']">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-slate-800">{{ __('មុខវិជ្ជាដែលអាចចុះឈ្មោះបាន') }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ __('គ្រប់គ្រងការចុះឈ្មោះសិក្សាតាមមុខវិជ្ជា') }}</p>
                </div>
            </div>

            @if($departments->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-slate-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-600 mb-2">{{ __('មិនទាន់មានមុខវិជ្ជា') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('មិនទាន់មានមុខវិជ្ជាណាមួយសម្រាប់ចុះឈ្មោះនៅឡើយទេ។ សូមទាក់ទងរដ្ឋបាល។') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($departments as $dept)
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                    <i class="fas fa-book-open text-emerald-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">{{ $dept->name_km }}</h3>
                                    <p class="text-xs text-slate-500">{{ $dept->name_en }}</p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm text-slate-600">
                                @if($dept->faculty)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-university text-slate-400 w-4"></i>
                                        <span>{{ $dept->faculty->name_km }}</span>
                                    </div>
                                @endif
                                @if($dept->degree_level)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-medal text-slate-400 w-4"></i>
                                        <span>{{ $dept->degree_level }}</span>
                                    </div>
                                @endif
                                @if($dept->duration_years)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock text-slate-400 w-4"></i>
                                        <span>{{ $dept->duration_years }} ឆ្នាំ</span>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <form method="POST" action="{{ route('student.enroll-department') }}">
                                    @csrf
                                    <input type="hidden" name="department_id" value="{{ $dept->id }}">
                                    <button type="submit" class="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors">
                                        {{ __('ចុះឈ្មោះ') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
