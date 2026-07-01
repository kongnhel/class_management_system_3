<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.rooms.index') }}"
                       class="h-10 w-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">បន្ទប់ {{ $room->room_number }}</h1>
                        <p class="text-gray-400 text-sm mt-0.5">ព័ត៌មានលម្អិតនៃបន្ទប់</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.rooms.edit', $room->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        កែប្រែ
                    </a>
                    <button onclick="openDeleteModal({{ $room->id }}, '{{ $room->room_number }}')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-red-200 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 hover:border-red-300 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        លុប
                    </button>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left Column - QR Code --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">WiFi QR Code</h3>
                            <div class="aspect-square bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100">
                                @if($room->wifi_qr_code)
                                    <img src="{{ str_starts_with($room->wifi_qr_code, 'http') ? $room->wifi_qr_code : asset('storage/' . $room->wifi_qr_code) }}" alt="WiFi QR Code"
                                         class="w-full h-full object-contain p-4">
                                @else
                                    <div class="flex flex-col items-center justify-center text-gray-300">
                                        <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        <span class="text-sm">មិនមានរូបភាព</span>
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 text-center mt-3">ស្កេនដើម្បីភ្ជាប់ WiFi</p>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Details --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                        <div class="p-6 sm:p-8">
                            {{-- Room Type Badge --}}
                            @if($room->type_of_room)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600 mb-4">
                                    {{ $room->type_of_room }}
                                </span>
                            @endif

                            {{-- Info Rows --}}
                            <div class="divide-y divide-gray-100">
                                {{-- សមត្ថភាព --}}
                                <div class="flex items-center justify-between py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-500">សមត្ថភាព</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $room->capacity }} នាក់</span>
                                </div>

                                {{-- ទីតាំង --}}
                                <div class="flex items-center justify-between py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-500">ទីតាំង</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $room->location_of_room ?? 'មិនបានកំណត់' }}</span>
                                </div>

                                {{-- ឈ្មោះ WiFi --}}
                                <div class="flex items-center justify-between py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-500">ឈ្មោះ WiFi</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 font-mono">{{ $room->wifi_name ?? 'N/A' }}</span>
                                </div>

                                {{-- ពាក្យសម្ងាត់ WiFi --}}
                                <div class="flex items-center justify-between py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-500">ពាក្យសម្ងាត់ WiFi</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 font-mono">{{ $room->wifi_password ?? 'N/A' }}</span>
                                </div>
                            </div>

                            {{-- Created At --}}
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-400">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                បង្កើតនៅ {{ $room->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>

{{-- Delete Modal --}}
<div id="delete-modal" class="fixed inset-0 z-[100] hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl relative" onclick="event.stopPropagation()">
            <div class="text-center">
                <div class="mx-auto w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">បញ្ជាក់ការលុប</h3>
                <p class="text-gray-500 text-sm">តើអ្នកប្រាកដថាចង់លុបបន្ទប់ <strong id="delete-room-name" class="text-gray-900"></strong> មែនទេ?</p>
            </div>
            <div class="flex gap-3 mt-8">
                <button onclick="closeDeleteModal()"
                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition text-sm">
                    បោះបង់
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition text-sm shadow-lg shadow-red-200">
                        លុបចេញ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(id, name) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/rooms/${id}`;
        document.getElementById('delete-room-name').textContent = name;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
</script>
