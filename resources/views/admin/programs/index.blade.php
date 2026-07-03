<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 rounded-xl">
                            <i class="fas fa-graduation-cap text-emerald-600 text-xl"></i>
                        </div>
                        {{ __('គ្រប់គ្រងកម្មវិធីសិក្សា') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 ml-11">{{ __('រកឃើញកម្មវិធីសិក្សាចំនួន') }} {{ $programs->total() }} {{ __('កម្មវិធី') }}</p>
                </div>
                <a href="{{ route('admin.create-program') }}" class="mt-4 md:mt-0 inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-600 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    {{ __('បន្ថែមកម្មវិធីសិក្សាថ្មី') }}
                </a>
            </div>

            {{-- Toast --}}
            @if (session('success') || session('error'))
                <div x-data="{ show: true, progress: 100 }" x-init="let i=setInterval(()=>{progress-=1;if(progress<=0){show=false;clearInterval(i)}},30)" x-show="show" x-transition class="fixed top-6 right-6 z-[9999] w-full max-w-sm">
                    <div class="relative overflow-hidden bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-2xl p-4 ring-1 ring-black/5">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                @if(session('success'))
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-500/10 text-green-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-500/10 text-red-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 pt-0.5">
                                <p class="text-sm font-bold text-gray-900 leading-tight">{{ session('success') ? 'ជោគជ័យ' : 'កំហុស' }}</p>
                                <p class="mt-1 text-sm text-gray-600 leading-relaxed">{{ session('success') ?? session('error') }}</p>
                            </div>
                            <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="absolute bottom-0 left-0 h-1 bg-green-500 rounded-b-2xl transition-all duration-100" :style="{ width: progress + '%' }"></div>
                    </div>
                </div>
            @endif

            {{-- Search & Filters --}}
            <form method="GET" action="{{ route('admin.manage-programs') }}" id="filterForm" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Search --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ស្វែងរក') }}</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ស្វែងរកតាមឈ្មោះ ឬកម្រិតសញ្ញាបត្រ...') }}" class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 focus:bg-white transition" onkeyup="clearTimeout(window.__searchTimer);window.__searchTimer=setTimeout(()=>document.getElementById('filterForm').submit(),500)" onchange="document.getElementById('filterForm').submit()">
                        </div>
                    </div>

                    {{-- Department Filter --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('ដេប៉ាតឺម៉ង់') }}</label>
                        <select name="department_id" onchange="document.getElementById('filterForm').submit()" class="w-full px-3 py-2.5 rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 bg-gray-50 focus:bg-white transition">
                            <option value="">{{ __('ទាំងអស់') }}</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name_km }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Degree Level Filter --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">{{ __('កម្រិតសញ្ញាបត្រ') }}</label>
                        <select name="degree_level" onchange="document.getElementById('filterForm').submit()" class="w-full px-3 py-2.5 rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 bg-gray-50 focus:bg-white transition">
                            <option value="">{{ __('ទាំងអស់') }}</option>
                            @foreach($degreeLevels as $level)
                                <option value="{{ $level }}" {{ request('degree_level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-emerald-700 transition">
                            <i class="fas fa-filter"></i> {{ __('ច្រោះ') }}
                        </button>
                        @if(request()->hasAny(['search', 'department_id', 'degree_level']))
                            <a href="{{ route('admin.manage-programs') }}" class="inline-flex items-center gap-2 bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-200 transition">
                                <i class="fas fa-times"></i> {{ __('សម្អាត') }}
                            </a>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-gray-500">{{ __('រៀបចំតាម') }}:</label>
                        <select onchange="window.location.href=this.value" class="px-3 py-1.5 rounded-lg border-gray-200 text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                            @php
                                $currentSort = request('sort', 'name_km');
                                $currentDir = request('direction', 'asc');
                                $sorts = [
                                    'name_km' => 'ឈ្មោះខ្មែរ',
                                    'name_en' => 'ឈ្មោះអង់គ្លេស',
                                    'duration_years' => 'រយៈពេល',
                                    'created_at' => 'កាលបរិច្ឆេទ',
                                ];
                            @endphp
                            @foreach($sorts as $key => $label)
                                <option value="{{ route('admin.manage-programs', array_merge(request()->except(['sort','direction']), ['sort' => $key, 'direction' => 'asc'])) }}" {{ $currentSort === $key && $currentDir === 'asc' ? 'selected' : '' }}>{{ $label }} ↑</option>
                                <option value="{{ route('admin.manage-programs', array_merge(request()->except(['sort','direction']), ['sort' => $key, 'direction' => 'desc'])) }}" {{ $currentSort === $key && $currentDir === 'desc' ? 'selected' : '' }}>{{ $label }} ↓</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            {{-- Programs Grid --}}
            @if($programs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($programs as $program)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden group">
                            {{-- Card Header --}}
                            <div class="p-6 pb-4">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/20">
                                        <i class="fas fa-graduation-cap text-lg"></i>
                                    </div>
                                    @if($program->pathwayProgram)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">
                                            <i class="fas fa-link"></i> {{ __('ផ្លូវបន្ត') }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 leading-tight group-hover:text-emerald-600 transition-colors">{{ $program->name_km }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $program->name_en }}</p>
                            </div>

                            {{-- Card Body --}}
                            <div class="px-6 pb-4 space-y-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-building text-gray-400 w-4"></i>
                                    <span class="text-gray-600">{{ $program->department->name_km ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-calendar-alt text-gray-400 w-4"></i>
                                    <span class="text-gray-600">{{ $program->duration_years }} {{ __('ឆ្នាំ') }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-award text-gray-400 w-4"></i>
                                    <span class="text-gray-600">{{ $program->degree_level }}</span>
                                </div>
                                @if($program->pathwayProgram)
                                    <div class="flex items-center gap-2 text-sm">
                                        <i class="fas fa-arrow-right text-purple-400 w-4"></i>
                                        <span class="text-purple-600">{{ __('ផ្លូវបន្ត') }}: {{ $program->pathwayProgram->name_km }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Card Footer --}}
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('admin.edit-program', $program->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200 transition">
                                        <i class="fas fa-pen text-[10px]"></i> {{ __('កែប្រែ') }}
                                    </a>
                                    <button onclick="openDeleteModal('{{ route('admin.delete-program', $program->id) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold text-red-600 hover:bg-red-50 hover:border-red-200 transition">
                                        <i class="fas fa-trash text-[10px]"></i> {{ __('លុប') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($programs->hasPages())
                    <div class="mt-8">
                        {{ $programs->links() }}
                    </div>
                @endif

            @else
                {{-- Empty State --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                    <div class="p-4 bg-gray-100 rounded-2xl inline-block mb-4">
                        <i class="fas fa-graduation-cap text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700">{{ __('មិនមានកម្មវិធីសិក្សា') }}</h3>
                    <p class="text-sm text-gray-500 mt-1 mb-6">{{ __('សូមបង្កើតកម្មវិធីសិក្សាថ្មីដើម្បីចាប់ផ្តើម។') }}</p>
                    <a href="{{ route('admin.create-program') }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-emerald-700 transition">
                        <i class="fas fa-plus"></i> {{ __('បន្ថែមកម្មវិធីសិក្សាថ្មី') }}
                    </a>
                </div>
            @endif

        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-md" onclick="closeDeleteModal()"></div>
            <div class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-slate-200">
                <div class="bg-white px-8 pt-10 pb-8 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 text-red-500 mb-4">
                        <i class="fas fa-trash-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">{{ __('តើអ្នកប្រាកដទេ?') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('ទិន្នន័យនេះនឹងត្រូវលុបចេញពីប្រព័ន្ធរៀងរហូត។') }}</p>
                </div>
                <div class="bg-slate-50 px-8 py-5 flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-white border border-slate-200 text-sm font-bold text-slate-600 rounded-xl hover:bg-slate-100 transition">
                        {{ __('បោះបង់') }}
                    </button>
                    <form id="delete-form" method="POST" action="" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-sm font-bold text-white rounded-xl hover:bg-red-700 shadow-lg shadow-red-200 transition active:scale-95">
                            {{ __('យល់ព្រមលុប') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const deleteModal = document.getElementById('delete-modal');
        const deleteForm = document.getElementById('delete-form');

        function openDeleteModal(deleteUrl) {
            deleteForm.action = deleteUrl;
            deleteModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            deleteModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDeleteModal();
        });
    </script>
</x-app-layout>
