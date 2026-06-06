<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            {{-- Toast Notification --}}
            @if (session('success') || session('error'))
            <div 
                x-data="{ show: false, progress: 100, startTimer() { this.show = true; let i = setInterval(() => { this.progress -= 1; if(this.progress <= 0){ this.show=false; clearInterval(i); } }, 50); } }" 
                x-init="startTimer()"
                x-show="show" 
                class="fixed top-6 right-6 z-[9999] w-full max-w-sm">
                <div class="relative overflow-hidden bg-white/80 backdrop-blur-xl border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-2xl p-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            @if(session('success'))
                                <div class="h-10 w-10 rounded-xl bg-green-500/10 text-green-600 flex items-center justify-center">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            @else
                                <div class="h-10 w-10 rounded-xl bg-red-500/10 text-red-600 flex items-center justify-center">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-900">{{ session('success') ? __('ជោគជ័យ!') : __('បរាជ័យ!') }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ session('success') ?? session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 bg-gray-100 w-full">
                        <div class="h-full {{ session('success') ? 'bg-green-500' : 'bg-red-500' }} transition-all duration-75" :style="`width: ${progress}%`"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Main Container with Single x-data --}}
            <div x-data="{ viewMode: 'grid' }">

                {{-- Header + Toggle Buttons --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                    <div>
                        <h2 class="text-4xl font-black text-gray-900 flex items-center gap-4">
                            <div class="h-14 w-14 bg-green-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-green-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            {{ __('គ្រប់គ្រងបន្ទប់') }}
                        </h2>
                        <p class="text-gray-500 mt-3 ml-2 text-lg">{{ __('គ្រប់គ្រង និងតាមដានបញ្ជីបន្ទប់ក្នុងប្រព័ន្ធ') }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- View Toggle -->
                        <div class="bg-white p-1.5 rounded-xl border border-gray-200 shadow-sm flex">
                            <button @click="viewMode = 'grid'" 
                                    :class="viewMode === 'grid' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                    class="p-3 rounded-lg transition-all">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </button>
                            <button @click="viewMode = 'table'" 
                                    :class="viewMode === 'table' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                    class="p-3 rounded-lg transition-all">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>

                        <a href="{{ route('admin.rooms.create') }}" 
                           class="inline-flex items-center gap-2.5 px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ __('បង្កើតថ្មី') }}</span>
                        </a>
                    </div>
                </div>

                {{-- GRID VIEW --}}
                <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($rooms as $room)
                        <div class="group bg-white rounded-[2rem] p-8 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 hover:-translate-y-2 relative">
                            <div class="absolute top-6 right-6 flex gap-3 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                <a href="{{ route('admin.rooms.edit', $room->id) }}" class="p-3 bg-white border border-gray-100 text-blue-600 rounded-xl hover:bg-blue-50">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <button onclick="openDeleteModal({{ $room->id }})" class="p-3 bg-white border border-gray-100 text-red-600 rounded-xl hover:bg-red-50">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            <div class="flex flex-col items-center text-center">
                                <div class="w-32 h-32 rounded-3xl bg-gray-50 flex items-center justify-center mb-6 group-hover:scale-105 transition-transform border border-gray-100">
                                    @if($room->wifi_qr_code)
                                        <img src="{{ $room->wifi_qr_code }}" alt="QR" class="w-full h-full object-cover rounded-3xl p-2">
                                    @else
                                        <span class="text-6xl">🚪</span>
                                    @endif
                                </div>
                                <h3 class="text-2xl font-black text-gray-900">{{ $room->room_number }}</h3>
                                <p class="text-gray-500 mt-1">{{ $room->location_of_room ?? '---' }}</p>

                                <div class="w-full pt-6 border-t mt-8 flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ __('សមត្ថភាព') }}</span>
                                    <span class="px-5 py-2 bg-green-50 text-green-700 font-bold rounded-2xl text-sm flex items-center gap-2">
                                        {{ $room->capacity }} នាក់
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- TABLE VIEW --}}
                <div x-show="viewMode === 'table'" x-cloak class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 text-xs uppercase font-bold text-gray-500 border-b">
                                    <th class="px-8 py-6 text-left">{{ __('បន្ទប់') }}</th>
                                    <th class="px-8 py-6 text-left">{{ __('ទីតាំង') }}</th>
                                    <th class="px-8 py-6 text-center">{{ __('សមត្ថភាព') }}</th>
                                    <th class="px-8 py-6 text-right">{{ __('សកម្មភាព') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($rooms as $room)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center overflow-hidden">
                                                @if($room->wifi_qr_code)
                                                    <img src="{{ $room->wifi_qr_code }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-3xl">🚪</span>
                                                @endif
                                            </div>
                                            <span class="font-bold text-xl">{{ $room->room_number }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-gray-600">{{ $room->location_of_room ?? '---' }}</td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="inline-block px-6 py-2 bg-green-100 text-green-700 font-bold rounded-2xl">
                                            {{ $room->capacity }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <a href="{{ route('admin.rooms.edit', $room->id) }}" class="text-blue-600 hover:text-blue-700 mr-6">{{ __('កែប្រែ') }}</a>
                                        <button onclick="openDeleteModal({{ $room->id }})" class="text-red-600 hover:text-red-700">{{ __('លុប') }}</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div> <!-- End of main x-data -->

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $rooms->links() }}
            </div>

        </div>
    </div>
</x-app-layout>

{{-- Delete Modal --}}
<div id="delete-modal" class="fixed inset-0 z-[100] hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-black mb-2">{{ __('បញ្ជាក់ការលុប') }}</h3>
                <p class="text-gray-500 mb-8">តើអ្នកប្រាកដថាចង់លុបបន្ទប់នេះមែនទេ?</p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 py-4 bg-gray-100 hover:bg-gray-200 rounded-2xl font-bold">{{ __('បោះបង់') }}</button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-bold">{{ __('លុបចេញ') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(id) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/rooms/${id}`;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
</script>