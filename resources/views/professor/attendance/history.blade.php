<x-app-layout>
    <style>
        .font-khmer { font-family: 'Kantumruy Pro', sans-serif; }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center font-khmer">
            <div>
                <h2 class="font-black text-3xl text-slate-800">ប្រវត្តិវត្តមានរបស់លោកគ្រូ</h2>
                <p class="text-slate-500">កំណត់ត្រាចុះវត្តមានទាំងអស់</p>
            </div>
            <a href="{{ route('professor.dashboard') }}" 
               class="px-5 py-3 bg-white border border-slate-200 rounded-2xl font-bold text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
    </x-slot>

    <div class="bg-[#f8fafc] min-h-screen font-khmer pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            @if(session('success'))
                <div class="bg-emerald-500 text-white p-4 rounded-2xl mb-6">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-2xl font-black text-slate-800">កំណត់ត្រាចុះវត្តមាន</h3>
                    <span class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-2xl text-sm font-bold">
                        {{ $attendances->total() }} ដង
                    </span>
                </div>

                @forelse($attendances as $att)
                <div class="p-8 border-b border-slate-100 hover:bg-slate-50 transition-all group">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex gap-5">
                            <!-- Icon -->
                            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl flex items-center justify-center shadow-inner flex-shrink-0">
                                <i class="fas fa-check-circle text-3xl"></i>
                            </div>
                            
                            <div>
                                <h4 class="font-bold text-xl text-slate-800 leading-tight">
                                    {{ $att->courseOffering->course->name_km ?? $att->courseOffering->course->name }}
                                </h4>
                                <p class="text-slate-500 text-sm mt-1">
                                    ជំនាន់៖ <b>{{ $att->courseOffering->generation }}</b> • 
                                    បន្ទប់៖ <b>{{ $att->room?->room_number ?? 'Online' }}</b>
                                </p>
                                <div class="flex items-center gap-4 text-xs text-slate-500 mt-3">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-calendar"></i> 
                                        {{ \Carbon\Carbon::parse($att->verified_date)->format('d M Y') }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-clock"></i> 
                                        {{ \Carbon\Carbon::parse($att->verified_at)->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-5 py-2.5 rounded-2xl text-sm font-bold">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ round($att->distance ?? 0) }} ម៉ែត្រ</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-2">
                                Session ID: {{ $att->session_id }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="p-20 text-center">
                        <i class="fas fa-calendar-times text-6xl text-slate-200 mb-4"></i>
                        <p class="text-slate-400 font-bold">មិនទាន់មានកំណត់ត្រាវត្តមាននៅឡើយទេ</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</x-app-layout>