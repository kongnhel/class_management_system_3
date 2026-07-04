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

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="h-8 w-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-medium text-emerald-700">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="h-8 w-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <span class="text-sm font-medium text-red-700">{{ session('error') }}</span>
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
                                    <form action="{{ route('admin.generations.destroy', $gen->id) }}" method="POST" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបជំនាន់នេះមែនទេ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
