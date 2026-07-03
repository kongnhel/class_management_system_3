<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-3xl text-gray-800 leading-tight">
                    {{ __('គ្រប់គ្រងកម្រងសំណួរសម្រាប់មុខវិជ្ជា') }}
                </h2>
                <p class="mt-1 text-lg text-gray-500">{{ $courseOffering->course->title_km ?? $courseOffering->course->title_en ?? 'N/A' }} ({{ $courseOffering->academic_year }} - {{ $courseOffering->semester }})</p>
            </div>
            <a href="{{ route('professor.my-course-offerings', ['offering_id' => $courseOffering->id]) }}"
                class="inline-flex items-center px-6 py-3 
                bg-gradient-to-r from-emerald-500 via-emerald-600 to-emerald-700 
                hover:from-emerald-600 hover:via-emerald-700 hover:to-emerald-800 
                text-white text-sm font-semibold rounded-lg shadow-md 
                hover:shadow-lg transform hover:scale-105 
                transition-all duration-300 ease-out focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path></svg>
                {{ __('ត្រឡប់ទៅបញ្ជីមុខវិជ្ជា') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- ប៊ូតុងបង្កើតថ្មី -->
                <div class="flex justify-end mb-6">
                    <button id="open-create-quiz-modal-btn"
                        class="inline-flex items-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg shadow-md transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        {{ __('បង្កើតកម្រងសំណួរថ្មី') }}
                    </button>
                </div>

                <!-- សារជូនដំណឹង (Success/Error Messages) -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                
                <!-- តារាងបង្ហាញបញ្ជីកម្រងសំណួរ -->
                <h3 class="text-2xl font-semibold text-gray-700 mb-4">{{ __('បញ្ជីកម្រងសំណួរ') }}</h3>
                
                @if ($quizzes->isEmpty())
                    <p class="text-gray-500">{{ __('មិនទាន់មានកម្រងសំណួរត្រូវបានបង្កើតនៅឡើយទេ។') }}</p>
                @else
                    <div class="overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ចំណងជើង') }} (ខ្មែរ)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ពិន្ទុអតិបរមា') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ចាប់ផ្ដើម') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('បញ្ចប់') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('បោះពុម្ព') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('សកម្មភាព') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($quizzes as $quiz)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $quiz->title_km }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $quiz->max_score }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($quiz->start_time)->format('Y-m-d H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($quiz->end_time)->format('Y-m-d H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $quiz->is_published ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $quiz->is_published ? __('បោះពុម្ពហើយ') : __('មិនទាន់បោះពុម្ព') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button
                                                class="text-emerald-600 hover:text-emerald-900 mr-3 edit-quiz-btn"
                                                data-id="{{ $quiz->id }}"
                                                data-title-km="{{ $quiz->title_km }}"
                                                data-title-en="{{ $quiz->title_en }}"
                                                data-description-km="{{ $quiz->description_km }}"
                                                data-description-en="{{ $quiz->description_en }}"
                                                data-max-score="{{ $quiz->max_score }}"
                                                data-start-time="{{ \Carbon\Carbon::parse($quiz->start_time)->format('Y-m-d\TH:i') }}"
                                                data-end-time="{{ \Carbon\Carbon::parse($quiz->end_time)->format('Y-m-d\TH:i') }}"
                                                data-is-published="{{ $quiz->is_published }}"
                                            >
                                                {{ __('កែប្រែ') }}
                                            </button>
                                            <!-- ប៊ូតុងផ្សេងទៀតដូចជាគ្រប់គ្រងសំណួរ -->
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>


    <!-- ========================================================================================= -->
    <!-- CREATE QUIZ MODAL (សម្រាប់បង្កើតថ្មី) -->
    <!-- ========================================================================================= -->
    <div id="create-quiz-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform transition-all duration-300 scale-95 opacity-0" id="create-quiz-content">
            <div class="p-6">
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 class="text-xl font-bold text-gray-800">{{ __('បង្កើតកម្រងសំណួរថ្មី') }}</h3>
                    <button id="close-create-quiz-modal-btn" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                
                <!-- ត្រូវប្រើ route ត្រឹមត្រូវសម្រាប់ store -->
                <form method="POST" action="{{ route('professor.quizzes.store', ['offering_id' => $courseOffering->id]) }}">
                    @csrf
                    <!-- លេខសម្គាល់មុខវិជ្ជា (Hidden Field) -->
                    <input type="hidden" name="course_offering_id" value="{{ $courseOffering->id }}">
                    
                    <div class="space-y-4">
                        <!-- ចំណងជើង (ខ្មែរ) -->
                        <div>
                            <label for="create_title_km" class="block text-sm font-medium text-gray-700">{{ __('ចំណងជើង') }} (ខ្មែរ) <span class="text-red-500">*</span></label>
                            <input type="text" id="create_title_km" name="title_km" value="{{ old('title_km') }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('title_km') border-red-500 @enderror">
                            @error('title_km')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ចំណងជើង (អង់គ្លេស) -->
                        <div>
                            <label for="create_title_en" class="block text-sm font-medium text-gray-700">{{ __('ចំណងជើង') }} (អង់គ្លេស)</label>
                            <input type="text" id="create_title_en" name="title_en" value="{{ old('title_en') }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('title_en') border-red-500 @enderror">
                            @error('title_en')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- ពេលវេលាចាប់ផ្តើម (Start Time) -->
                            <div>
                                <label for="create_start_time" class="block text-sm font-medium text-gray-700">{{ __('ពេលវេលាចាប់ផ្តើម') }} <span class="text-red-500">*</span></label>
                                <input type="datetime-local" id="create_start_time" name="start_time" value="{{ old('start_time') }}" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('start_time') border-red-500 @enderror">
                                @error('start_time')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ពេលវេលាបញ្ចប់ (End Time) -->
                            <div>
                                <label for="create_end_time" class="block text-sm font-medium text-gray-700">{{ __('ពេលវេលាបញ្ចប់') }} <span class="text-red-500">*</span></label>
                                <input type="datetime-local" id="create_end_time" name="end_time" value="{{ old('end_time') }}" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('end_time') border-red-500 @enderror">
                                @error('end_time')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- ពិន្ទុអតិបរមា (Max Score) -->
                        <div>
                            <label for="create_max_score" class="block text-sm font-medium text-gray-700">{{ __('ពិន្ទុអតិបរមា') }} <span class="text-red-500">*</span></label>
                            <input type="number" id="create_max_score" name="max_score" value="{{ old('max_score') }}" required min="1"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('max_score') border-red-500 @enderror">
                            @error('max_score')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ការពិពណ៌នា (ខ្មែរ) -->
                        <div>
                            <label for="create_description_km" class="block text-sm font-medium text-gray-700">{{ __('ការពិពណ៌នា') }} (ខ្មែរ)</label>
                            <textarea id="create_description_km" name="description" rows="3"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('description_km') border-red-500 @enderror">{{ old('description_km') }}</textarea>
                            @error('description_km')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ស្ថានភាពបោះពុម្ព (Is Published) -->
                        <div class="flex items-center">
                            <input type="checkbox" id="create_is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                            <label for="create_is_published" class="ml-2 text-sm font-medium text-gray-700">{{ __('បោះពុម្ពភ្លាមៗ') }} (Publish)</label>
                            @error('is_published')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" id="cancel-create-quiz-modal-btn"
                            class="px-6 py-3 text-gray-600 font-semibold rounded-xl shadow-sm transition-all duration-200 hover:bg-gray-200">
                            {{ __('បោះបង់') }}
                        </button>
                        <button type="submit" class="px-6 py-3 text-white font-extrabold rounded-xl shadow-md transition-all duration-200 bg-purple-600 hover:bg-purple-700">
                            {{ __('រក្សាទុកកម្រងសំណួរ') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- EDIT QUIZ MODAL (សម្រាប់កែប្រែ) - ត្រូវប្ដូរ Route សម្រាប់ update -->
    <!-- ========================================================================================= -->
    <div id="edit-quiz-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform transition-all duration-300 scale-95 opacity-0" id="edit-quiz-content">
            <div class="p-6">
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 class="text-xl font-bold text-gray-800">{{ __('កែប្រែកម្រងសំណួរ') }}</h3>
                    <button id="close-edit-quiz-modal-btn" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                
                <form id="edit-quiz-form" method="POST" action="">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <!-- ចំណងជើង (ខ្មែរ) -->
                        <div>
                            <label for="edit_title_km" class="block text-sm font-medium text-gray-700">{{ __('ចំណងជើង') }} (ខ្មែរ) <span class="text-red-500">*</span></label>
                            <input type="text" id="edit_title_km" name="title_km" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <!-- ចំណងជើង (អង់គ្លេស) -->
                        <div>
                            <label for="edit_title_en" class="block text-sm font-medium text-gray-700">{{ __('ចំណងជើង') }} (អង់គ្លេស)</label>
                            <input type="text" id="edit_title_en" name="title_en"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- ពេលវេលាចាប់ផ្តើម (Start Time) -->
                            <div>
                                <label for="edit_start_time" class="block text-sm font-medium text-gray-700">{{ __('ពេលវេលាចាប់ផ្តើម') }} <span class="text-red-500">*</span></label>
                                <input type="datetime-local" id="edit_start_time" name="start_time" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <!-- ពេលវេលាបញ្ចប់ (End Time) -->
                            <div>
                                <label for="edit_end_time" class="block text-sm font-medium text-gray-700">{{ __('ពេលវេលាបញ្ចប់') }} <span class="text-red-500">*</span></label>
                                <input type="datetime-local" id="edit_end_time" name="end_time" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>

                        <!-- ពិន្ទុអតិបរមា (Max Score) -->
                        <div>
                            <label for="edit_max_score" class="block text-sm font-medium text-gray-700">{{ __('ពិន្ទុអតិបរមា') }} <span class="text-red-500">*</span></label>
                            <input type="number" id="edit_max_score" name="max_score" required min="1"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <!-- ការពិពណ៌នា (ខ្មែរ) -->
                        <div>
                            <label for="edit_description_km" class="block text-sm font-medium text-gray-700">{{ __('ការពិពណ៌នា') }} (ខ្មែរ)</label>
                            <textarea id="edit_description_km" name="description_km" rows="3"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                        
                        <!-- ស្ថានភាពបោះពុម្ព (Is Published) -->
                        <div class="flex items-center">
                            <input type="checkbox" id="edit_is_published" name="is_published" value="1"
                                class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                            <label for="edit_is_published" class="ml-2 text-sm font-medium text-gray-700">{{ __('បោះពុម្ពភ្លាមៗ') }} (Publish)</label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" id="cancel-edit-quiz-modal-btn"
                            class="px-6 py-3 text-gray-600 font-semibold rounded-xl shadow-sm transition-all duration-200 hover:bg-gray-200">
                            {{ __('បោះបង់') }}
                        </button>
                        <button type="submit" class="px-6 py-3 text-white font-extrabold rounded-xl shadow-md transition-all duration-200 bg-purple-600 hover:bg-purple-700">
                            {{ __('រក្សាទុកការកែប្រែ') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createModal = document.getElementById('create-quiz-modal');
            const createContent = document.getElementById('create-quiz-content');
            const openCreateBtn = document.getElementById('open-create-quiz-modal-btn');
            const closeCreateBtn = document.getElementById('close-create-quiz-modal-btn');
            const cancelCreateBtn = document.getElementById('cancel-create-quiz-modal-btn');

            const editModal = document.getElementById('edit-quiz-modal');
            const editContent = document.getElementById('edit-quiz-content');
            const editForm = document.getElementById('edit-quiz-form');
            const closeEditBtn = document.getElementById('close-edit-quiz-modal-btn');
            const cancelEditBtn = document.getElementById('cancel-edit-quiz-modal-btn');

            // ------------------ Helper Functions ------------------
            const showModal = (modal, content) => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            };

            const hideModal = (modal, content) => {
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }, 300);
            };

            // ------------------ CREATE Modal Logic ------------------
            openCreateBtn.addEventListener('click', () => showModal(createModal, createContent));
            closeCreateBtn.addEventListener('click', () => hideModal(createModal, createContent));
            cancelCreateBtn.addEventListener('click', () => hideModal(createModal, createContent));
            
            // Check for validation errors on page load and reopen modal if errors exist
            @if ($errors->any() && old('course_offering_id') == $courseOffering->id)
                showModal(createModal, createContent);
            @endif

            // ------------------ EDIT Modal Logic ------------------
            // 1. Open Edit Modal when button is clicked
            document.querySelectorAll('.edit-quiz-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const data = this.dataset;
                    const quizId = data.id;

                    // Set form action URL (assuming the update route is: professor.quizzes.update/{quizId})
                    // NOTE: Please ensure this route is correctly defined in web.php
                    editForm.action = `/professor/quizzes/${quizId}`; 

                    // Populate form fields
                    document.getElementById('edit_title_km').value = data.titleKm || '';
                    document.getElementById('edit_title_en').value = data.titleEn || '';
                    document.getElementById('edit_description_km').value = data.descriptionKm || '';
                    document.getElementById('edit_description_en').value = data.descriptionEn || '';
                    document.getElementById('edit_max_score').value = data.maxScore;
                    // Populate datetime-local fields. The data attributes already contain the required 'Y-m-d\TH:i' format.
                    document.getElementById('edit_start_time').value = data.startTime;
                    document.getElementById('edit_end_time').value = data.endTime;
                    
                    // Set checkbox state
                    const isPublishedCheckbox = document.getElementById('edit_is_published');
                    isPublishedCheckbox.checked = data.isPublished === '1';

                    showModal(editModal, editContent);
                });
            });

            // 2. Close Edit Modal
            closeEditBtn.addEventListener('click', () => hideModal(editModal, editContent));
            cancelEditBtn.addEventListener('click', () => hideModal(editModal, editContent));
        });
    </script>
</x-app-layout>