<x-app-layout>
    <div class="min-h-screen bg-gray-100 font-sans text-gray-900">
        <div class="bg-slate-900 text-white pb-32 pt-12 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold tracking-tight text-white">នាំចូលទិន្នន័យ</h2>
                <p class="text-slate-400 mt-2 max-w-2xl text-sm leading-relaxed">នាំចូលសិស្ស ឬសាស្ត្រាចារ្យពីឯកសារ Excel</p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-10">
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6">
                {{ session('success') }}
                @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="mt-2 text-sm">
                    <strong>Errors:</strong>
                    <ul class="list-disc list-inside mt-1">
                        @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-download text-blue-500"></i> ទាញយក template
                </h3>
                <p class="text-gray-600 mb-4">ទាញយក template សម្រាប់បំពេញទិន្នន័យមុនពេលនាំចូល</p>
                <a href="{{ route('admin.import.template') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold shadow transition-all">
                    <i class="fas fa-file-csv"></i> ទាញយក CSV Template
                </a>
            </div>

            <form action="{{ route('admin.import.users') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-xl border border-gray-100 p-6">
                @csrf
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-upload text-emerald-500"></i> នាំចូលទិន្នន័យ
                </h3>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">ប្រភេទអ្នកប្រើប្រាស់ *</label>
                        <select name="role" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="student">សិស្ស</option>
                            <option value="professor">សាស្ត្រាចារ្យ</option>
                        </select>
                    </div>

                    <div id="student-fields">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">កម្មវិធីសិក្សា *</label>
                                <select name="program_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">ជ្រើសរើសកម្មវិធីសិក្សា</option>
                                    @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name_km }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">ជំនាន់</label>
                                <input type="text" name="generation" placeholder="ឧ. 17" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div id="professor-fields" style="display: none;">
                        <label class="block text-sm font-bold text-gray-700 mb-2">ដេប៉ាតឺម៉ង់ *</label>
                        <select name="department_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">ជ្រើសរើសដេប៉ាតឺម៉ង់</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name_km }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">ឯកសារ (CSV/Excel) *</label>
                        <input type="file" name="import_file" accept=".xlsx,.xls,.csv" required
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">ទំហំអតិបរមា: 10MB</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold shadow-lg transition-all">
                        <i class="fas fa-upload mr-2"></i> នាំចូល
                    </button>
                </div>
            </form>
        </div>
    </div>

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
    </script>
</x-app-layout>
