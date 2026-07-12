<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">គ្រប់គ្រងបន្ទប់</h1>
                        <p class="text-gray-500 text-sm mt-0.5">គ្រប់គ្រង និងតាមដានបញ្ជីបន្ទប់ក្នុងប្រព័ន្ធ</p>
                    </div>
                </div>
                <a href="{{ route('admin.rooms.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-200 hover:shadow-xl hover:-translate-y-0.5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2.5">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    បង្កើតថ្មី
                </a>
            </div>

            {{-- Search & Filters --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" action="{{ route('admin.rooms.index') }}">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ $search }}" placeholder="ស្វែងរកបន្ទប់..."
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl transition text-sm">
                                ស្វែងរក
                            </button>
                            @if($search)
                                <a href="{{ route('admin.rooms.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition text-sm">
                                    សម្អាត
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            @if($rooms->isEmpty())
                {{-- Empty State --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12">
                    <div class="text-center max-w-sm mx-auto">
                        <div class="h-20 w-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $search ? 'រកមិនឃើញលទ្ធផល' : 'មិនទាន់មានបន្ទប់' }}</h3>
                        <p class="text-gray-500 text-sm mb-6">
                            @if($search)
                                រកមិនឃើញបន្ទប់ដែលត្រូវនឹង "{{ $search }}" ។ សូមព្យាយាមម្តងទៀត។
                            @else
                                ចុចប៊ូតុងខាងក្រោមដើម្បីបង្កើតបន្ទប់ថ្មី។
                            @endif
                        </p>
                        @if(!$search)
                            <a href="{{ route('admin.rooms.create') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-200">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2.5">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                បង្កើតបន្ទប់ថ្មី
                            </a>
                        @endif
                    </div>
                </div>
            @else
                {{-- Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">សរុបបន្ទប់</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $rooms->total() }}</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">សមត្ថភាពផ្ទុកសរុប</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($rooms->sum('capacity')) }}</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">ទំព័របច្ចុប្បន្ន</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $rooms->currentPage() }}/{{ $rooms->lastPage() }}</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">ក្នុងទំព័រនេះ</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $rooms->count() }}</p>
                    </div>
                </div>

                {{-- Desktop Table --}}
                <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">បន្ទប់</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ទីតាំង</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ប្រភេទ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">សមត្ថភាពផ្ទុក</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">សកម្មភាព</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($rooms as $room)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                            @if($room->wifi_qr_code)
                                                <img src="{{ $room->wifi_qr_code }}" alt="" class="h-10 w-10 rounded-xl object-cover">
                                            @else
                                                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <span class="font-bold text-gray-900">{{ $room->room_number }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $room->location_of_room ?? '---' }}</td>
                                <td class="px-6 py-4">
                                    @if($room->type_of_room)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-purple-50 text-purple-700">
                                            {{ $room->type_of_room }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">---</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-green-50 text-green-700">
                                        {{ $room->capacity }} នាក់
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.rooms.show', $room->id) }}"
                                           class="px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition">
                                            មើល
                                        </a>
                                        <a href="{{ route('admin.rooms.edit', $room->id) }}"
                                           class="px-3 py-1.5 text-sm font-medium text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                            កែប្រែ
                                        </a>
                                        <button onclick="openDeleteModal({{ $room->id }}, '{{ $room->room_number }}')"
                                                class="px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">
                                            លុប
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden space-y-3">
                    @foreach($rooms as $room)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                    @if($room->wifi_qr_code)
                                        <img src="{{ $room->wifi_qr_code }}" alt="" class="h-12 w-12 rounded-xl object-cover">
                                    @else
                                        <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $room->room_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $room->location_of_room ?? '---' }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-green-50 text-green-700">
                                {{ $room->capacity }} នាក់
                            </span>
                        </div>
                        @if($room->type_of_room)
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-purple-50 text-purple-700">
                                {{ $room->type_of_room }}
                            </span>
                        </div>
                        @endif
                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                            <a href="{{ route('admin.rooms.show', $room->id) }}"
                               class="px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition">
                                មើល
                            </a>
                            <a href="{{ route('admin.rooms.edit', $room->id) }}"
                               class="px-3 py-1.5 text-sm font-medium text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                កែប្រែ
                            </a>
                            <button onclick="openDeleteModal({{ $room->id }}, '{{ $room->room_number }}')"
                                    class="px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">
                                លុប
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($rooms->hasPages())
                <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            បង្ហាញ {{ $rooms->firstItem() }}-{{ $rooms->lastItem() }} ក្នុងចំណោម {{ $rooms->total() }} លទ្ធផល
                        </p>
                        <div class="flex gap-1">
                            @if($rooms->onFirstPage())
                                <span class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">&laquo;</span>
                            @else
                                <a href="{{ $rooms->previousPageUrl() }}" class="px-3 py-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">&laquo;</a>
                            @endif

                            @foreach($rooms->getUrlRange(max(1, $rooms->currentPage() - 2), min($rooms->lastPage(), $rooms->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url }}"
                                   class="px-3 py-1.5 text-sm font-medium rounded-lg transition {{ $page == $rooms->currentPage() ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 bg-white border border-gray-200 hover:bg-gray-50' }}">
                                    {{ $page }}
                                </a>
                            @endforeach

                            @if($rooms->currentPage() >= $rooms->lastPage())
                                <span class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">&raquo;</span>
                            @else
                                <a href="{{ $rooms->nextPageUrl() }}" class="px-3 py-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">&raquo;</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @endif

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
