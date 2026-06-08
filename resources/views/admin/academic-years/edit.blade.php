<x-app-layout>
    <div class="min-h-screen bg-gray-100 font-sans text-gray-900">
        <div class="bg-slate-900 text-white pb-32 pt-12 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold tracking-tight text-white">កែប្រែឆ្នាំសិក្សា</h2>
                <p class="text-slate-400 mt-2 max-w-2xl text-sm leading-relaxed">កែប្រែព័ត៌មានឆ្នាំសិក្សា</p>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-10">
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.academic-years.update', $academicYear->id) }}" method="POST" class="bg-white rounded-xl shadow-xl border border-gray-100 p-6">
                @csrf @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">ឈ្មោះឆ្នាំសិក្សា *</label>
                        <input type="text" name="name" value="{{ old('name', $academicYear->name) }}" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="ឧ. 2025-2026">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">កាលបរិច្ឆេទចាប់ផ្តើម *</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $academicYear->start_date) }}" required
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">កាលបរិច្ឆេទបញ្ចប់ *</label>
                            <input type="date" name="end_date" value="{{ old('end_date', $academicYear->end_date) }}" required
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">ការពិពណ៌នា</label>
                        <textarea name="description" rows="3"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="ការពិពណ៌នាអំពីឆ្នាំសិក្សា...">{{ old('description', $academicYear->description) }}</textarea>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_current" value="1" {{ old('is_current', $academicYear->is_current) ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label class="ml-2 block text-sm text-gray-700">កំណត់ជាឆ្នាំសិក្សាបច្ចុប្បន្ន</label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <a href="{{ route('admin.academic-years.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition-colors font-bold text-sm">
                        បោះបង់
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold shadow-lg transition-all">
                        រក្សាទុក
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
