<div class="p-6" wire:poll.30s>

    {{-- Search Bar --}}
    <div class="mb-6">
        <div class="relative max-w-md">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('ស្វែងរកមហាវិទ្យាល័យ...') }}"
                class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200"
            />
            @if($search)
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    @if ($faculties->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                </svg>
            </div>
            @if($search)
                <p class="text-sm font-semibold text-gray-700">{{ __('មិនរកឃើញមហាវិទ្យាល័យ') }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('សូមព្យាយាមស្វែងរកជាមួយពាក្យគន្លឹសខុសគ្នា') }}</p>
            @else
                <p class="text-sm font-semibold text-gray-700">{{ __('មិនទាន់មានមហាវិទ្យាល័យទេ') }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('ចាប់ផ្តើមដោយបន្ថែមមហាវិទ្យាល័យដំបូងរបស់អ្នក។') }}</p>
                <div class="mt-4">
                    <a href="{{ route('admin.create-faculty') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('បន្ថែមមហាវិទ្យាល័យថ្មី') }}
                    </a>
                </div>
            @endif
        </div>
    @else
        {{-- CARD/GRID VIEW --}}
        <div x-show="viewMode === 'grid'" x-transition:enter.duration.300ms>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($faculties as $faculty)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition-all duration-200">
                        <div>
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                                    </svg>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z" />
                                    </svg>
                                    {{ $faculty->departments->count() }} {{ __('ដេប៉ាតឺម៉ង់') }}
                                </span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 leading-snug">{{ $faculty->name_km }}</h4>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $faculty->name_en }}</p>
                            <div class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <span class="truncate">{{ $faculty->dean->name ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-5 pt-4 border-t border-gray-100">
                            <a href="{{ route('admin.edit-faculty', $faculty->id) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-semibold text-emerald-600 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                {{ __('កែប្រែ') }}
                            </a>
                            <button type="button" onclick="openDeleteModal('{{ $faculty->id }}', '{{ addslashes($faculty->name_km) }}')" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-semibold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                {{ __('លុប') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TABLE VIEW --}}
        <div x-show="viewMode === 'table'" x-transition:enter.duration.300ms style="display: none;">
            <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('លេខរៀង') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('ឈ្មោះមហាវិទ្យាល័យ') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">{{ __('ប្រធាន') }}</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">{{ __('ដេប៉ាតឺម៉ង់') }}</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('សកម្មភាព') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($faculties as $faculty)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $loop->iteration + (($faculties->currentPage() - 1) * $faculties->perPage()) }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $faculty->name_km }}</div>
                                    <div class="text-xs text-gray-400 md:hidden">{{ $faculty->name_en }}</div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 hidden md:table-cell">
                                    {{ $faculty->dean->name ?? '—' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center hidden lg:table-cell">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600">
                                        {{ $faculty->departments->count() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.edit-faculty', $faculty->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                            </svg>
                                            {{ __('កែប្រែ') }}
                                        </a>
                                        <button type="button" onclick="openDeleteModal('{{ $faculty->id }}', '{{ addslashes($faculty->name_km) }}')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            {{ __('លុប') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $faculties->links('pagination::tailwind') }}
        </div>
    @endif

    {{-- DELETE MODAL --}}
    <div id="delete-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" x-data x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
            <div class="relative bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="p-8 text-center">
                    <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-2xl bg-red-50 mb-5">
                        <svg class="h-7 w-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('បញ្ជាក់ការលុប') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ __('តើអ្នកពិតជាចង់លុបមហាវិទ្យាល័យ') }} <span class="font-semibold text-gray-900" id="delete-faculty-name"></span> {{ __('មែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ។') }}</p>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        {{ __('បោះបង់') }}
                    </button>
                    <form id="delete-form" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors">
                            {{ __('យល់ព្រមលុប') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(facultyId, facultyName) {
            const modal = document.getElementById('delete-modal');
            const form = document.getElementById('delete-form');
            const nameEl = document.getElementById('delete-faculty-name');
            form.action = `/admin/faculties/${facultyId}`;
            nameEl.textContent = facultyName;
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            modal.classList.add('hidden');
        }
    </script>
</div>
