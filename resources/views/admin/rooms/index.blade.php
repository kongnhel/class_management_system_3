<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ viewMode: localStorage.getItem('room_view') || 'table' }" x-init="$watch('viewMode', v => localStorage.setItem('room_view', v))">

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
                <div class="flex items-center gap-3">
                    {{-- View Toggle --}}
                    <div class="inline-flex rounded-xl bg-white border border-gray-200 p-1 shadow-sm">
                        <button @click="viewMode = 'card'"
                                :class="viewMode === 'card' ? 'bg-emerald-50 text-emerald-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                class="p-2 rounded-lg transition-all duration-200" title="ទម្រង់ប័ណ្ណ">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                        </button>
                        <button @click="viewMode = 'table'"
                                :class="viewMode === 'table' ? 'bg-emerald-50 text-emerald-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                class="p-2 rounded-lg transition-all duration-200" title="ទម្រង់តារាង">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 0v.375" />
                            </svg>
                        </button>
                    </div>
                    <a href="{{ route('admin.rooms.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-200 hover:shadow-xl hover:-translate-y-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2.5">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        បង្កើតថ្មី
                    </a>
                </div>
            </div>

            {{-- Search & Filters --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" action="{{ route('admin.rooms.index') }}" data-admin-realtime-filter>
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

            <div data-admin-results>
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

                {{-- Table View --}}
                <div x-show="viewMode === 'table'" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
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

                {{-- Card View --}}
                <div x-show="viewMode === 'card'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($rooms as $room)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        @if($room->wifi_qr_code)
                            <div class="flex justify-center mb-3">
                                <div class="h-28 w-28 rounded-xl bg-white border border-gray-200 flex items-center justify-center shadow-sm overflow-hidden">
                                    <img src="{{ $room->wifi_qr_code }}" alt="WiFi QR" class="h-26 w-26 object-contain">
                                </div>
                            </div>
                        @endif
                        <div class="flex items-start gap-3 mb-4">
                            <div class="h-12 w-12 rounded-xl bg-emerald-500 flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-200">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $room->room_number }}</h3>
                                <p class="text-sm text-gray-500 truncate">{{ $room->location_of_room ?? '---' }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>{{ $room->type_of_room ?? 'បន្ទប់ធម្មតា' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                </svg>
                                <span>{{ $room->capacity }} នាក់</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                                </svg>
                                <span>{{ $room->location_of_room ?? '---' }}</span>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-100 flex items-center gap-2">
                            <a href="{{ route('admin.rooms.edit', $room->id) }}"
                               class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                                កែប្រែ
                            </a>
                            <button onclick="openDeleteModal({{ $room->id }}, '{{ $room->room_number }}')"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
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
