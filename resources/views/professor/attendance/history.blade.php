<x-app-layout>
    <style>
        .font-khmer { font-family: 'Battambang', 'Hanuman', sans-serif; }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center font-khmer">
            <div>
                <h2 class="font-black text-3xl text-slate-800">{{ __('ប្រវត្តិវត្តមានរបស់លោកគ្រូ') }}</h2>
                <p class="text-slate-500">{{ __('កំណត់ត្រាចុះវត្តមានទាំងអស់') }}</p>
            </div>
            <a href="{{ route('professor.dashboard') }}" 
               class="px-5 py-3 bg-white border border-slate-200 rounded-2xl font-bold text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
    </x-slot>

    <div class="bg-[#f8fafc] min-h-screen font-khmer pb-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            @if(session('success'))
                <div class="bg-emerald-500 text-white p-4 rounded-2xl mb-6">{{ session('success') }}</div>
            @endif

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-emerald-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800">{{ $attendances->total() }}</p>
                        <p class="text-xs text-slate-400 font-bold">{{ __('សរុបវត្តមាន') }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-emerald-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800">{{ $attendances->where('verified_at', '>=', now()->startOfWeek())->count() }}</p>
                        <p class="text-xs text-slate-400 font-bold">{{ __('សប្តាហ៍នេះ') }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-amber-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800">{{ $attendances->where('verified_at', '>=', now()->startOfMonth())->count() }}</p>
                        <p class="text-xs text-slate-400 font-bold">{{ __('ខែនេះ') }}</p>
                    </div>
                </div>
            </div>

            {{-- Attendance List --}}
            <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100">
                    <h3 class="text-lg font-black text-slate-800">{{ __('កំណត់ត្រាចុះវត្តមាន') }}</h3>
                </div>

                @forelse($attendances as $att)
                <div class="px-8 py-5 border-b border-slate-50 hover:bg-slate-50/50 transition-all {{ $loop->last ? 'border-b-0' : '' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 leading-tight">
                                    {{ $att->courseOffering?->course?->title_km ?? $att->courseOffering?->course?->title_en ?? 'N/A' }}
                                </h4>
                                <p class="text-xs text-slate-400 font-bold mt-1">
                                     {{ __('ជំនាន់') }} {{ $att->courseOffering?->generation ?? $att->courseOffering?->targetPrograms->pluck('generation')->filter()->first() ?? '...' }}
                                     • {{ __('បន្ទប់') }} {{ $att->courseOffering?->room_number ?? 'Online' }}
                                    • {{ $att->courseOffering?->semester ?? '' }}/{{ $att->courseOffering?->academic_year ?? '' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-5 text-sm pl-15 sm:pl-0">
                            <div class="flex items-center gap-2 text-slate-500">
                                <i class="fas fa-calendar-day text-xs"></i>
                                <span class="font-bold">{{ \Carbon\Carbon::parse($att->verified_at)->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-500">
                                <i class="fas fa-clock text-xs"></i>
                                <span class="font-bold">{{ \Carbon\Carbon::parse($att->verified_at)->format('H:i') }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-check-double"></i> {{ __('វត្តមាន') }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="px-8 py-20 text-center">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-times text-2xl text-slate-300"></i>
                        </div>
                        <p class="text-slate-400 font-bold">{{ __('មិនទាន់មានកំណត់ត្រាវត្តមាននៅឡើយទេ') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-6 flex justify-center">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
