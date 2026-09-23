<x-app-layout>
    <div class="bg-gray-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <span class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl shadow-sm">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </span>
                        {{ __('academic_year_management') }}
                    </h2>
                    <p class="text-gray-500 mt-2 ml-14">{{ __('manage_academic_years_and_set_the_current_year') }}</p>
                </div>
                <a href="{{ route('admin.academic-years.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-emerald-100 hover:shadow-emerald-200 transition-all active:scale-95">
                    <i class="fas fa-plus"></i>
                    <span>{{ __('add_new') }}</span>
                </a>
            </div>

            {{-- Current Year Highlight --}}
            @php $currentYear = $academicYears->firstWhere('is_current', true) ?? \App\Models\AcademicYear::getCurrent(); @endphp
            @if($currentYear)
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl shadow-xl p-6 mb-8 text-white">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-sm">
                            <i class="fas fa-star text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-emerald-100 text-sm font-medium uppercase tracking-wider">{{ __('current_academic_year') }}</p>
                            <h3 class="text-2xl font-bold">{{ $currentYear->name }}</h3>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-play-circle text-emerald-200"></i>
                            <span>{{ \Carbon\Carbon::parse($currentYear->start_date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-stop-circle text-emerald-200"></i>
                            <span>{{ \Carbon\Carbon::parse($currentYear->end_date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock text-emerald-200"></i>
                            <span>{{ \Carbon\Carbon::parse($currentYear->start_date)->diffInDays(\Carbon\Carbon::parse($currentYear->end_date)) }} {{ __('days') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Table Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-list text-gray-400"></i>
                        {{ __('academic_year_list') }}
                    </h3>
                </div>

                @if($academicYears->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('start_date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('end_date') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('status') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('actions_2') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($academicYears as $year)
                            <tr class="hover:bg-gray-50 transition-colors {{ $year->is_current ? 'bg-emerald-50/50' : '' }}">
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $year->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl {{ $year->is_current ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center font-bold text-sm">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $year->name }}</p>
                                            @if($year->description)
                                            <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($year->description, 40) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i class="fas fa-calendar-day text-gray-300"></i>
                                        {{ \Carbon\Carbon::parse($year->start_date)->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i class="fas fa-calendar-check text-gray-300"></i>
                                        {{ \Carbon\Carbon::parse($year->end_date)->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($year->is_current)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">
                                        <i class="fas fa-circle text-[6px]"></i>
                                        {{ __('current') }}
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-500">
                                        <i class="fas fa-circle text-[6px]"></i>
                                        {{ __('inactive') }}
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if(!$year->is_current)
                                        <button type="button" onclick="confirmSetCurrent({{ $year->id }}, '{{ $year->name }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-colors">
                                            <i class="fas fa-check-circle"></i>
                                            {{ __('set_as_current') }}
                                        </button>
                                        @endif
                                        <a href="{{ route('admin.academic-years.edit', $year->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-colors">
                                            <i class="fas fa-edit"></i>
                                            {{ __('edit_2') }}
                                        </a>
                                        <button type="button" onclick="confirmDelete({{ $year->id }}, '{{ $year->name }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                                            <i class="fas fa-trash"></i>
                                            {{ __('delete_2') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @else
                {{-- Empty State --}}
                <div class="px-6 py-16 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gray-100 mb-6">
                        <i class="fas fa-calendar-times text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('no_academic_years') }}</h3>
                    <p class="text-gray-500 mb-6 max-w-sm mx-auto">{{ __('click_the_button_below_to_create_a_new_academic_year') }}</p>
                    <a href="{{ route('admin.academic-years.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all active:scale-95">
                        <i class="fas fa-plus"></i>
                        {{ __('create_new_academic_year') }}
                    </a>
                </div>
                @endif
            </div>

            {{-- Pagination --}}
            @if($academicYears->hasPages())
            <div class="mt-6">
                {{ $academicYears->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Set Current Confirmation Modal --}}
    <div x-data="{ showSetCurrentModal: false, setCurrentId: null, setCurrentName: '' }" x-cloak>
        <div x-show="showSetCurrentModal" class="fixed inset-0 z-[200] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showSetCurrentModal" @click="showSetCurrentModal = false" x-transition.opacity class="fixed inset-0 bg-black/30 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showSetCurrentModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl sm:my-8 sm:max-w-lg sm:w-full z-[210]">
                    <div class="bg-white px-8 pt-10 pb-6">
                        <div class="sm:flex sm:items-start flex-col items-center text-center">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-20 w-20 rounded-3xl bg-emerald-50 mb-6 border border-emerald-100">
                                <i class="fas fa-check-circle text-3xl text-emerald-600"></i>
                            </div>
                            <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-4">{{ __('confirm_setting') }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">
                                {{ __('are_you_sure_you_want_to_set') }} <span class="font-bold text-emerald-600" x-text="setCurrentName"></span> {{ __('as_the_current_academic_year') }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-8 py-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" @click="showSetCurrentModal = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl border-2 border-gray-200 px-6 py-3 bg-white text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all">{{ __('cancel_2') }}</button>
                        <form :action="'/admin/academic-years/' + setCurrentId + '/set-current'" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent px-6 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-sm font-bold text-white hover:from-emerald-600 hover:to-green-700 shadow-lg transition-all">
                                <i class="fas fa-check"></i>
                                {{ __('confirm_set') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-data="{ showDeleteModal: false, deletingId: null, deletingName: '' }" x-cloak>
        <div x-show="showDeleteModal" class="fixed inset-0 z-[200] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" @click="showDeleteModal = false" x-transition.opacity class="fixed inset-0 bg-black/30 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl sm:my-8 sm:max-w-lg sm:w-full z-[210]">
                    <div class="bg-white px-8 pt-10 pb-6">
                        <div class="sm:flex sm:items-start flex-col items-center text-center">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-20 w-20 rounded-3xl bg-rose-50 mb-6 border border-rose-100">
                                <i class="fas fa-exclamation-triangle text-3xl text-rose-600"></i>
                            </div>
                            <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-4">{{ __('confirm_deletion') }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">
                                {{ __('are_you_sure_you_want_to_delete_the_academic_year') }} <span class="font-bold text-rose-600" x-text="deletingName"></span> {{ __('this_action_cannot_be_undone') }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-8 py-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" @click="showDeleteModal = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl border-2 border-gray-200 px-6 py-3 bg-white text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all">{{ __('cancel_2') }}</button>
                        <form :action="'/admin/academic-years/' + deletingId" method="POST" class="w-full sm:w-auto">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent px-6 py-3 bg-gradient-to-r from-rose-500 to-red-600 text-sm font-bold text-white hover:from-rose-600 hover:to-red-700 shadow-lg transition-all">
                                <i class="fas fa-trash"></i>
                                {{ __('confirm_delete_2') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmSetCurrent(id, name) {
            var el = document.querySelector('[x-data*="showSetCurrentModal"]');
            var scope = Alpine.$data(el);
            scope.setCurrentId = id;
            scope.setCurrentName = name;
            scope.showSetCurrentModal = true;
        }

        function confirmDelete(id, name) {
            var el = document.querySelector('[x-data*="showDeleteModal"]');
            var scope = Alpine.$data(el);
            scope.deletingId = id;
            scope.deletingName = name;
            scope.showDeleteModal = true;
        }
    </script>
</x-app-layout>
