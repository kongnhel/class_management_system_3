<x-app-layout>
    <div class="min-h-screen bg-gray-100 font-sans text-gray-900">
        <div class="bg-slate-900 text-white pb-32 pt-12 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold tracking-tight text-white">ការកំណត់ប្រព័ន្ធ</h2>
                <p class="text-slate-400 mt-2 max-w-2xl text-sm leading-relaxed">គ្រប់គ្រងការកំណត់ទូទៅនៃប្រព័ន្ធគ្រប់គ្រងសិក្សា</p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12 relative z-10">
            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6">
                {{ session('success') }}
            </div>
            @endif

            @php
                $settingMap = [];
                foreach ($settings as $group => $items) {
                    foreach ($items as $item) {
                        $settingMap[$item->key] = $item->value;
                    }
                }
            @endphp

            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf @method('PUT')

                {{-- Grading Settings --}}
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-calculator text-blue-500"></i> ការកំណត់ការដាក់ពិន្ទុ
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">អវត្តមាន (ច្រើនជាង X ដង = -១ ពិន្ទុ)</label>
                            <input type="number" name="settings[absence_threshold]" value="{{ $settingMap['absence_threshold'] ?? 2 }}" min="1" max="10"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">សិទ្ធិអនុគ្រោះ (ច្រើនជាង X ដង = -១ ពិន្ទុ)</label>
                            <input type="number" name="settings[permission_threshold]" value="{{ $settingMap['permission_threshold'] ?? 4 }}" min="1" max="10"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ទម្ងន់អវត្តមាន (%)</label>
                            <input type="number" name="settings[attendance_weight]" value="{{ $settingMap['attendance_weight'] ?? 15 }}" min="0" max="100"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ទម្ងន់ប្រលងពាក់កណ្តាលឆមាស (%)</label>
                            <input type="number" name="settings[midterm_weight]" value="{{ $settingMap['midterm_weight'] ?? 15 }}" min="0" max="100"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ទម្ងន់កិច្ចការក្រុម (%)</label>
                            <input type="number" name="settings[group_assignment_weight]" value="{{ $settingMap['group_assignment_weight'] ?? 20 }}" min="0" max="100"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ទម្ងន់ប្រលងផ្ទាល់ (%)</label>
                            <input type="number" name="settings[final_exam_weight]" value="{{ $settingMap['final_exam_weight'] ?? 50 }}" min="0" max="100"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                {{-- Enrollment Settings --}}
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-plus text-emerald-500"></i> ការកំណត់ការចុះឈ្មោះ
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="hidden" name="settings[self_enrollment_enabled]" value="0">
                            <input type="checkbox" name="settings[self_enrollment_enabled]" value="1" 
                                {{ ($settingMap['self_enrollment_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label class="ml-2 block text-sm text-gray-700">អនុញ្ញាតឱ្យសិស្សចុះឈ្មោះដោយខ្លួនឯង</label>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">សមត្ថភាពអតិបរមាក្នុងមុខវិជ្ជា</label>
                            <input type="number" name="settings[max_enrollment_per_course]" value="{{ $settingMap['max_enrollment_per_course'] ?? 50 }}" min="1" max="200"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                {{-- Registration Settings --}}
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-graduate text-amber-500"></i> ការកំណត់ការចុះឈ្មោះគណនី
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="hidden" name="settings[registration_open]" value="0">
                            <input type="checkbox" name="settings[registration_open]" value="1" 
                                {{ ($settingMap['registration_open'] ?? '1') == '1' ? 'checked' : '' }}
                                id="registration_open"
                                class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <label for="registration_open" class="ml-2 block text-sm font-bold text-gray-700">បើកការចុះឈ្មោះ</label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">ថ្ងៃចាប់ផ្តើមចុះឈ្មោះ</label>
                                <input type="date" name="settings[registration_start]" value="{{ $settingMap['registration_start'] ?? now()->format('Y-m-d') }}"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">ថ្ងៃបញ្ចប់ការចុះឈ្មោះ</label>
                                <input type="date" name="settings[registration_end]" value="{{ $settingMap['registration_end'] ?? now()->addMonth()->format('Y-m-d') }}"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">សូមបញ្ជាក់កាលបរិច្ឆេទចាប់ផ្តើម និងបញ្ចប់ដើម្បីគ្រប់គ្រងរយៈពេលចុះឈ្មោះ។ ប្រសិនបើបិទ ទំព័រចុះឈ្មោះនឹងមិនអាចប្រើប្រាស់បាន។</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold shadow-lg transition-all">
                        រក្សាទុកការកំណត់
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>