<x-app-layout>
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 text-white pb-28 pt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-2xl">
                        <i class="fas fa-file-import text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">នាំចូលទិន្នន័យ</h2>
                        <p class="text-blue-200 mt-1 text-sm">នាំចូលសិស្ស ឬសាស្ត្រាចារ្យពីឯកសារ Excel</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 pb-12 relative z-10">

            {{-- Success Toast --}}
            @if(session('success'))
            <div id="success-toast" class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 h-1 bg-emerald-500 animate-toast-progress w-full"></div>
                <div class="flex items-start gap-3">
                    <div class="bg-emerald-100 text-emerald-600 p-2 rounded-xl flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-emerald-800">{{ session('success') }}</p>
                        @if(session('import_errors') && count(session('import_errors')) > 0)
                        <div class="mt-3 bg-emerald-100/50 rounded-xl p-3">
                            <p class="text-xs font-bold text-emerald-700 mb-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i> មានកំហុស {{ count(session('import_errors')) }} កន្លែង
                            </p>
                            <ul class="text-xs text-emerald-700 space-y-1 max-h-32 overflow-y-auto">
                                @foreach(session('import_errors') as $error)
                                <li class="flex items-start gap-1">
                                    <span class="text-emerald-400 mt-0.5">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                    <button onclick="document.getElementById('success-toast').remove()" class="text-emerald-400 hover:text-emerald-600 flex-shrink-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- Error Toast --}}
            @if(session('error'))
            <div id="error-toast" class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 h-1 bg-red-500 w-full"></div>
                <div class="flex items-start gap-3">
                    <div class="bg-red-100 text-red-600 p-2 rounded-xl flex-shrink-0">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-red-800">{{ session('error') }}</p>
                    </div>
                    <button onclick="document.getElementById('error-toast').remove()" class="text-red-400 hover:text-red-600 flex-shrink-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- Steps Instructions --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                    <div class="bg-blue-100 text-blue-600 p-2 rounded-xl">
                        <i class="fas fa-list-ol text-sm"></i>
                    </div>
                    ជំហានក្នុងការនាំចូល
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="flex items-start gap-3 bg-gray-50 rounded-xl p-4">
                        <div class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">1</div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">ទាញយក Template</p>
                            <p class="text-xs text-gray-500 mt-1">ទាញយកម៉ាស៊ីនត្រជាក់ CSV សម្រាប់បំពេញទិន្នន័យ</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-50 rounded-xl p-4">
                        <div class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">2</div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">បំពេញទិន្នន័យ</p>
                            <p class="text-xs text-gray-500 mt-1">បំពេញព័ត៌មានក្នុងម៉ាស៊ីនត្រជាក់ឱ្យបានត្រឹមត្រូវ</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-50 rounded-xl p-4">
                        <div class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">3</div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">នាំចូល</p>
                            <p class="text-xs text-gray-500 mt-1">ជ្រើសរើសឯកសារហើយចុចប៊ូតុងនាំចូល</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Template Download --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-emerald-100 text-emerald-600 p-3 rounded-2xl">
                            <i class="fas fa-file-csv text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">ទាញយក Template</h3>
                            <p class="text-sm text-gray-500 mt-0.5">ទាញយកម៉ាស៊ីនត្រជាក់ CSV សម្រាប់បំពេញទិន្នន័យ</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.import.template') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-download"></i> ទាញយក Template
                    </a>
                </div>
            </div>

            {{-- Import Form --}}
            <form action="{{ route('admin.import.users') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                @csrf

                <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                    <div class="bg-indigo-100 text-indigo-600 p-2 rounded-xl">
                        <i class="fas fa-cloud-upload-alt text-sm"></i>
                    </div>
                    នាំចូលទិន្នន័យ
                </h3>

                <div class="space-y-5">
                    {{-- Role Selection --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            ប្រភេទអ្នកប្រើប្រាស់ <span class="text-red-500">*</span>
                        </label>
                        <select name="role" required class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="student">សិស្ស</option>
                            <option value="professor">សាស្ត្រាចារ្យ</option>
                        </select>
                    </div>

                    {{-- Student Fields --}}
                    <div id="student-fields">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    កម្មវិធីសិក្សា <span class="text-red-500">*</span>
                                </label>
                                <select name="program_id" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="">ជ្រើសរើសកម្មវិធីសិក្សា</option>
                                    @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name_km }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">ជំនាន់</label>
                                <input type="text" name="generation" placeholder="ឧ. 17"
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Professor Fields --}}
                    <div id="professor-fields" style="display: none;">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            ដេប៉ាតឺម៉ង់ <span class="text-red-500">*</span>
                        </label>
                        <select name="department_id" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">ជ្រើសរើសដេប៉ាតឺម៉ង់</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name_km }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- File Upload --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            ឯកសារ <span class="text-red-500">*</span>
                        </label>
                        <div id="drop-zone"
                            class="relative border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center cursor-pointer transition-all duration-200 hover:border-blue-400 hover:bg-blue-50/30">
                            <input type="file" name="import_file" accept=".xlsx,.xls,.csv" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                onchange="handleFileSelect(this)">
                            <div id="drop-zone-content">
                                <div class="bg-blue-100 text-blue-500 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                </div>
                                <p class="font-bold text-gray-700">អូសឯកសារមកទីនេះ</p>
                                <p class="text-sm text-gray-500 mt-1">ឬចុចដើម្បីជ្រើសរើសឯកសារ</p>
                                <p class="text-xs text-gray-400 mt-3">គាំទ្រ: CSV, XLS, XLSX • ទំហំអតិបរមា: 10MB</p>
                            </div>
                            <div id="file-preview" class="hidden">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="bg-emerald-100 text-emerald-600 p-3 rounded-xl">
                                        <i class="fas fa-file-alt text-xl"></i>
                                    </div>
                                    <div class="text-left">
                                        <p id="file-name" class="font-bold text-gray-900"></p>
                                        <p id="file-size" class="text-xs text-gray-500"></p>
                                    </div>
                                    <button type="button" onclick="removeFile()" class="text-red-400 hover:text-red-600 ml-4">
                                        <i class="fas fa-times-circle text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end mt-6 pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-file-import"></i> នាំចូល
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes toast-progress {
            from { width: 100%; }
            to { width: 0%; }
        }
        .animate-toast-progress {
            animation: toast-progress 5s linear forwards;
        }
    </style>

    <script>
        document.querySelector('select[name="role"]').addEventListener('change', function() {
            const studentFields = document.getElementById('student-fields');
            const professorFields = document.getElementById('professor-fields');
            if (this.value === 'student') {
                studentFields.style.display = 'block';
                professorFields.style.display = 'none';
            } else {
                studentFields.style.display = 'none';
                professorFields.style.display = 'block';
            }
        });

        const dropZone = document.getElementById('drop-zone');
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-blue-400', 'bg-blue-50');
        });
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
            const file = e.dataTransfer.files[0];
            if (file) {
                const input = document.querySelector('input[name="import_file"]');
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
                showFilePreview(file);
            }
        });

        function handleFileSelect(input) {
            if (input.files.length > 0) {
                showFilePreview(input.files[0]);
            }
        }

        function showFilePreview(file) {
            document.getElementById('drop-zone-content').classList.add('hidden');
            document.getElementById('file-preview').classList.remove('hidden');
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = formatFileSize(file.size);
            dropZone.classList.add('border-emerald-300', 'bg-emerald-50/30');
        }

        function removeFile() {
            document.querySelector('input[name="import_file"]').value = '';
            document.getElementById('drop-zone-content').classList.remove('hidden');
            document.getElementById('file-preview').classList.add('hidden');
            dropZone.classList.remove('border-emerald-300', 'bg-emerald-50/30');
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }
    </script>
</x-app-layout>
