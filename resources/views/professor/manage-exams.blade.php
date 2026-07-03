<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="font-extrabold text-3xl text-gray-900 tracking-wide">
                {{ __('គ្រប់គ្រងការប្រលងសម្រាប់មុខវិជ្ជា') }}
            </h2>
            <p class="mt-1 text-lg text-gray-600">
                {{ $courseOffering->course->title_km ?? $courseOffering->course->title_en ?? 'N/A' }}
                <span class="text-sm font-medium text-gray-500">
                    ({{ $courseOffering->academic_year }} - {{ $courseOffering->semester }})
                </span>
            </p>
        </div>

        <a href="{{ route('professor.my-course-offerings', ['offering_id' => $courseOffering->id]) }}"
        class="inline-flex items-center px-6 py-3 
                bg-gradient-to-r from-emerald-500 via-emerald-600 to-emerald-700 
                hover:from-emerald-600 hover:via-emerald-700 hover:to-emerald-800 
                text-white text-sm font-semibold rounded-lg shadow-md 
                hover:shadow-lg transform hover:scale-105 
                transition-all duration-300 ease-out focus:outline-none focus:ring-2 focus:ring-emerald-400">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ __('ត្រឡប់ទៅបញ្ជីមុខវិជ្ជា') }}
        </a>

    </div>
</x-slot>

     {{-- Success/Error Messages (Existing) --}}
                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mx-6 mt-6 mb-0" role="alert">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-green-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <p class="font-semibold">{{ __('ជោគជ័យ!') }}</p>
                            <p class="ml-auto">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mx-6 mt-6 mb-0" role="alert">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-red-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <p class="font-semibold">{{ __('បរាជ័យ!') }}</p>
                            <p class="ml-auto">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-100 transition-transform duration-500 ease-in-out">

                {{-- Course Information Box (styled like assignments page) --}}
                <div class="bg-emerald-50 p-6 rounded-2xl shadow-md border-l-4 border-emerald-500 mb-10 transition-all duration-300 transform hover:scale-[1.005]">
                    <div class="flex items-center space-x-4">
                        <svg class="w-10 h-10 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="text-xl font-bold text-emerald-800">{{ __('ព័ត៌មានវគ្គសិក្សា') }}</p>
                            <ul class="list-disc list-inside text-gray-700 mt-2 text-sm md:text-base">
                                {{-- <li>{{ __('លេខកូដមុខវិជ្ជា:') }} <span class="font-semibold text-gray-900">{{ $courseOffering->course->code ?? 'N/A' }}</span></li> --}}
                                <li>{{ __('គ្រូបង្រៀន:') }} <span class="font-semibold text-gray-900">{{ $courseOffering->lecturer->name ?? 'N/A' }}</span></li>
                                <li>{{ __('ចំនួននិស្សិតចុះឈ្មោះ:') }} <span class="font-semibold text-gray-900">{{ $courseOffering->studentCourseEnrollments->count() }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h4 class="text-2xl font-bold text-gray-700 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('បន្ថែមការប្រលងថ្មី') }}
                </h4>
                <div class="bg-gray-50 p-8 rounded-2xl shadow-inner mb-10 border border-gray-100">
                    {{-- Form pointing to the store route --}}
                    <form action="{{ route('professor.store-exam', ['offering_id' => $courseOffering->id]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf
                        <div>
                            <label for="title_km" class="block text-sm font-medium text-gray-700">{{ __('ចំណងជើង (ខ្មែរ)') }} <span class="text-red-500">*</span></label>
                            <input type="text" id="title_km" name="title_km" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div>
                            <label for="title_en" class="block text-sm font-medium text-gray-700">{{ __('ចំណងជើង (អង់គ្លេស)') }}</label>
                            <input type="text" id="title_en" name="title_en" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div class="md:col-span-2">
                            <label for="description_km" class="block text-sm font-medium text-gray-700">{{ __('បរិយាយ (ខ្មែរ)') }}</label>
                            <textarea id="description_km" name="description_km" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="description_en" class="block text-sm font-medium text-gray-700">{{ __('បរិយាយ (អង់គ្លេស)') }}</label>
                            <textarea id="description_en" name="description_en" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"></textarea>
                        </div>
                        <div>
                            <label for="exam_date" class="block text-sm font-medium text-gray-700">{{ __('ថ្ងៃប្រលង') }} <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="exam_date" name="exam_date" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700">{{ __('រយៈពេល (នាទី)') }} <span class="text-red-500">*</span></label>
                            <input type="number" id="duration_minutes" name="duration_minutes" required value="60" min="10" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>
                        <div class="md:col-span-2">
                             <label for="max_score" class="block text-sm font-medium text-gray-700">{{ __('ពិន្ទុអតិបរមា') }} <span class="text-red-500">*</span></label>
                            <input type="number" id="max_score" name="max_score" required value="50" min="0" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                        </div>

                        <div class="md:col-span-2 flex justify-end mt-4">
                            <button type="submit" class="w-full md:w-auto px-8 py-4 text-white font-extrabold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:scale-[1.01] bg-gradient-to-r from-emerald-600 to-purple-700 hover:from-emerald-700 hover:to-purple-800">
                                <span class="flex items-center justify-center space-x-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    <span>{{ __('បន្ថែមការប្រលង') }}</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <h4 class="text-2xl font-bold text-gray-700 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M12 18h.01"></path></svg>
                    {{ __('បញ្ជីការប្រលង') }}
                </h4>
            <div class="bg-gray-50 rounded-2xl shadow-xl mb-6">
    <div class="overflow-x-auto hidden lg:block">
        <table class="min-w-full leading-normal">
            <thead class="bg-gradient-to-r from-teal-600 to-cyan-700">
                <tr>
                    <th class="py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider rounded-tl-2xl">{{ __('ចំណងជើង') }}</th>
                    <th class="py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('ថ្ងៃប្រលង') }}</th>
                    <th class="py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('រយៈពេល') }}</th>
                    <th class="py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('ពិន្ទុ') }}</th>
                    {{-- <th class="py-4 px-6 text-left text-sm font-bold text-white uppercase tracking-wider">{{ __('ស្ថានភាព') }}</th> --}}
                    <th class="py-4 px-6 text-center text-sm font-bold text-white uppercase tracking-wider rounded-tr-2xl">{{ __('សកម្មភាព') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($exams as $exam)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="py-4 px-6 text-gray-800 font-medium">{{ $exam->title_km ?? $exam->title_en ?? 'N/A' }}</td>
                        <td class="py-4 px-6 text-gray-600">{{ \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d H:i') }}</td>
                        <td class="py-4 px-6 text-gray-600">{{ $exam->duration_minutes }} {{ __('នាទី') }}</td>
                        <td class="py-4 px-6 text-gray-600">{{ $exam->max_score }}</td>
                        {{-- <td class="py-4 px-6 text-gray-600">
                            @php
                                $examDate = \Carbon\Carbon::parse($exam->exam_date);
                                if ($examDate->isPast()) {
                                    echo '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 shadow-sm">' . __('បានបញ្ចប់') . '</span>';
                                } elseif ($examDate->isToday()) {
                                    echo '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 shadow-sm">' . __('ថ្ងៃនេះ') . '</span>';
                                } else {
                                    echo '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 shadow-sm">' . __('ជិតដល់') . '</span>';
                                }
                            @endphp
                        </td> --}}
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                {{-- ត្រូវបានប្តូរទៅជា button ដើម្បីបើក Modal --}}
                                <button type="button" class="inline-flex items-center text-sm font-semibold text-purple-600 hover:text-purple-800 transition-colors duration-200 hover:bg-purple-100 rounded-full px-3 py-1 edit-exam-btn"
                                        data-id="{{ $exam->id }}"
                                        data-title-km="{{ $exam->title_km ?? '' }}"
                                        data-title-en="{{ $exam->title_en ?? '' }}"
                                        data-description-km="{{ $exam->description_km ?? '' }}"
                                        data-description-en="{{ $exam->description_en ?? '' }}"
                                        data-exam-date="{{ \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d\TH:i') }}"
                                        data-duration-minutes="{{ $exam->duration_minutes }}"
                                        data-max-score="{{ $exam->max_score }}">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                                    {{ __('កែសម្រួល') }}
                                </button>
                                <form action="{{ route('professor.exams.destroy', ['offering_id' => $courseOffering->id, 'exam' => $exam->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('តើអ្នកពិតជាចង់លុបការប្រលងនេះមែនទេ?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center text-sm font-semibold text-red-600 hover:text-red-800 transition-colors duration-200 hover:bg-red-100 rounded-full px-3 py-1">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        {{ __('លុប') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- No data message for large screen is now handled below the table (for consistency) --}}
                @endforelse
            </tbody>
        </table>
        
        @if ($exams->isEmpty())
             <div class="py-10 px-6 text-center text-gray-500 bg-gray-50 rounded-b-2xl">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                    <p class="text-xl font-semibold mb-1">{{ __('មិនទាន់មានការប្រលងណាមួយសម្រាប់វគ្គសិក្សានេះនៅឡើយទេ។') }}</p>
                    <p class="text-sm text-gray-400">{{ __('សូមប្រើទម្រង់ខាងលើដើម្បីបង្កើតការប្រលងដំបូងរបស់អ្នក។') }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Mobile Card View (រក្សាទុកដើម្បី responsive) --}}
    <div class="space-y-4 p-4 lg:hidden">
        @forelse ($exams as $exam)
            <div class="bg-white p-4 rounded-lg shadow-md border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                <div class="flex justify-between items-start mb-3 border-b pb-2">
                    <h3 class="text-lg font-bold text-teal-700 leading-snug">{{ $exam->title_km ?? $exam->title_en ?? 'N/A' }}</h3>
                    @php
                        $examDate = \Carbon\Carbon::parse($exam->exam_date);
                        $statusClass = '';
                        $statusText = '';

                        if ($examDate->isPast()) {
                            $statusClass = 'bg-red-100 text-red-800';
                            $statusText = __('បានបញ្ចប់');
                        } elseif ($examDate->isToday()) {
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                            $statusText = __('ថ្ងៃនេះ');
                        } else {
                            $statusClass = 'bg-green-100 text-green-800';
                            $statusText = __('ជិតដល់');
                        }
                    @endphp
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }} shadow-sm flex-shrink-0">
                        {{ $statusText }}
                    </span>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-gray-50 pb-1">
                        <span class="font-semibold text-gray-600">{{ __('ថ្ងៃប្រលង') }}:</span>
                        <span class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-1">
                        <span class="font-semibold text-gray-600">{{ __('រយៈពេល') }}:</span>
                        <span class="text-gray-800 font-medium">{{ $exam->duration_minutes }} {{ __('នាទី') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-600">{{ __('ពិន្ទុ') }}:</span>
                        <span class="text-gray-800 font-medium">{{ $exam->max_score }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end space-x-2">
                    {{-- ត្រូវបានប្តូរទៅជា button ដើម្បីបើក Modal --}}
                    <button type="button" class="inline-flex items-center text-xs font-semibold text-purple-600 hover:text-purple-800 transition-colors duration-200 bg-purple-50 hover:bg-purple-100 rounded-full px-3 py-1 edit-exam-btn"
                            data-id="{{ $exam->id }}"
                            data-title-km="{{ $exam->title_km ?? '' }}"
                            data-title-en="{{ $exam->title_en ?? '' }}"
                            data-description-km="{{ $exam->description_km ?? '' }}"
                            data-description-en="{{ $exam->description_en ?? '' }}"
                            data-exam-date="{{ \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d\TH:i') }}"
                            data-duration-minutes="{{ $exam->duration_minutes }}"
                            data-max-score="{{ $exam->max_score }}">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                        {{ __('កែសម្រួល') }}
                    </button>
                    <form action="{{ route('professor.exams.destroy', ['offering_id' => $courseOffering->id, 'exam' => $exam->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('តើអ្នកពិតជាចង់លុបការប្រលងនេះមែនទេ?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-800 transition-colors duration-200 bg-red-50 hover:bg-red-100 rounded-full px-3 py-1">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ __('លុប') }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-10 px-6 text-center text-gray-500 bg-white rounded-lg border border-gray-200 shadow-inner">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                    <p class="text-xl font-semibold mb-1">{{ __('មិនទាន់មានការប្រលងណាមួយសម្រាប់វគ្គសិក្សានេះនៅឡើយទេ។') }}</p>
                    <p class="text-sm text-gray-400">{{ __('សូមប្រើទម្រង់ខាងលើដើម្បីបង្កើតការប្រលងដំបូងរបស់អ្នក។') }}</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

                @if ($exams->lastPage() > 1)
                    <div class="mt-4">
                        {{ $exams->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Edit Exam Modal (Modal កែសម្រួលការប្រលង) --}}
    {{-- Edit Exam Modal (Modal កែសម្រួលការប្រលង) --}}
    <div x-data="{ 
            open: false, 
            examId: '', 
            titleKm: '', 
            titleEn: '', 
            descriptionKm: '', 
            descriptionEn: '', 
            examDate: '', 
            durationMinutes: '', 
            maxScore: '',
            courseOfferingId: '{{ $courseOffering->id }}',
            updateRoute: '{{ route('professor.exams.update', ['offering_id' => $courseOffering->id, 'exam' => 0]) }}' 
        }"
        @open-edit-exam-modal.window="
            open = true; 
            examId = $event.detail.id; 
            titleKm = $event.detail.titleKm; 
            titleEn = $event.detail.titleEn; 
            descriptionKm = $event.detail.descriptionKm; 
            descriptionEn = $event.detail.descriptionEn; 
            examDate = $event.detail.examDate; 
            durationMinutes = $event.detail.durationMinutes; 
            maxScore = $event.detail.maxScore;">
        
        <div x-show="open" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50" style="display: none;">
            <div @click.away="open = false" 
                  class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-sm sm:max-w-lg md:max-w-xl 
                         mx-auto max-h-full overflow-y-auto 
                         transform transition-all duration-300 scale-95"
                  x-transition:enter="ease-out duration-300"
                  x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                  x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave="ease-in duration-200"
                  x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <h4 class="text-2xl font-bold text-gray-800 mb-6 flex items-center space-x-2">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                    <span>{{ __('កែសម្រួលការប្រលង') }}</span>
                </h4>
                
                <form :action="updateRoute.replace('0', examId)" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="edit_title_km" class="block text-sm font-medium text-gray-700">{{ __('ចំណងជើង (ខ្មែរ)') }} <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_title_km" name="title_km" x-model="titleKm" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                    </div>
                    <div>
                        <label for="edit_title_en" class="block text-sm font-medium text-gray-700">{{ __('ចំណងជើង (អង់គ្លេស)') }}</label>
                        <input type="text" id="edit_title_en" name="title_en" x-model="titleEn" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                    </div>
                    <div class="md:col-span-2">
                        <label for="edit_description_km" class="block text-sm font-medium text-gray-700">{{ __('បរិយាយ (ខ្មែរ)') }}</label>
                        <textarea id="edit_description_km" name="description_km" x-model="descriptionKm" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label for="edit_description_en" class="block text-sm font-medium text-gray-700">{{ __('បរិយាយ (អង់គ្លេស)') }}</label>
                        <textarea id="edit_description_en" name="description_en" x-model="descriptionEn" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"></textarea>
                    </div>
                    <div>
                        <label for="edit_exam_date" class="block text-sm font-medium text-gray-700">{{ __('ថ្ងៃប្រលង') }} <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="edit_exam_date" name="exam_date" x-model="examDate" required class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                    </div>
                    <div>
                        <label for="edit_duration_minutes" class="block text-sm font-medium text-gray-700">{{ __('រយៈពេល (នាទី)') }} <span class="text-red-500">*</span></label>
                        <input type="number" id="edit_duration_minutes" name="duration_minutes" x-model="durationMinutes" required min="10" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                    </div>
                    <div class="md:col-span-2">
                        <label for="edit_max_score" class="block text-sm font-medium text-gray-700">{{ __('ពិន្ទុអតិបរមា') }} <span class="text-red-500">*</span></label>
                        <input type="number" id="edit_max_score" name="max_score" x-model="maxScore" required min="0" class="mt-1 block w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                    </div>

                    <div class="md:col-span-2 flex justify-end space-x-3 mt-4">
                        <button type="button" @click="open = false" class="px-6 py-3 text-gray-700 font-semibold rounded-xl shadow-sm transition-all duration-200 hover:bg-gray-200">
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
            document.querySelectorAll('.edit-exam-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const data = this.dataset;
                    
                    window.dispatchEvent(new CustomEvent('open-edit-exam-modal', {
                        detail: {
                            id: data.id,
                            titleKm: data.titleKm || '',
                            titleEn: data.titleEn || '',
                            descriptionKm: data.descriptionKm || '',
                            descriptionEn: data.descriptionEn || '',
                            examDate: data.examDate,
                            durationMinutes: data.durationMinutes,
                            maxScore: data.maxScore,
                        }
                    }));
                });
            });
        });
    </script>
</x-app-layout>