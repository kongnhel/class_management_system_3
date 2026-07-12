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

                {{-- Livewire Table --}}
                <div class="mt-2">
                    @livewire('department-table')
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
