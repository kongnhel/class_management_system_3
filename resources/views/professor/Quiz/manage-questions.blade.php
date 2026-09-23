<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-3xl text-gray-800 leading-tight">
                    {{ __('manage_quiz_questions') }}
                </h2>
                <p class="mt-1 text-lg text-gray-500">
                    Quiz: <span class="font-semibold text-emerald-600">{{ $quiz->title_km ?? $quiz->title_en }}</span>
                    | មុខវិជ្ជា: {{ $courseOffering->course->title_km ?? 'N/A' }}
                </p>
            </div>
            
            <div class="flex space-x-3">
                <a wire:navigate href="{{ route('professor.quizzes.index', $courseOffering->id) }}"
                   class="inline-flex items-center px-6 py-3 border border-gray-300 bg-white 
                          hover:bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg shadow-sm 
                          transition-all duration-300 ease-out focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('back_to_quizzes') }}
                </a>

                <button onclick="document.getElementById('create-question-modal').classList.remove('hidden')"
                    class="inline-flex items-center px-6 py-3 bg-emerald-600 
                           hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-md 
                           hover:shadow-lg transform hover:scale-105 
                           transition-all duration-300 ease-out focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    {{ __('add_new_question') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <h3 class="text-2xl font-bold mb-4 text-gray-700 border-b pb-2">{{ __('existing_questions') }}</h3>
                
                @forelse ($quiz->questions ?? [] as $question)
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm mb-4 border border-gray-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 mr-4">
                                <span class="text-sm font-semibold text-gray-500 mr-2">({{ $loop->iteration }}) {{ $question->type === 'multiple_choice' ? 'ពហុជ្រើសរើស' : 'N/A' }}:</span>
                                <p class="text-lg font-medium text-gray-900 mt-1">{!! nl2br(e($question->text_km)) !!}</p>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <p class="text-xl font-extrabold text-green-600">{{ $question->score ?? 0 }} {{ __('score') }}</p>
                                <div class="mt-2 space-x-2">
                                    <button class="text-purple-600 hover:text-purple-900 text-sm"
                                        onclick="openEditQuestionModal(this)"
                                        data-id="{{ $question->id }}"
                                        data-type="{{ $question->type }}"
                                        data-text-km="{{ $question->text_km }}"
                                        data-score="{{ $question->score }}">
                                        {{ __('edit_2') }}
                                    </button>
                                    {{-- Placeholder for Delete Form --}}
                                    <form action="{{ route('professor.quizzes.destroy', ['offering_id' => $courseOffering->id, 'quiz' => $quiz->id]) }}/questions/{{ $question->id }}" method="POST" class="inline" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបសំណួរនេះមែនទេ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm ml-2">
                                            {{ __('delete_2') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Placeholder for Question Options/Answers Management --}}
                        <div class="mt-4 pt-3 border-t border-gray-200">
                            <h4 class="text-md font-semibold text-gray-700">{{ __('answer_options') }}</h4>
                            <p class="text-sm text-gray-500 italic">
                                {{ __('answer_options_coming_soon') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700">
                        {{ __('no_questions_yet_add_one') }}
                    </div>
                @endforelse

            </div>
        </div>
    </div>

    <!-- Modal for Creating New Question -->
    <div id="create-question-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-8 border w-11/12 md:w-1/2 lg:w-2/5 shadow-2xl rounded-xl bg-white">
            <h3 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">{{ __('add_new_question') }}</h3>
            {{-- Action will go to a new QuestionController method: /offering_id/quizzes/quiz_id/questions --}}
            <form action="{{ route('professor.quizzes.manage-questions', ['offering_id' => $courseOffering->id, 'quiz' => $quiz->id]) }}" method="POST">
                @csrf 
                
                {{-- Placeholder for Question Type --}}
                <div class="mb-4">
                    <label for="create_question_type" class="block text-sm font-medium text-gray-700">{{ __('question_type') }}<span class="text-red-500">*</span></label>
                    <select name="type" id="create_question_type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="multiple_choice">{{ __('multiple_choice') }}</option>
                        {{-- Add other types as needed --}}
                    </select>
                </div>

                <!-- Question Text (Khmer) -->
                <div class="mb-4">
                    <label for="create_text_km" class="block text-sm font-medium text-gray-700">{{ __('question_text_khmer') }}<span class="text-red-500">*</span></label>
                    <textarea name="text_km" id="create_text_km" rows="4" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('text_km') }}</textarea>
                </div>

                <!-- Score -->
                <div class="mb-6">
                    <label for="create_score" class="block text-sm font-medium text-gray-700">{{ __('score') }}<span class="text-red-500">*</span></label>
                    <input type="number" name="score" id="create_score" required step="0.1" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" value="{{ old('score', 1) }}">
                </div>

                {{-- Note: Options/Answers Management requires a dedicated component/further logic --}}
                <p class="text-sm text-yellow-600 italic mb-6 border-t pt-3">{{ __('note_answer_options_future') }}</p>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('create-question-modal').classList.add('hidden')" 
                            class="px-4 py-2 text-gray-700 font-semibold rounded-lg shadow-sm transition-all duration-200 hover:bg-gray-200">
                        {{ __('cancel_2') }}
                    </button>
                    <button type="submit" class="px-6 py-3 text-white font-extrabold rounded-lg shadow-md transition-all duration-200 bg-emerald-600 hover:bg-emerald-700">
                        {{ __('save_question') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Editing Existing Question -->
    <div id="edit-question-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-8 border w-11/12 md:w-1/2 lg:w-2/5 shadow-2xl rounded-xl bg-white">
            <h3 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">{{ __('edit_question') }}</h3>
            {{-- Action will be updated by JS --}}
            <form id="edit-question-form" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="edit_question_type" class="block text-sm font-medium text-gray-700">{{ __('question_type') }}<span class="text-red-500">*</span></label>
                    <select name="type" id="edit_question_type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="multiple_choice">{{ __('multiple_choice') }}</option>
                    </select>
                </div>

                <!-- Question Text (Khmer) -->
                <div class="mb-4">
                    <label for="edit_text_km" class="block text-sm font-medium text-gray-700">{{ __('question_text_khmer') }}<span class="text-red-500">*</span></label>
                    <textarea name="text_km" id="edit_text_km" rows="4" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>

                <!-- Score -->
                <div class="mb-6">
                    <label for="edit_score" class="block text-sm font-medium text-gray-700">{{ __('score') }}<span class="text-red-500">*</span></label>
                    <input type="number" name="score" id="edit_score" required step="0.1" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('edit-question-modal').classList.add('hidden')" 
                            class="px-4 py-2 text-gray-700 font-semibold rounded-lg shadow-sm transition-all duration-200 hover:bg-gray-200">
                        {{ __('cancel_2') }}
                    </button>
                    <button type="submit" class="px-6 py-3 text-white font-extrabold rounded-lg shadow-md transition-all duration-200 bg-purple-600 hover:bg-purple-700">
                        {{ __('save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript to handle modal opening and data population -->
    <script>
        function openEditQuestionModal(button) {
            const data = button.dataset;
            const modal = document.getElementById('edit-question-modal');
            const form = document.getElementById('edit-question-form');
            
            // Note: This assumes a separate QuestionController with RESTful routes
            const actionUrl = '{{ url("professor/my-course-offerings/{$courseOffering->id}/quizzes/{$quiz->id}/questions") }}/' + data.id;

            // Set the form action
            form.setAttribute('action', actionUrl);

            // Populate form fields
            document.getElementById('edit_question_type').value = data.type;
            document.getElementById('edit_text_km').value = data.textKm;
            document.getElementById('edit_score').value = data.score;

            // Show the modal
            modal.classList.remove('hidden');
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const createModal = document.getElementById('create-question-modal');
            const editModal = document.getElementById('edit-question-modal');

            window.onclick = function(event) {
                if (event.target === createModal) {
                    createModal.classList.add('hidden');
                }
                if (event.target === editModal) {
                    editModal.classList.add('hidden');
                }
            }
        });
    </script>
</x-app-layout>