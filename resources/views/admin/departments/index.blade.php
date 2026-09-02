<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ viewMode: 'grid' }" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:p-8">

                {{-- Header --}}
                <div class="flex flex-col md:flex-row items-center justify-between mb-8 pb-6 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <span class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl shadow-sm">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">{{ __('គ្រប់គ្រងដេប៉ាតឺម៉ង់') }}</h2>
                            <p class="mt-1 text-sm text-gray-500">{{ __('បញ្ជីឈ្មោះដេប៉ាតឺម៉ង់ទាំងអស់នៅក្នុងប្រព័ន្ធ') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 md:mt-0 flex items-center gap-3">
                        {{-- View Toggle --}}
                        <div class="inline-flex rounded-xl bg-gray-100 p-1">
                            <button @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-white shadow text-emerald-600' : 'text-gray-400 hover:text-gray-600'"
                                class="p-2 rounded-lg transition" title="{{ __('ទម្រង់ប័ណ្ណ') }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </button>
                            <button @click="viewMode = 'table'"
                                :class="viewMode === 'table' ? 'bg-white shadow text-emerald-600' : 'text-gray-400 hover:text-gray-600'"
                                class="p-2 rounded-lg transition" title="{{ __('ទម្រង់តារាង') }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                            </button>
                        </div>

                        {{-- Add Button --}}
                        <a href="{{ route('admin.create-department') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white font-bold text-sm rounded-xl shadow hover:bg-emerald-700 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="hidden sm:inline">{{ __('បន្ថែមដេប៉ាតឺម៉ង់ថ្មី') }}</span>
                            <span class="sm:hidden">{{ __('បន្ថែម') }}</span>
                        </a>
                    </div>
                </div>

                {{-- Search & Faculty Filter --}}
                <form method="GET" action="{{ route('admin.manage-departments') }}" data-admin-realtime-filter class="mb-6">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('ស្វែងរកដេប៉ាតឺម៉ង់...') }}"
                                autocomplete="off"
                                class="block w-full pl-11 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50 transition"
                            />
                            @if(request('search'))
                                <button type="button" data-admin-clear-search class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                        <div>
                            <select name="faculty_id" class="w-full sm:w-56 px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-gray-50 transition">
                                <option value=""> {{ __('មហាវិទ្យាល័យទាំងអស់') }}</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->name_km }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                {{-- Results --}}
                <div data-admin-results>
                @if ($departments->isNotEmpty())
                    {{-- GRID VIEW --}}
                    <div x-show="viewMode === 'grid'" x-transition:enter.duration.300ms>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($departments as $department)
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition-all duration-200">
                                    <div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-lg font-bold text-gray-900 truncate">{{ $department->name_km }}</h4>
                                                <p class="text-sm text-gray-500 truncate">{{ $department->name_en }}</p>
                                            </div>
                                        </div>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex items-center gap-2 text-gray-600">
                                                <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                                                </svg>
                                                <span>{{ $department->faculty->name_km ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-gray-600">
                                                <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span>{{ $department->head->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 mt-5 pt-4 border-t border-gray-100">
                                        <a href="{{ route('admin.edit-department', $department->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            {{ __('កែប្រែ') }}
                                        </a>
                                        <button type="button" onclick="openDeleteModal('{{ route('admin.delete-department', $department->id) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            {{ __('លុប') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $departments->links('pagination::tailwind') }}
                        </div>
                    </div>

                    {{-- TABLE VIEW --}}
                    <div x-show="viewMode === 'table'" x-transition:enter.duration.300ms style="display: none;">
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('លេខរៀង') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ឈ្មោះដេប៉ាតឺម៉ង់') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('មហាវិទ្យាល័យ') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('ប្រធាន') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('សកម្មភាព') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($departments as $department)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                                {{ $loop->iteration + (($departments->currentPage() - 1) * $departments->perPage()) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-gray-900">{{ $department->name_km }}</div>
                                                <div class="text-xs text-gray-500">{{ $department->name_en }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $department->faculty->name_km ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $department->head->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('admin.edit-department', $department->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        {{ __('កែប្រែ') }}
                                                    </a>
                                                    <button type="button" onclick="openDeleteModal('{{ route('admin.delete-department', $department->id) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                        <div class="mt-6">
                            {{ $departments->links('pagination::tailwind') }}
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-16">
                        <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-2xl bg-gray-100 mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        @if(request('search') || request('faculty_id'))
                            <p class="text-gray-500 font-medium">{{ __('មិនพบដេប៉ាតឺម៉ង់ដែលត្រូវនឹងការស្វែងរក') }}</p>
                            <p class="text-gray-400 text-sm mt-1">{{ __('សូមព្យាយាមស្វែងរកជាមួយពាក្យគន្លឹះផ្សេង') }}</p>
                            <a href="{{ route('admin.manage-departments') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-200 transition">
                                <i class="fas fa-times"></i> {{ __('សម្អាត') }}
                            </a>
                        @else
                            <p class="text-gray-500 font-medium">{{ __('មិនទាន់មានដេប៉ាតឺម៉ង់ណាមួយនៅឡើយទេ។') }}</p>
                            <p class="text-gray-400 text-sm mt-1">{{ __('ចុចប៊ូតុងខាងក្រោមដើម្បីបន្ថែមដេប៉ាតឺម៉ង់ថ្មី។') }}</p>
                            <a href="{{ route('admin.create-department') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('បន្ថែមដេប៉ាតឺម៉ង់ថ្មី') }}
                            </a>
                        @endif
                    </div>
                @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
            <div class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-gray-200">
                <div class="bg-white px-8 pt-10 pb-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-red-50 text-red-500 mb-5">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('តើអ្នកប្រាកដទេ?') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('ទិន្នន័យនេះនឹងត្រូវលុបចេញពីប្រព័ន្ធរៀងរហូត។ អ្នកមិនអាចស្ដារវាឡើងវិញបានឡើយ។') }}</p>
                </div>
                <div class="bg-gray-50 px-8 py-5 flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-white border border-gray-200 text-sm font-bold text-gray-600 rounded-xl hover:bg-gray-100 transition">
                        {{ __('បោះបង់') }}
                    </button>
                    <form id="delete-form" method="POST" action="" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-sm font-bold text-white rounded-xl hover:bg-red-700 shadow-sm transition active:scale-95">
                            {{ __('យល់ព្រមលុប') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

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
