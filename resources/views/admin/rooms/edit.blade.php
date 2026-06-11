<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.rooms.index') }}"
                   class="h-10 w-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">កែប្រែបន្ទប់</h1>
                        <p class="text-gray-500 text-sm mt-0.5">បំពេញព័ត៌មានខាងក្រោមដើម្បីធ្វើបច្ចុប្បន្នភាពបន្ទប់</p>
                    </div>
                </div>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="h-4 w-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-red-800 text-sm">មានបញ្ហា!</p>
                            <ul class="mt-1 text-sm text-red-600 list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- លេខបន្ទប់ --}}
                            <div>
                                <label for="room_number" class="block text-sm font-bold text-gray-700 mb-1.5">លេខបន្ទប់ <span class="text-red-500">*</span></label>
                                <input type="text" name="room_number" id="room_number" value="{{ old('room_number', $room->room_number) }}" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                                @error('room_number')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- សមត្ថភាព --}}
                            <div>
                                <label for="capacity" class="block text-sm font-bold text-gray-700 mb-1.5">សមត្ថភាព <span class="text-red-500">*</span></label>
                                <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $room->capacity) }}" required min="1"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                                @error('capacity')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- ទីតាំងបន្ទប់ --}}
                            <div class="sm:col-span-2">
                                <label for="location_of_room" class="block text-sm font-bold text-gray-700 mb-1.5">ទីតាំងបន្ទប់</label>
                                <input type="text" name="location_of_room" id="location_of_room" value="{{ old('location_of_room', $room->location_of_room) }}"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                                @error('location_of_room')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- ប្រភេទបន្ទប់ --}}
                            <div class="sm:col-span-2">
                                <label for="type_of_room" class="block text-sm font-bold text-gray-700 mb-1.5">ប្រភេទបន្ទប់</label>
                                <select name="type_of_room" id="type_of_room"
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition bg-white">
                                    <option value="">ជ្រើសរើសប្រភេទបន្ទប់</option>
                                    @php $types = ['បន្ទប់រៀនធម្មតា', 'បន្ទប់ពិសោធន៍', 'បន្ទប់កុំព្យូទ័រ', 'បន្ទប់សម្ភាសន៍', 'សាលប្រជុំ', 'ផ្សេងទៀត']; @endphp
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ old('type_of_room', $room->type_of_room) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('type_of_room')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- WiFi QR Code --}}
                        <div x-data="{ imagePreview: null }">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">រូបភាព WiFi QR Code</label>
                            <div class="flex flex-col sm:flex-row items-start gap-4 p-4 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                                <div class="relative w-36 h-36 flex-shrink-0">
                                    <template x-if="imagePreview">
                                        <img :src="imagePreview" class="w-full h-full object-contain rounded-xl border border-gray-200 bg-white shadow-sm">
                                    </template>
                                    <template x-if="!imagePreview">
                                        @if($room->wifi_qr_code)
                                            <img src="{{ asset('storage/' . $room->wifi_qr_code) }}" class="w-full h-full object-contain rounded-xl border border-gray-200 bg-white shadow-sm">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 rounded-xl text-gray-400">
                                                <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-xs">មិនមានរូបភាព</span>
                                            </div>
                                        @endif
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 mb-3">ជ្រើសរើសរូបភាពថ្មីដើម្បីជំនួសរូបភាពចាស់</p>
                                    <label for="wifi_qr_code" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl font-bold text-sm text-gray-700 hover:bg-gray-50 transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        ប្តូររូបភាព
                                        <input id="wifi_qr_code" name="wifi_qr_code" type="file" class="sr-only" accept="image/*"
                                               @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }">
                                    </label>
                                    @error('wifi_qr_code')
                                        <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('admin.rooms.index') }}"
                               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition text-sm">
                                បោះបង់
                            </a>
                            <button type="submit"
                                    class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition shadow-lg shadow-green-200 hover:shadow-xl hover:-translate-y-0.5 text-sm">
                                រក្សាទុកការផ្លាស់ប្តូរ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
