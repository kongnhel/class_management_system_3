<x-app-layout>
    <div class="min-h-screen bg-gray-100 font-sans text-gray-900">
        <div class="bg-slate-900 text-white pb-32 pt-12 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white">ការគ្រប់គ្រងឆ្នាំសិក្សា</h2>
                        <p class="text-slate-400 mt-2 max-w-2xl text-sm leading-relaxed">គ្រប់គ្រងឆ្នាំសិក្សា និងកំណត់ឆ្នាំសិក្សាបច្ចុប្បន្ន</p>
                    </div>
                    <a href="{{ route('admin.academic-years.create') }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-lg font-bold shadow-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>បន្ថែមថ្មី</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-10">
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase">#</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase">ឈ្មោះ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase">កាលបរិច្ឆេទចាប់ផ្តើម</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase">កាលបរិច្ឆេទបញ្ចប់</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase">ស្ថានភាព</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($academicYears as $year)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $year->id }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $year->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($year->start_date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($year->end_date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($year->is_current)
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">បច្ចុប្បន្ន</span>
                                @else
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">{{ $year->name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    @if(!$year->is_current)
                                    <form action="{{ route('admin.academic-years.set-current', $year->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-colors" title="កំណត់ជាបច្ចុប្បន្ន">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('admin.academic-years.edit', $year->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.academic-years.destroy', $year->id) }}" method="POST" class="inline" onsubmit="return confirm('តើអ្នកប្រាកដទេថាចង់លុប?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">មិនមានទិន្នន័យ</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $academicYears->links() }}</div>
        </div>
    </div>
</x-app-layout>
