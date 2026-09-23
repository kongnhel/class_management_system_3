<div class="space-y-8">
    {{-- 1. GRID VIEW --}}
    <div x-show="viewMode === 'grid'" id="grid-wrapper" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse ($rooms as $room)
            <div class="room-card group bg-white rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col overflow-hidden">
                {{-- Header --}}
                <div class="p-6 bg-gray-50/50 flex items-center justify-between border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center text-white font-black">
                            {{ substr($room->room_number, 0, 1) }}
                        </div>
                        <h4 class="text-xl font-bold text-gray-900">{{ $room->room_number }}</h4>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID: #{{ $room->id }}</span>
                </div>

                {{-- QR Code Display --}}
                <div class="p-8 flex flex-col items-center justify-center bg-white">
                    @if($room->wifi_qr_code)
                        <div class="relative group/qr">
                            <div class="absolute -inset-4 bg-green-50 rounded-[2rem] scale-95 opacity-0 group-hover/qr:scale-100 group-hover/qr:opacity-100 transition-all duration-500"></div>
                            <div class="relative p-4 bg-white rounded-2xl border-2 border-dashed border-green-200 shadow-inner">
                                {{-- ប្តូរ Path ឱ្យត្រូវជាមួយ Controller: storage/rooms/qrcodes/... --}}
                                <img src="{{ asset('storage/' . $room->wifi_qr_code) }}" 
                                     class="w-40 h-40 md:w-48 md:h-48 object-cover rounded-lg transform transition-transform group-hover/qr:scale-105 duration-500"
                                     onerror="this.src='https://placehold.co/200?text=No+Image'">
                            </div>
                            <p class="text-center mt-4 text-xs font-bold text-green-600 uppercase tracking-tighter opacity-70">{{ __('Scan for WiFi') }}</p>
                        </div>
                    @else
                        <div class="w-40 h-40 md:w-48 md:h-48 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-10 h-10 mx-auto text-gray-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 11v1m5-11v1m0 11v1M7 4v1m0 11v1m4-4h.01M9 16h.01M11 16h.01M13 16h.01M15 16h.01M12 12h.01M10 12h.01M14 12h.01M12 8h.01M10 8h.01M14 8h.01"></path></svg>
                                <span class="text-gray-300 text-sm font-medium">{{ __('no_qr_code') }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Info Section (ដក Dean ចេញដើម្បីការពារ Error RelationNotFound) --}}
                <div class="px-8 py-6 space-y-4 border-t border-gray-50">
                    <div class="flex items-center justify-between group/line">
                        <span class="text-sm font-medium text-gray-400">{{ __('capacity') }}</span>
                        <div class="flex-grow mx-4 border-b border-dashed border-gray-200 group-hover/line:border-green-200 transition-colors"></div>
                        <span class="text-sm font-bold text-gray-900">{{ $room->capacity }} {{ __('students_2') }}</span>
                    </div>
                    <div class="flex items-center justify-between group/line">
                        <span class="text-sm font-medium text-gray-400">{{ __('location') }}</span>
                        <div class="flex-grow mx-4 border-b border-dashed border-gray-200 group-hover/line:border-green-200 transition-colors"></div>
                        <span class="text-sm font-bold text-gray-900">{{ $room->location_of_room ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between group/line">
                        <span class="text-sm font-medium text-gray-400">{{ __('type') }}</span>
                        <div class="flex-grow mx-4 border-b border-dashed border-gray-200 group-hover/line:border-green-200 transition-colors"></div>
                        <span class="text-sm font-bold text-gray-900 text-right">{{ $room->type_of_room ?? 'N/A' }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-auto p-4 bg-gray-50/80 border-t border-gray-100 flex gap-3">
                    <a href="{{ route('admin.rooms.edit', $room->id) }}" class="flex-1 flex items-center justify-center gap-2 py-3 bg-white hover:bg-green-600 hover:text-white text-green-600 font-bold rounded-xl border border-green-100 shadow-sm transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        <span class="text-sm">{{ __('edit_2') }}</span>
                    </a>
                    <button @click="openDeleteModal('{{ $room->id }}')" class="px-4 py-3 bg-white hover:bg-red-50 text-red-400 hover:text-red-600 rounded-xl border border-gray-100 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 bg-white rounded-[2.5rem] border border-dashed border-gray-200">
                <p class="text-gray-400 font-medium">{{ __('no_room_data_available') }}</p>
            </div>
        @endforelse
    </div>

    {{-- 2. TABLE VIEW --}}
    <div x-show="viewMode === 'table'" x-cloak x-transition.opacity.duration.400ms class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/50">
                <tr>
                    <th class="px-8 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em]">{{ __('room') }}</th>
                    <th class="px-8 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em]">{{ __('capacity') }}</th>
                    <th class="px-8 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] hidden md:table-cell">{{ __('location') }}</th>
                    <th class="px-8 py-5 text-center text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em]">{{ __('WiFi') }}</th>
                    <th class="px-8 py-5 text-right text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em]">{{ __('actions_2') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ($rooms as $room)
                    <tr class="hover:bg-green-50/30 transition-colors group">
                        <td class="px-8 py-5 whitespace-nowrap">
                            <span class="font-black text-gray-900 group-hover:text-green-600">{{ $room->room_number }}</span>
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap">
                            <span class="text-gray-600 font-medium">{{ $room->capacity }} {{ __('students_2') }}</span>
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap hidden md:table-cell">
                            <span class="text-gray-500 text-sm italic">{{ $room->location_of_room ?? 'N/A' }}</span>
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap">
                            <div class="flex justify-center">
                                @if($room->wifi_qr_code)
                                    <img src="{{ asset('storage/' . $room->wifi_qr_code) }}" 
                                         class="h-10 w-10 rounded-lg shadow-sm border p-0.5 hover:scale-150 transition-transform cursor-zoom-in bg-white"
                                         onerror="this.src='https://placehold.co/50?text=QR'">
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.rooms.edit', $room->id) }}" class="text-green-600 hover:text-green-700 p-2 hover:bg-green-100 rounded-lg transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </a>
                                <button @click="openDeleteModal('{{ $room->id }}')" class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $rooms->links() }}
    </div>
</div>