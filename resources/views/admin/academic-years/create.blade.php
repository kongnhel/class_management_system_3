<x-app-layout>
    <div class="bg-gray-50 min-h-screen font-sans">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('admin.academic-years.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 transition-colors mb-4">
                    <i class="fas fa-arrow-left"></i>
                    ត្រលប់ក្រោយ
                </a>
                <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl shadow-sm">
                        <i class="fas fa-plus-circle text-xl"></i>
                    </span>
                    បង្កើតឆ្នាំសិក្សាថ្មី
                </h2>
                <p class="text-gray-500 mt-2 ml-14">បំពេញព័ត៌មានសម្រាប់ឆ្នាំសិក្សាថ្មី</p>
            </div>

            {{-- Error Messages --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <div>
                        <p class="font-bold text-red-800 text-sm">មានកំហុសក្នុងការបំពេញទម្រង់</p>
                        <ul class="mt-2 space-y-1">
                            @foreach($errors->all() as $error)
                            <li class="text-sm text-red-600 flex items-center gap-2">
                                <i class="fas fa-times text-[10px]"></i>
                                {{ $error }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-400"></i>
                        ព័ត៌មានឆ្នាំសិក្សា
                    </h3>
                </div>

                <form action="{{ route('admin.academic-years.store') }}" method="POST" class="p-6">
                    @csrf
                    <div class="space-y-6">
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <span class="text-red-500">*</span> ឈ្មោះឆ្នាំសិក្សា
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-gray-300"></i>
                                </div>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="block w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 text-sm transition-all"
                                    placeholder="ឧ. 2025-2026">
                            </div>
                        </div>

                        {{-- Date Range --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> កាលបរិច្ឆេទចាប់ផ្តើម
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-day text-gray-300"></i>
                                    </div>
                                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                                        class="block w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 text-sm transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <span class="text-red-500">*</span> កាលបរិច្ឆេទបញ្ចប់
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-check text-gray-300"></i>
                                    </div>
                                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                                        class="block w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 text-sm transition-all">
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                ការពិពណ៌នា
                            </label>
                            <div class="relative">
                                <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                    <i class="fas fa-align-left text-gray-300"></i>
                                </div>
                                <textarea name="description" rows="3"
                                    class="block w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 text-sm transition-all resize-none"
                                    placeholder="ការពិពណ៌នាអំពីឆ្នាំសិក្សា...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        {{-- Current Year Toggle --}}
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_current" value="1" {{ old('is_current') ? 'checked' : '' }}
                                    class="h-5 w-5 text-emerald-600 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 transition-all">
                                <div>
                                    <span class="block text-sm font-bold text-gray-700">កំណត់ជាឆ្នាំសិក្សាបច្ចុប្បន្ន</span>
                                    <span class="block text-xs text-gray-400 mt-0.5">ឆ្នាំសិក្សាមួយអាចកំណត់ជាបច្ចុប្បន្នបាន</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.academic-years.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                            <i class="fas fa-times"></i>
                            បោះបង់
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-100 hover:shadow-emerald-200 transition-all active:scale-95">
                            <i class="fas fa-save"></i>
                            រក្សាទុក
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
