<x-app-layout>
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-emerald-600 via-emerald-700 to-emerald-800 text-white pb-28 pt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-2xl">
                        <i class="fas fa-file-import text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">នាំចូលទិន្នន័យ</h2>
                        <p class="text-emerald-200 mt-1 text-sm">នាំចូលសិស្ស ឬសាស្ត្រាចារ្យពីឯកសារ Excel</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 pb-12 relative z-10">

            {{-- Validation Errors --}}
            @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="bg-red-100 text-red-600 p-2 rounded-xl flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-red-800">មានកំហុសក្នុងការបំពេញទិន្នន័យ</p>
                        <ul class="mt-2 text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                            <li class="flex items-start gap-1">
                                <span class="text-red-400 mt-0.5">•</span>
                                <span>{{ $error }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- Import Errors Detail (from successful import with skipped rows) --}}
            @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="bg-amber-100 text-amber-600 p-2 rounded-xl flex-shrink-0">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-amber-800">មានកំហុស {{ count(session('import_errors')) }} កន្លែង</p>
                        <ul class="mt-2 text-xs text-amber-700 space-y-1 max-h-32 overflow-y-auto">
                            @foreach(session('import_errors') as $error)
                            <li class="flex items-start gap-1">
                                <span class="text-amber-400 mt-0.5">•</span>
                                <span>{{ $error }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- Steps Instructions --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                    <div class="bg-emerald-100 text-emerald-600 p-2 rounded-xl">
                        <i class="fas fa-list-ol text-sm"></i>
                    </div>
                    ជំហានក្នុងការនាំចូល
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="flex items-start gap-3 bg-gray-50 rounded-xl p-4">
                        <div class="bg-emerald-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">1</div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">ទាញយក Template</p>
                            <p class="text-xs text-gray-500 mt-1">ទាញយកម៉ាស៊ីនត្រជាក់ XLSX</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-50 rounded-xl p-4">
                        <div class="bg-emerald-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">2</div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">បំពេញឈ្មោះ</p>
                            <p class="text-xs text-gray-500 mt-1">បំពេញឈ្មោះសិស្សក្នុងម៉ាស៊ីនត្រជាក់ (ត្រឹមឈ្មោះបានហើយ)</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-50 rounded-xl p-4">
                        <div class="bg-emerald-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">3</div>
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
                            <i class="fas fa-file-excel text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">ទាញយក Template</h3>
                            <p class="text-sm text-gray-500 mt-0.5">ទាញយកម៉ាស៊ីនត្រជាក់ XLSX សម្រាប់បំពេញឈ្មោះសិស្ស</p>
                            <p class="text-xs text-emerald-600 mt-1"><i class="fas fa-info-circle mr-1"></i> ត្រឹមតែឈ្មោះ (Name) ត្រូវបានទាមទារ • អ៊ីម៉ែលនិងព័ត៌មានផ្សេងៗសូមបំពេញនៅពេលក្រោយ</p>
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
                    <div class="bg-emerald-100 text-emerald-600 p-2 rounded-xl">
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
                        <select name="role" required class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 text-sm">
                            <option value="student" @if(old('role','student')=='student') selected @endif>សិស្ស</option>
                            <option value="professor" @if(old('role')=='professor') selected @endif>សាស្ត្រាចារ្យ</option>
                        </select>
                    </div>

                    {{-- Student Fields --}}
                    <div id="student-fields">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    កម្មវិធីសិក្សា <span class="text-red-500">*</span>
                                </label>
                                <select name="program_id" id="program_id" required class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 text-sm">
                                    <option value="">ជ្រើសរើសកម្មវិធីសិក្សា</option>
                                    @foreach($programs as $program)
                                    <option value="{{ $program->id }}" @if(old('program_id')==$program->id) selected @endif>{{ $program->name_km }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">ជំនាន់</label>
                                <input type="text" name="generation" value="{{ old('generation') }}" placeholder="ឧ. 17"
                                    class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Professor Fields --}}
                    <div id="professor-fields" style="display: none;">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            ដេប៉ាតឺម៉ង់ <span class="text-red-500">*</span>
                        </label>
                        <select name="department_id" id="department_id" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 text-sm">
                            <option value="">ជ្រើសរើសដេប៉ាតឺម៉ង់</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" @if(old('department_id')==$department->id) selected @endif>{{ $department->name_km }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- File Upload --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            ឯកសារ <span class="text-red-500">*</span>
                        </label>
                        <div id="drop-zone"
                            class="relative border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center cursor-pointer transition-all duration-200 hover:border-emerald-400 hover:bg-emerald-50/30">
                            <input type="file" name="import_file" accept=".xlsx,.xls,.csv" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                onchange="handleFileSelect(this)">
                            <div id="drop-zone-content">
                                <div class="bg-emerald-100 text-emerald-500 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                </div>
                                <p class="font-bold text-gray-700">អូសឯកសារមកទីនេះ</p>
                                <p class="text-sm text-gray-500 mt-1">ឬចុចដើម្បីជ្រើសរើសឯកសារ</p>
                                <p class="text-xs text-gray-400 mt-3">គាំទ្រ: CSV, XLS, XLSX • ទំហំអតិបរមា: 10MB • ឈ្មោះជាដាច់ខាត</p>
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
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-600 hover:from-emerald-700 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-file-import"></i> នាំចូល
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const roleSelect = document.querySelector('select[name="role"]');
        const studentFields = document.getElementById('student-fields');
        const professorFields = document.getElementById('professor-fields');
        const programSelect = document.getElementById('program_id');
        const deptSelect = document.getElementById('department_id');

        function toggleFields() {
            if (roleSelect.value === 'student') {
                studentFields.style.display = 'block';
                professorFields.style.display = 'none';
                programSelect.required = true;
                deptSelect.required = false;
            } else {
                studentFields.style.display = 'none';
                professorFields.style.display = 'block';
                programSelect.required = false;
                deptSelect.required = true;
            }
        }

        roleSelect.addEventListener('change', toggleFields);
        toggleFields();

        const dropZone = document.getElementById('drop-zone');
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-emerald-400', 'bg-emerald-50');
        });
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-emerald-400', 'bg-emerald-50');
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-emerald-400', 'bg-emerald-50');
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
