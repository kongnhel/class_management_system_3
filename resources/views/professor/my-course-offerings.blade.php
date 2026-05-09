<x-app-layout>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700;900&family=Inter:wght@400;500;600;700&display=swap');

    .page-wrap { 
        font-family: 'Inter', system-ui, sans-serif; 
    }
    .khmer { 
        font-family: 'Hanuman', serif; 
    }

    /* Main Container */
    .dashboard-bg {
        background: #f8fafc;
        min-height: 100vh;
    }

    /* Card Styles */
    .course-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .course-card:hover {
        border-color: #22c55e;
        box-shadow: 0 10px 25px -5px rgba(34, 197, 94, 0.12),
                    0 8px 10px -6px rgba(34, 197, 94, 0.1);
        transform: translateY(-4px);
    }

    /* Header Accent */
    .header-accent {
        height: 4px;
        background: linear-gradient(to right, #16a34a, #4ade80);
        border-radius: 4px;
    }

    /* Badge */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 5px 11px;
        border-radius: 9999px;
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    /* Button */
    .btn-primary {
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: white;
        font-weight: 600;
        padding: 14px 24px;
        border-radius: 12px;
        border: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #15803d, #14532d);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(22, 163, 74, 0.35);
    }

    /* Modal */
    .modal-overlay {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
    }

    .modal-content {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
        max-width: 460px;
        width: 100%;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        border-radius: 12px;
        color: #334155;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    
    .menu-item:hover {
        background: #f0fdf4;
        color: #166534;
    }

    .flash-message {
        border-left: 4px solid;
        padding: 14px 18px;
        border-radius: 12px;
        font-weight: 500;
    }

    [x-cloak] { display: none !important; }
</style>

<div class="page-wrap dashboard-bg py-10">
    <div class="max-w-6xl mx-auto px-6">

        {{-- Header --}}
        <div class="mb-10">
            <div class="flex items-center gap-4 mb-3">
                <div class="header-accent w-8"></div>
                <h1 class="khmer text-3xl font-bold text-slate-900 tracking-tight">
                    {{ __('មុខវិជ្ជាខ្ញុំបង្រៀន') }}
                </h1>
            </div>
            <p class="khmer text-slate-600 pl-12 text-[15px]">
                {{ __('បញ្ជីវគ្គសិក្សាទាំងអស់ដែលអ្នកកំពុងបង្រៀន') }}
            </p>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="flash-message bg-emerald-50 border-emerald-500 text-emerald-800 mb-8 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="khmer">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flash-message bg-red-50 border-red-500 text-red-800 mb-8 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z" />
                </svg>
                <span class="khmer">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Content --}}
        @if ($courseOfferings->isEmpty())
            <div class="bg-white border border-dashed border-slate-300 rounded-2xl py-20 text-center">
                <div class="mx-auto w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.462 9.492 5 8 5a4 4 0 00-4 4v8a4 4 0 004 4c1.492 0 2.832-.462 4-1.253m0-13C13.168 5.462 14.508 5 16 5a4 4 0 014 4v8a4 4 0 01-4 4c-1.492 0-2.832-.462-4-1.253" />
                    </svg>
                </div>
                <p class="khmer text-xl font-semibold text-slate-700 mb-2">{{ __('មិនទាន់មានមុខវិជ្ជាត្រូវបានចាត់តាំង') }}</p>
                <p class="khmer text-slate-500">{{ __('សូមទាក់ទងរដ្ឋបាល ប្រសិនបើមានចម្ងល់។') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($courseOfferings as $offering)
                    <div class="course-card p-7 flex flex-col">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.462 9.492 5 8 5a4 4 0 00-4 4v8a4 4 0 004 4c1.492 0 2.832-.462 4-1.253m0-13C13.168 5.462 14.508 5 16 5a4 4 0 014 4v8a4 4 0 01-4 4c-1.492 0-2.832-.462-4-1.253" />
                                </svg>
                            </div>
                            
                            <div class="flex gap-2">
                                <span class="badge khmer">
                                    <span>{{ $offering->academic_year }}</span>
                                </span>
                                <span class="badge khmer">
                                    ឆមាស {{ $offering->semester }}
                                </span>
                            </div>
                        </div>

                        <div class="flex-1 mb-8">
                            <h3 class="khmer text-xl font-semibold text-slate-900 leading-tight mb-2">
                                {{ $offering->course->title_km ?? 'N/A' }}
                            </h3>
                            <p class="text-sm text-slate-500 line-clamp-2">
                                {{ $offering->course->title_en ?? 'N/A' }}
                            </p>
                        </div>

                        <button
                            x-data="{}"
                            x-on:click="$dispatch('open-course-management-modal', { courseOfferingId: {{ $offering->id }} })"
                            class="btn-primary w-full flex items-center justify-center gap-2 text-base khmer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.608 3.292 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ __('គ្រប់គ្រងវគ្គសិក្សា') }}
                        </button>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-10 flex justify-center">
                {{ $courseOfferings->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>

{{-- Modal --}}
<div x-data="{ open: false, courseOfferingId: null }"
     x-on:open-course-management-modal.window="open = true; courseOfferingId = $event.detail.courseOfferingId"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">

    <div x-show="open" 
         class="modal-overlay fixed inset-0"
         @click="open = false"></div>

    <div x-show="open"
         class="modal-content relative">
        
        {{-- Header --}}
        <div class="px-8 pt-8 pb-6 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-emerald-50 rounded-2xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.462 9.492 5 8 5a4 4 0 00-4 4v8a4 4 0 004 4c1.492 0 2.832-.462 4-1.253m0-13C13.168 5.462 14.508 5 16 5a4 4 0 014 4v8a4 4 0 01-4 4c-1.492 0-2.832-.462-4-1.253" />
                        </svg>
                    </div>
                    <h3 class="khmer text-2xl font-bold text-slate-900">{{ __('គ្រប់គ្រងវគ្គសិក្សា') }}</h3>
                </div>
                
                <button @click="open = false" 
                        class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    ✕
                </button>
            </div>
        </div>

        {{-- Menu --}}
        <div class="p-6 space-y-2">
            @php
                $menuItems = [
                    [
                        'route' => 'professor.students.in-course-offering',
                        'label' => 'មើលនិស្សិត',
                        'desc'  => 'View enrolled students',
                        'icon'  => 'M17 20h5v-2a3 3 0 01-5.356-1.857M17 20H7m5-2v-2c0-.656-.126-1.284-.356-1.852M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.284.356-1.852m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    ],
                    [
                        'route' => 'professor.manage-grades',
                        'label' => 'គ្រប់គ្រងពិន្ទុ',
                        'desc'  => 'Manage student grades',
                        'icon'  => 'M19 21V5a2 2 0 01-2 2H7a2 2 0 01-2 2v16m14 0h2m-2 0h-5m-4 0H3',
                    ],
                ];
            @endphp

            @foreach ($menuItems as $item)
                <a href="#"
                   :href="courseOfferingId ? '{{ route($item['route'], ['offering_id' => ':id']) }}'.replace(':id', courseOfferingId) : '#'"
                   class="menu-item">
                    <div class="w-10 h-10 bg-emerald-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="khmer font-semibold text-slate-800">{{ __($item['label']) }}</div>
                        <div class="text-xs text-slate-500">{{ $item['desc'] }}</div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="p-6 border-t border-slate-100">
            <button @click="open = false"
                    class="khmer w-full py-3.5 text-slate-600 font-medium bg-slate-100 hover:bg-slate-200 rounded-2xl transition-colors">
                {{ __('បិទ') }}
            </button>
        </div>
    </div>
</div>

</x-app-layout>