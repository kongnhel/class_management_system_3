<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between px-4 md:px-6 lg:px-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">គ្រប់គ្រងជំនាន់</h2>
                <p class="mt-1 text-sm text-gray-400">បង្កើត និងគ្រប់គ្រងជំនាន់និស្សិត</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Toast --}}
            @if(session('success') || session('error'))
            <div x-data="{ show: false, progress: 100, startTimer() { this.show = true; let interval = setInterval(() => { this.progress -= 1; if (this.progress <= 0) { this.show = false; clearInterval(interval); } }, 30); } }" x-init="startTimer()" x-show="show" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="translate-y-12 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed top-6 right-6 z-[9999] w-full max-w-sm">
                <div class="relative overflow-hidden bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-2xl p-4">
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
                            <p class="text-sm font-bold text-gray-900">{{ session('success') ? 'ជោគជ័យ!' : 'បរាជ័យ!' }}</p>
                            <p class="mt-1 text-sm text-gray-600">{{ session('success') ?? session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 bg-gray-100 w-full">
                        <div class="h-full transition-all duration-75 ease-linear {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}" :style="`width: ${progress}%`"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Create Form --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">បង្កើតជំនាន់ថ្មី</h3>
                <form action="{{ route('admin.generations.store') }}" method="POST" class="flex items-end gap-4">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">លេខជំនាន់</label>
                        <input type="number" name="name" required min="1" max="99"
                               placeholder="ឧ. 16, 17, 18"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition shadow-sm">
                        + បង្កើត
                    </button>
                </form>
            </div>

            {{-- Generations List --}}
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">ជំនាន់ទាំងអស់ <span class="text-gray-300">· {{ $generations->count() }}</span></h3>
                </div>

                @if($generations->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-gray-400 text-sm">មិនទាន់មានជំនាន់ណាមួយនៅឡើយ</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($generations as $gen)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                                        <span class="text-sm font-bold text-emerald-600">G{{ $gen->name }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">ជំនាន់ទី{{ $gen->name }}</p>
                                        <p class="text-xs text-gray-400">ចូលរៀនឆ្នាំ {{ $gen->join_year }} · {{ $gen->students_count }} និស្សិត</p>
                                    </div>
                                </div>
                                @if($gen->students_count === 0)
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditModal({{ $gen->id }}, {{ $gen->name }})" class="text-emerald-500 hover:text-emerald-700 transition" title="កែប្រែ">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button type="button" onclick="openDeleteModal({{ $gen->id }}, '{{ $gen->name }}')" class="text-red-400 hover:text-red-600 transition" title="លុប">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Edit Modal (plain JS, no Alpine) --}}
    <div id="edit-modal" class="hidden fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
            <div id="edit-backdrop" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full">
                <div class="bg-white px-8 pt-10 pb-6">
                    <div class="flex flex-col items-center text-center">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-2xl bg-emerald-50 mb-5 border border-emerald-100">
                            <svg class="h-7 w-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">កែប្រែជំនាន់</h3>
                        <form id="edit-generation-form" method="POST" class="w-full text-left">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">លេខជំនាន់</label>
                                <input type="number" id="edit-name-input" name="name" required min="1" max="99"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="bg-gray-50/50 px-8 py-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="w-full sm:w-auto inline-flex justify-center rounded-xl border-2 border-gray-200 px-6 py-3 bg-white text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all">បោះបង់</button>
                    <button type="submit" form="edit-generation-form" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl border border-transparent px-6 py-3 bg-emerald-600 text-sm font-bold text-white hover:bg-emerald-700 shadow-lg transition-all">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        រក្សាទុក
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal (plain JS, no Alpine) --}}
    <div id="delete-modal" class="hidden fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
            <div id="delete-backdrop" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <div class="bg-white px-8 pt-10 pb-6">
                    <div class="flex flex-col items-center text-center">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-20 w-20 rounded-3xl bg-rose-50 mb-6 border border-rose-100">
                            <svg class="h-9 w-9 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-4">បញ្ជាក់ការលុប</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            តើអ្នកពិតជាចង់លុបជំនាន់ទី<span id="delete-gen-name" class="font-bold text-rose-600"></span> នេះមែនទេ? សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានឡើយ។
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50/50 px-8 py-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="w-full sm:w-auto inline-flex justify-center rounded-xl border-2 border-gray-200 px-6 py-3 bg-white text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all">បោះបង់</button>
                    <form id="delete-generation-form" method="POST" class="w-full sm:w-auto">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl border border-transparent px-6 py-3 bg-gradient-to-r from-rose-500 to-red-600 text-sm font-bold text-white hover:from-rose-600 hover:to-red-700 shadow-lg transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            យល់ព្រមលុប
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, name) {
            document.getElementById('edit-generation-form').action = '/admin/generations/' + id;
            document.getElementById('edit-name-input').value = name;
            document.getElementById('edit-modal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        function openDeleteModal(id, name) {
            document.getElementById('delete-generation-form').action = '/admin/generations/' + id;
            document.getElementById('delete-gen-name').textContent = name;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }

        document.getElementById('edit-backdrop').addEventListener('click', closeEditModal);
        document.getElementById('delete-backdrop').addEventListener('click', closeDeleteModal);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout>
