<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('exams_for') }} {{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-semibold mb-4 text-emerald-700">{{ __('create_new_exam') }}</h3>
                <form method="POST" action="{{ route('professor.store-exam', $courseOffering->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="exam-title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('exam_title_khmer') }}</label>
                        <input type="text" id="exam-title" name="title_km" value="{{ old('title_km') }}" class="p-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="{{ __('e_g_final_exam_math') }}" required>
                        @error('title_km') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="exam-title-en" class="block text-sm font-medium text-gray-700 mb-1">{{ __('exam_title_english') }}</label>
                        <input type="text" id="exam-title-en" name="title_en" value="{{ old('title_en') }}" class="p-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="{{ __('Final Exam Math') }}" required>
                        @error('title_en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="exam-date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('exam_date_2') }}</label>
                            <input type="datetime-local" id="exam-date" name="exam_date" value="{{ old('exam_date') }}" class="p-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-emerald-500" required>
                            @error('exam_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="exam-duration" class="block text-sm font-medium text-gray-700 mb-1">{{ __('duration_minutes') }}</label>
                            <input type="number" id="exam-duration" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="1" class="p-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-emerald-500" required>
                            @error('duration_minutes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="exam-max-score" class="block text-sm font-medium text-gray-700 mb-1">{{ __('maximum_score') }}</label>
                        <input type="number" id="exam-max-score" name="total_points" value="{{ old('total_points', 100) }}" min="1" class="p-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-emerald-500" required>
                        @error('total_points') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="bg-emerald-600 text-white px-6 py-3 rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 ease-in-out">
                        {{ __('create_exam') }}
                    </button>
                </form>

                <h3 class="text-2xl font-semibold mb-4 text-emerald-700 mt-8">{{ __('all_exams') }}</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('title') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('duration_minutes') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('maximum_score') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('actions_2') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($courseOffering->exams as $exam)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $exam->title_km }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($exam->exam_date)->format('d-M-Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $exam->duration_minutes }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $exam->total_points }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a wire:navigate href="{{ route('professor.edit-exam', ['offering_id' => $courseOffering->id, 'exam' => $exam->id]) }}" class="text-emerald-600 hover:text-emerald-900 mr-3">{{ __('edit_2') }}</a>
                                        <form action="{{ route('professor.delete-exam', ['offering_id' => $courseOffering->id, 'exam' => $exam->id]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('are_you_sure') }}')">{{ __('delete_2') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('no_exams_yet') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
