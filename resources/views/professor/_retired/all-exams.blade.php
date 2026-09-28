<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('all_exams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-8">
                <h3 class="text-3xl font-bold text-gray-800 mb-8 flex items-center">
                    <i class="fas fa-pen-square mr-3 text-purple-600"></i>{{ __('all_exams_managed_by_me') }}
                </h3>

                <div class="overflow-x-auto bg-gray-50 rounded-lg shadow-inner mb-6">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700 uppercase text-sm font-semibold">
                                <th class="py-3 px-4 text-left rounded-tl-lg">{{ __('title') }}</th>
                                <th class="py-3 px-4 text-left">{{ __('course') }}</th>
                                <th class="py-3 px-4 text-left">{{ __('exam_date') }}</th>
                                <th class="py-3 px-4 text-left">{{ __('duration_minutes') }}</th>
                                <th class="py-3 px-4 text-left">{{ __('maximum_score') }}</th>
                                <th class="py-3 px-4 text-left">{{ __('status') }}</th>
                                <th class="py-3 px-4 text-center rounded-tr-lg">{{ __('actions_2') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($exams as $exam)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-4 text-gray-800">{{ $exam->title_km ?? $exam->title_en ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $exam->courseOffering->course->title_km ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d H:i') }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $exam->duration_minutes }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $exam->max_score }}</td>
                                    <td class="py-3 px-4 text-gray-600">
                                        @if (\Carbon\Carbon::parse($exam->exam_date)->isPast())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('completed') }}</span>
                                        @elseif (\Carbon\Carbon::parse($exam->exam_date)->isToday())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('today') }}</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('upcoming') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        {{-- Link to view results for this exam (requires a specific route) --}}
                                        <a wire:navigate href="{{ route('professor.manage-exams', ['offering_id' => $exam->course_offering_id]) }}" class="text-emerald-600 hover:text-emerald-800 font-semibold py-1 px-3 rounded-full text-sm transition-colors duration-200 hover:bg-emerald-100">
                                            {{ __('view_results') }} (0)
                                        </a>
                                        {{-- Edit Button (placeholder for now) --}}
                                        <button class="text-purple-600 hover:text-purple-800 font-semibold py-1 px-3 rounded-full text-sm transition-colors duration-200 hover:bg-purple-100">
                                            {{ __('edit_4') }}
                                        </button>
                                        {{-- Delete Form (placeholder for now) --}}
                                        <form action="#" method="POST" class="inline-block" onsubmit="return confirm('{{ __('confirm_delete_exam') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold py-1 px-3 rounded-full text-sm transition-colors duration-200 hover:bg-red-100">
                                                {{ __('delete_2') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 px-6 text-center text-gray-500">
                                        {{ __('no_exams_have_been_scheduled_yet') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="mt-4">
                    {{ $exams->links('pagination::tailwind', ['pageName' => 'examsPage']) }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
