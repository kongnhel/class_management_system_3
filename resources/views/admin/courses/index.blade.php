<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen font-inter antialiased">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Main Container --}}
            <div x-data="{ viewMode: 'table', search: '' }"
                 class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Header Section --}}
                <div class="p-6 lg:p-8 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-md shadow-blue-200">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.462 9.492 5 8 5c-5.072 0-8 3.844-8 7s2.928 7 8 7c1.492 0 2.832-.462 4-1.253m0-13C13.168 5.462 14.508 5 16 5c5.072 0 8 3.844 8 7s-2.928 7-8 7c-1.492 0-2.832-.462-4-1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">{{ __('គ្រប់គ្រងមុខវិជ្ជា') }}</h2>
                            <p class="mt-1 text-sm text-gray-500">{{ __('គ្រប់គ្រង និងពិនិត្យមើលបញ្ជីឈ្មោះមុខវិជ្ជាទាំងអស់') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Search Input --}}
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="text" x-model="search" placeholder="{{ __('ស្វែងរកមុខវិជ្ជា...') }}"
                                   class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full md:w-72 transition-all">
                        </div>

                        {{-- VIEW TOGGLE --}}
                        <div class="inline-flex bg-gray-100 p-1 rounded-xl">
                            <button @click="viewMode = 'table'"
                                    :class="viewMode === 'table' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                                {{ __('បញ្ជី') }}
                            </button>
                            <button @click="viewMode = 'grid'"
                                    :class="viewMode === 'grid' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                {{ __('ប័ណ្ណ') }}
                            </button>
                        </div>

                        <a href="{{ route('admin.create-course') }}"
                           class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-blue-200 gap-2 active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            {{ __('បន្ថែមមុខវិជ្ជា') }}
                        </a>
                    </div>
                </div>

                {{-- Toast Notification --}}
                @if (session('success') || session('error'))
                <div x-data="{
                        show: true,
                        progress: 100,
                        init() {
                            let duration = 5000;
                            let step = 100 / (duration / 50);
                            let timer = setInterval(() => {
                                this.progress -= step;
                                if (this.progress <= 0) {
                                    clearInterval(timer);
                                    this.show = false;
                                }
                            }, 50);
                        }
                    }"
                     x-init="init()"
                     x-show="show"
                     x-transition:enter="transform ease-out duration-300 transition"
                     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed top-6 right-6 z-[9999] w-full max-w-sm">
                    <div class="bg-white border-l-4 {{ session('success') ? 'border-blue-500' : 'border-red-500' }} shadow-2xl rounded-2xl overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if(session('success'))
                                        <div class="h-10 w-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-900">{{ session('success') ? __('ប្រតិបត្តិការជោគជ័យ') : __('មានបញ្ហាអ្វីមួយ') }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ session('success') ?? session('error') }}</p>
                                </div>
                                <button @click="show = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                        {{-- Progress Bar --}}
                        <div class="h-1 bg-gray-100">
                            <div class="h-full {{ session('success') ? 'bg-blue-500' : 'bg-red-500' }} transition-all duration-100"
                                 :style="`width: ${progress}%`"></div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Content --}}
                <div class="p-6 lg:p-8">
                    <div class="space-y-12">
                        @php
                            $filteredGrouped = $coursesGrouped;
                        @endphp

                        @forelse ($coursesGrouped as $programName => $generations)
                            <div class="program-section"
                                 x-show="search === '' || '{{ mb_strtolower($programName) }}'.includes(search.toLowerCase()) || @js(collect($generations)->flatten()->pluck('title_km', 'title_en')->toArray()) !== null">

                                {{-- Program Title --}}
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="flex flex-col">
                                        <h3 class="text-xl font-bold text-gray-800 tracking-tight">{{ $programName }}</h3>
                                        <div class="h-1 w-12 bg-blue-600 rounded-full mt-1"></div>
                                    </div>
                                </div>

                                @foreach ($generations as $generationName => $courseList)
                                    @php
                                        $searchLower = strtolower($search);
                                        $filteredCourses = $search === '' ? $courseList : $courseList->filter(function ($course) use ($searchLower) {
                                            return str_contains(strtolower($course->title_km), $searchLower)
                                                || str_contains(strtolower($course->title_en), $searchLower)
                                                || str_contains(strtolower($course->department->name_km ?? ''), $searchLower);
                                        });
                                    @endphp

                                    @if($filteredCourses->isEmpty())
                                        @continue
                                    @endif

                                    <div class="mb-12 last:mb-0">
                                        <div class="flex items-center gap-3 mb-6">
                                            <span class="text-[11px] font-bold text-blue-700 bg-blue-50 px-4 py-1.5 rounded-full uppercase tracking-widest border border-blue-100">
                                                {{ $generationName }}
                                            </span>
                                            <span class="text-xs text-gray-400 font-bold">{{ $filteredCourses->count() }} {{ __('មុខវិជ្ជា') }}</span>
                                            <div class="flex-1 h-px bg-gradient-to-r from-gray-200 to-transparent"></div>
                                        </div>

                                        {{-- GRID VIEW --}}
                                        <div x-show="viewMode === 'grid'" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            @foreach ($filteredCourses as $course)
                                                <div class="group bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-400 hover:shadow-lg transition-all relative overflow-hidden">
                                                    <div class="flex justify-between items-start mb-5">
                                                        <div class="h-12 w-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.462 9.492 5 8 5c-5.072 0-8 3.844-8 7s2.928 7 8 7c1.492 0 2.832-.462 4-1.253m0-13C13.168 5.462 14.508 5 16 5c5.072 0 8 3.844 8 7s-2.928 7-8 7c-1.492 0-2.832-.462-4-1.253"/></svg>
                                                        </div>
                                                        <div class="flex gap-1">
                                                            <a href="{{ route('admin.edit-course', $course->id) }}"
                                                               class="px-3 py-1.5 text-xs font-bold text-blue-600 hover:bg-blue-50 rounded-xl transition-all border border-blue-200">
                                                                {{ __('កែប្រែ') }}
                                                            </a>
                                                            <button @click="openDeleteModal('{{ route('admin.delete-course', $course->id) }}')"
                                                                    class="px-3 py-1.5 text-xs font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all border border-red-200">
                                                                {{ __('លុប') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <h4 class="text-lg font-bold text-gray-900 mb-1">{{ $course->title_km }}</h4>
                                                    <p class="text-xs text-gray-400 font-bold uppercase mb-3">{{ $course->title_en }}</p>

                                                    <div class="flex flex-wrap gap-1 mb-4">
                                                        @foreach($course->programs as $p)
                                                            <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-lg font-bold">{{ $p->name_km }}</span>
                                                        @endforeach
                                                    </div>

                                                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span class="text-xs font-bold text-gray-600">{{ $course->credits }} {{ __('ក្រេឌីត') }}</span>
                                                        </div>
                                                        <span class="text-[10px] bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg font-bold border border-blue-100">
                                                            {{ $course->department->name_km ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- TABLE VIEW --}}
                                        <div x-show="viewMode === 'table'" x-transition class="overflow-x-auto border border-gray-200 rounded-2xl shadow-sm">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase">{{ __('ព័ត៌មានមុខវិជ្ជា') }}</th>
                                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase">{{ __('កម្មវិធីសិក្សា') }}</th>
                                                        <th class="px-6 py-4 text-center text-[11px] font-bold text-gray-500 uppercase">{{ __('ក្រេឌីត') }}</th>
                                                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase">{{ __('សកម្មភាព') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-100">
                                                    @foreach ($filteredCourses as $course)
                                                        <tr class="hover:bg-blue-50/30 transition-colors group">
                                                            <td class="px-6 py-4">
                                                                <div class="text-sm font-bold text-gray-800">{{ $course->title_km }}</div>
                                                                <div class="text-[10px] text-gray-400 font-bold uppercase">{{ $course->title_en }}</div>
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                <div class="flex flex-wrap gap-1">
                                                                    @foreach($course->programs as $p)
                                                                        <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded-lg text-gray-600 font-bold">{{ $p->name_km }}</span>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 text-center">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 border border-blue-100 text-blue-700">
                                                                    {{ $course->credits }} {{ __('ក្រេឌីត') }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                                                <div class="flex justify-end gap-2">
                                                                    <a href="{{ route('admin.edit-course', $course->id) }}"
                                                                       class="px-3 py-1.5 text-xs font-bold text-blue-600 hover:bg-blue-50 rounded-xl transition-all border border-blue-200">
                                                                        {{ __('កែប្រែ') }}
                                                                    </a>
                                                                    <button @click="openDeleteModal('{{ route('admin.delete-course', $course->id) }}')"
                                                                            class="px-3 py-1.5 text-xs font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all border border-red-200">
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
                                @endforeach
                            </div>
                        @empty
                            {{-- Empty State --}}
                            <div class="py-24 text-center">
                                <div class="mx-auto h-20 w-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-6">
                                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.462 9.492 5 8 5c-5.072 0-8 3.844-8 7s2.928 7 8 7c1.492 0 2.832-.462 4-1.253m0-13C13.168 5.462 14.508 5 16 5c5.072 0 8 3.844 8 7s-2.928 7-8 7c-1.492 0-2.832-.462-4-1.253"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('មិនទាន់មានមុខវិជ្ជា') }}</h3>
                                <p class="text-sm text-gray-500 mb-6">{{ __('ចុចប៊ូតុងខាងក្រោមដើម្បីបន្ថែមមុខវិជ្ជាថ្មី') }}</p>
                                <a href="{{ route('admin.create-course') }}"
                                   class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-blue-200 gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/></svg>
                                    {{ __('បន្ថែមមុខវិជ្ជា') }}
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div id="delete-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>

            <div class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-gray-200">
                <div class="bg-white px-8 pt-10 pb-8 text-center">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-50 text-red-500 mb-6">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ __('តើអ្នកប្រាកដទេ?') }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ __('ទិន្នន័យនេះនឹងត្រូវលុបចេញពីប្រព័ន្ធរៀងរហូត។ អ្នកមិនអាចស្ដារវាឡើងវិញបានឡើយ។') }}</p>
                </div>
                <div class="bg-gray-50 px-8 py-6 flex gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 px-4 py-3 bg-white border border-gray-200 text-sm font-bold text-gray-600 rounded-xl hover:bg-gray-100 transition-all">
                        {{ __('បោះបង់') }}
                    </button>
                    <form id="delete-form" method="POST" action="" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-full px-4 py-3 bg-red-600 text-sm font-bold text-white rounded-xl hover:bg-red-700 shadow-lg shadow-red-200 transition-all active:scale-95">
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
