<x-app-layout>
    <div class="py-16 bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="max-w-xl mx-auto px-6 lg:px-8 w-full">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden p-8 lg:p-12 border border-gray-200">

                <div class="text-center mb-10">
                    <h3 class="text-4xl font-extrabold text-gray-800 leading-tight">{{ __('ចុះឈ្មោះសិស្សចូលវគ្គសិក្សា') }}</h3>
                    <p class="mt-2 text-lg text-gray-500">{{ __('បំពេញព័ត៌មានខាងក្រោមដើម្បីចុះឈ្មោះសិស្ស') }}</p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl relative mb-8 shadow-md" role="alert">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.perform_enrollment') }}" method="POST" class="space-y-8">
                    @csrf
                    <div>
                        <label for="student_user_id" class="block text-base font-semibold text-gray-700 mb-2">
                            {{ __('ជ្រើសរើសសិស្ស') }}
                        </label>
                        <select id="student_user_id" name="student_user_id" required
                                class="mt-1 block w-full p-4 bg-gray-50 border-2 border-gray-300 rounded-xl text-gray-800 shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200">
                            <option value="" class="text-gray-400">-- {{ __('ជ្រើសរើសសិស្ស') }} --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" class="text-gray-800" {{ old('student_user_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="course_offering_id" class="block text-base font-semibold text-gray-700 mb-2">
                            {{ __('ជ្រើសរើសវគ្គសិក្សា') }}
                        </label>
                        <select id="course_offering_id" name="course_offering_id" required
                                class="mt-1 block w-full p-4 bg-gray-50 border-2 border-gray-300 rounded-xl text-gray-800 shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200">
                            <option value="" class="text-gray-400">-- {{ __('ជ្រើសរើសវគ្គសិក្សា') }} --</option>
                            @foreach($courseOfferings as $offering)
                                <option value="{{ $offering->id }}" class="text-gray-800" {{ old('course_offering_id') == $offering->id ? 'selected' : '' }}>
                                    {{ $offering->course?->title_km ?? $offering->course?->title_en ?? 'N/A' }} ({{ $offering->academic_year }} - {{ $offering->semester }}) - {{ $offering->lecturer?->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full px-8 py-4 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-xl shadow-lg hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200 transform hover:-translate-y-0.5">
                            {{ __('ចុះឈ្មោះ') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>