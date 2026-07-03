<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">

            {{-- Header --}}
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-amber-100 rounded-2xl p-3">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ __('បញ្ជីសេចក្តីប្រកាស') }}</h1>
                            <p class="text-gray-500 mt-1">{{ __('គ្រប់គ្រង និងកែសម្រួលសេចក្តីប្រកាសរបស់សាលា') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold rounded-xl shadow-lg hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200 hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('បង្កើតសេចក្តីប្រកាសថ្មី') }}
                    </a>
                </div>
            </div>

            {{-- Toast --}}
            @if (session('success') || session('error'))
            <div
                x-data="{
                    show: false,
                    progress: 100,
                    init() {
                        this.show = true;
                        const timer = setInterval(() => {
                            this.progress -= 1;
                            if (this.progress <= 0) { this.show = false; clearInterval(timer); }
                        }, 50);
                    }
                }"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="fixed top-6 right-6 z-[9999] w-full max-w-sm"
            >
                <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-200 p-4">
                    <div class="flex items-center gap-3">
                        @if(session('success'))
                            <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @else
                            <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900">{{ session('success') ? 'ជោគជ័យ!' : 'បរាជ័យ!' }}</p>
                            <p class="text-sm text-gray-600 truncate">{{ session('success') ?? session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-100 rounded-b-2xl overflow-hidden">
                        <div class="h-full transition-all duration-75 ease-linear {{ session('success') ? 'bg-emerald-500' : 'bg-red-500' }}" :style="`width: ${progress}%`"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Search Bar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" action="{{ route('admin.announcements.index') }}">
                    <div class="flex items-center gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('ស្វែងរកសេចក្តីប្រកាស...') }}"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                            />
                        </div>
                        @if(request('search'))
                            <a href="{{ route('admin.announcements.index') }}" class="px-4 py-3 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors duration-200">
                                {{ __('សម្អាត') }}
                            </a>
                        @endif
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold text-sm rounded-xl shadow hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200">
                            {{ __('ស្វែងរក') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider">{{ __('ចំណងជើង') }}</th>
                            <th class="text-left px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider">{{ __('ខ្លឹមសារ') }}</th>
                            <th class="text-left px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider">{{ __('អ្នកបង្ហោះ') }}</th>
                            <th class="text-left px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider">{{ __('គោលដៅ') }}</th>
                            <th class="text-left px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider">{{ __('កាលបរិច្ឆេទ') }}</th>
                            <th class="text-right px-6 py-4 font-bold text-gray-600 text-xs uppercase tracking-wider">{{ __('សកម្មភាព') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($announcements as $announcement)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $announcement->title_km }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $announcement->title_en }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-600 text-sm line-clamp-2">{{ Str::limit($announcement->content_km, 60) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-700">{{ $announcement->poster->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    {{ __($announcement->target_role) }}
                                </span>
                                @if ($announcement->course_offering_id)
                                    <div class="text-xs text-gray-500 mt-1">{{ $announcement->courseOffering->course->name_en }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                {{ $announcement->created_at->format('d M, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        {{ __('កែប្រែ') }}
                                    </a>
                                    <button type="button" onclick="openDeleteModal('{{ route('admin.announcements.destroy', $announcement->id) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        {{ __('លុប') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">{{ __('មិនទាន់មានសេចក្តីប្រកាសណាមួយនៅឡើយ') }}</p>
                                    <a href="{{ route('admin.announcements.create') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white font-bold text-sm rounded-xl hover:bg-emerald-700 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        {{ __('បង្កើតសេចក្តីប្រកាសថ្មី') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-4">
                @forelse ($announcements as $announcement)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 truncate">{{ $announcement->title_km }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $announcement->title_en }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 ml-2 flex-shrink-0">
                            {{ __($announcement->target_role) }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($announcement->content_km, 100) }}</p>

                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        <span>{{ $announcement->poster->name }}</span>
                        <span>{{ $announcement->created_at->format('d M, Y') }}</span>
                    </div>

                    @if ($announcement->course_offering_id)
                        <div class="text-xs text-gray-500 mb-3 pb-3 border-b border-gray-100">
                            {{ $announcement->courseOffering->course->name_en }} ({{ $announcement->courseOffering->program->name_en }})
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ __('កែប្រែ') }}
                        </a>
                        <button type="button" onclick="openDeleteModal('{{ route('admin.announcements.destroy', $announcement->id) }}')" class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-bold text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ __('លុប') }}
                        </button>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">{{ __('មិនទាន់មានសេចក្តីប្រកាសណាមួយនៅឡើយ') }}</p>
                    <a href="{{ route('admin.announcements.create') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white font-bold text-sm rounded-xl hover:bg-emerald-700 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('បង្កើតសេចក្តីប្រកាសថ្មី') }}
                    </a>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($announcements->hasPages())
            <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                {{ $announcements->links() }}
            </div>
            @endif

        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
            <div id="modal-container" class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0">
                <div class="p-6">
                    <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-2xl bg-red-100 mb-4">
                        <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900" id="modal-title">{{ __('លុបសេចក្តីប្រកាស') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('តើអ្នកពិតជាចង់លុបសេចក្តីប្រកាសនេះមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់វិញបានទេ។') }}</p>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <form id="delete-form" method="POST" action="" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-bold text-sm rounded-xl shadow hover:from-red-600 hover:to-red-700 transition-all duration-200">
                            {{ __('លុបចោលភ្លាម') }}
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 py-3 bg-gray-100 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-200 transition-colors duration-200">
                        {{ __('បោះបង់') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(deleteUrl) {
            const modal = document.getElementById('delete-modal');
            const container = document.getElementById('modal-container');
            const form = document.getElementById('delete-form');
            form.action = deleteUrl;
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            });
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            const container = document.getElementById('modal-container');
            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        document.getElementById('delete-modal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    </script>
</x-app-layout>
