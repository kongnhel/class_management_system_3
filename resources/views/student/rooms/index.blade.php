<x-app-layout>
    <div class="py-10 bg-[#f8fafc] min-h-screen font-['Battambang']">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header section with Glass Effect --}}
            <div class="mb-12">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <h2 class="text-4xl font-black text-slate-900 leading-tight flex items-center gap-4">
                            {{ __('ព័ត៌មានបន្ទប់សិក្សា') }} 
                            <span class="p-2.5 bg-emerald-100 text-emerald-600 rounded-2xl">
                                <i class="fas fa-door-open text-xl"></i>
                            </span>
                        </h2>
                        <p class="mt-2 text-slate-500 font-medium">{{ __('ស្វែងរកបន្ទប់សិក្សា និងការតភ្ជាប់ WiFi របស់សាលា') }}</p>
                    </div>

                    <div class="relative w-full md:w-96 group">
                        <input type="text" id="searchInput" 
                               placeholder="{{ __('ស្វែងរកលេខបន្ទប់...') }}" 
                               class="w-full pl-12 pr-12 py-4 bg-white border border-slate-100 rounded-[1.5rem] focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 shadow-xl shadow-slate-200/50 transition-all placeholder-slate-300 font-bold text-slate-700">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-emerald-500 transition-colors">
                            <i class="fas fa-search text-lg"></i>
                        </div>
                        <button type="button" id="clearRoomSearch" aria-label="{{ __('សម្អាតការស្វែងរក') }}"
                                class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-600 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if ($rooms->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 bg-white rounded-[3rem] border border-dashed border-slate-200 shadow-sm">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-box-open text-3xl text-slate-200"></i>
                    </div>
                    <p class="text-xl font-black text-slate-800">{{ __('មិនទាន់មានទិន្នន័យបន្ទប់') }}</p>
                </div>
            @else
                <div id="roomList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($rooms as $room)
                        <div class="room-card group bg-white rounded-[2.5rem] border border-slate-100 p-2 transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/60 hover:-translate-y-1 relative flex flex-col">
                            
                            <div class="p-6 flex flex-col h-full">
                                {{-- Card Top --}}
                                <div class="flex justify-between items-start mb-6">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-lg">
                                                {{ $room->type_of_room ?? __('Lecture') }}
                                            </span>
                                        </div>
                                        <h4 class="text-4xl font-black text-slate-800 room-number-text">
                                            {{ $room->room_number }}
                                        </h4>
                                    </div>
                                    <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300 group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-inner">
                                        <i class="fas fa-location-arrow"></i>
                                    </div>
                                </div>

                                {{-- WiFi Section (Modernized) --}}
                                <div class="mb-6 relative flex flex-col items-center p-6 bg-slate-50 rounded-[2rem] border border-slate-100 group-hover:bg-white transition-colors">
                                    @if($room->wifi_qr_code)
                                        <div class="relative bg-white p-3 rounded-2xl shadow-lg border border-slate-50">
                                            <img src="{{ $room->wifi_qr_code}}" alt="WiFi QR" class="w-32 h-32 md:w-40 md:h-40 object-cover rounded-lg">
                                        </div>
                                        <div class="mt-4 flex items-center gap-2 px-4 py-1.5 bg-white text-emerald-600 border border-emerald-100 rounded-full shadow-sm">
                                            <i class="fas fa-wifi text-xs"></i>
                                            <span class="text-[10px] font-black uppercase tracking-tighter">{{ __('Scan WiFi') }}</span>
                                        </div>
                                    @else
                                        <div class="w-32 h-32 md:w-40 md:h-40 flex flex-col items-center justify-center text-slate-200 border-2 border-dashed border-slate-200 rounded-2xl">
                                            <i class="fas fa-qrcode text-3xl mb-2"></i>
                                            <span class="text-[9px] font-bold">{{ __('No QR') }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Details --}}
                                <div class="mt-auto space-y-3">
                                    <div class="flex items-center justify-between p-3 bg-slate-50/50 rounded-xl">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-users text-slate-400 text-xs"></i>
                                            <span class="text-xs font-bold text-slate-500 uppercase">{{ __('សមត្ថភាព') }}</span>
                                        </div>
                                        <span class="text-sm font-black text-slate-800">{{ $room->capacity }} {{ __('នាក់') }}</span>
                                    </div>

                                    <div class="p-3">
                                        <span class="text-[10px] font-black text-slate-300 uppercase block mb-1">{{ __('ទីតាំង') }}</span>
                                        <div class="flex items-start gap-2 text-xs text-slate-600 font-bold leading-relaxed">
                                            <i class="fas fa-map-pin text-rose-400 mt-0.5"></i>
                                            <span>{{ $room->location_of_room ?? __('មិនមានព័ត៌មានទីតាំង') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearRoomSearch');
        const roomList = document.getElementById('roomList');

        if (!roomList || !searchInput) return;

        const roomCards = Array.from(roomList.querySelectorAll('.room-card'));

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            clearSearch.classList.toggle('hidden', query === '');
            let found = false;

            roomCards.forEach(card => {
                const roomNumber = card.querySelector('.room-number-text').textContent.toLowerCase();
                const cardText = card.textContent.toLowerCase();

                if (roomNumber.includes(query) || cardText.includes(query)) {
                    card.style.display = 'flex';
                    found = true;
                } else {
                    card.style.display = 'none';
                }
            });

            // Handle "No Results"
            const existingMsg = document.getElementById('noResultsMessage');
            if (existingMsg) existingMsg.remove();

            if (!found && roomCards.length > 0) {
                const messageDiv = document.createElement('div');
                messageDiv.id = 'noResultsMessage';
                messageDiv.className = 'col-span-full flex flex-col items-center justify-center py-20 text-slate-400 bg-white rounded-[3rem] border border-dashed border-slate-200';
                messageDiv.innerHTML = `
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-200">
                        <i class="fas fa-search text-2xl"></i>
                    </div>
                    <p class="text-lg font-black text-slate-800">{{ __('រកមិនឃើញលេខបន្ទប់ដែលអ្នកចង់រកទេ') }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ __('សូមពិនិត្យមើលលេខបន្ទប់ឡើងវិញ') }}</p>
                `;
                roomList.appendChild(messageDiv);
            }
        });

        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input', { bubbles: true }));
            searchInput.focus();
        });
    });
</script>
