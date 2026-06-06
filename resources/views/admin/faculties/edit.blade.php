<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-8 lg:p-12 border border-gray-200 transition-all duration-300 transform hover:shadow-3xl">

                <!-- Page Header -->
                <div class="mb-8 pb-4 border-b border-gray-200">
                    <h2 class="font-extrabold text-4xl text-gray-900 leading-tight">
            {{ __('កែប្រែមហាវិទ្យាល័យ: ') }} {{ $faculty->name_km }}
                 </h2>
    </div>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold text-green-700 mb-6">កែប្រែព័ត៌មានមហាវិទ្យាល័យ</h3>

                              <!-- Error & Session Messages -->
                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl relative mb-8 flex items-center space-x-3 shadow-md" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="block sm:inline font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl relative mb-8 flex items-center space-x-3 shadow-md" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586l-1.293-1.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="block sm:inline font-medium">{{ session('error') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl relative mb-8 flex items-start space-x-3 shadow-md" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <strong class="font-bold block">{{ __('មានបញ្ហា!') }}</strong>
                            <span class="block sm:inline mt-1">{{ __('សូមពិនិត្យមើលកំហុសឆ្គងខាងក្រោម។') }}</span>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.update-faculty', $faculty->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Name Khmer -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    
                    <div class="mb-4">
                        <label for="name_km" class="block font-medium text-sm text-gray-700">{{ __('ឈ្មោះមហាវិទ្យាល័យជាខ្មែរ') }}</label>
                        <input id="name_km" type="text" name="name_km" value="{{ old('name_km', $faculty->name_km) }}" required autofocus
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('name_km') border-red-500 @enderror">
                        @error('name_km') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Name English -->
                    <div class="mb-4">
                        <label for="name_en" class="block font-medium text-sm text-gray-700">{{ __('ឈ្មោះមហាវិទ្យាល័យជាអង់គ្លេស') }}</label>
                        <input id="name_en" type="text" name="name_en" value="{{ old('name_en', $faculty->name_en) }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('name_en') border-red-500 @enderror">
                        @error('name_en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Dean (Professor) -->
                    <div class="mb-4">
                        <label for="dean_user_id" class="block font-medium text-sm text-gray-700">{{ __('ប្រធានមហាវិទ្យាល័យ (Dean)') }}</label>
                        <select id="dean_user_id" name="dean_user_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('dean_user_id') border-red-500 @enderror">
                            <option value="">{{ __('ជ្រើសរើសប្រធានមហាវិទ្យាល័យ (ស្រេចចិត្ត)') }}</option>
                            @foreach ($professors as $professor)
                                <option value="{{ $professor->id }}" {{ old('dean_user_id', $faculty->dean_user_id) == $professor->id ? 'selected' : '' }}>
                                    {{ $professor->name }} ({{ $professor->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('dean_user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    </div>

                    <div class="mt-12 flex justify-between items-center">

                        <a href="{{ route('admin.manage-faculties') }}" class="px-6 py-3 text-gray-600 font-semibold rounded-full hover:bg-gray-200 transition duration-300 transform hover:scale-105">{{ __('បោះបង់') }}</a>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-full shadow-lg hover:from-blue-600 hover:to-indigo-700 transition duration-300 transform hover:scale-105 flex items-center space-x-2">
                            <span>{{ __('រក្សាទុកការកែប្រែ') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                        {{-- <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"> --}}
                            
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
