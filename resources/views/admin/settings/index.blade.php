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

            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf @method('PUT')

                {{-- Grading Settings --}}
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-calculator text-blue-500"></i> ការកំណត់ការដាក់ពិន្ទុ
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">អវត្តមាន (ច្រើនជាង ២ ដង = -១ ពិន្ទុ)</label>
                            <input type="number" name="settings[absence_threshold]" value="{{ $settings['grading'][0]->value ?? 2 }}" min="1" max="10"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">សិទ្ធិអនុគ្រោះ (ច្រើនជាង ៤ ដង = -១ ពិន្ទុ)</label>
                            <input type="number" name="settings[permission_threshold]" value="{{ $settings['grading'][1]->value ?? 4 }}" min="1" max="10"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ទម្ងន់អវត្តមាន (%)</label>
                            <input type="number" name="settings[attendance_weight]" value="{{ $settings['grading'][2]->value ?? 15 }}" min="0" max="100"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ទម្ងន់ប្រលងពាក់កណ្តាលឆមាស (%)</label>
                            <input type="number" name="settings[midterm_weight]" value="{{ $settings['grading'][3]->value ?? 15 }}" min="0" max="100"
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
                                {{ ($settings['enrollment'][0]->value ?? '1') == '1' ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label class="ml-2 block text-sm text-gray-700">អនុញ្ញាតឱ្យសិស្សចុះឈ្មោះដោយខ្លួនឯង</label>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">សមត្ថភាពអតិបរមាក្នុងមុខវិជ្ជា</label>
                            <input type="number" name="settings[max_enrollment_per_course]" value="{{ $settings['enrollment'][1]->value ?? 50 }}" min="1" max="200"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold shadow-lg transition-all">
                        រក្សាទុកការកំណត់
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
