<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.import.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 leading-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600">
                        <i class="fas fa-check-circle"></i>
                    </span>
                    {{ __('លទ្ធផលនាំចូល') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Result Summary --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
                @if($imported > 0 && $skipped === 0)
                    <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check-circle text-emerald-500 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">នាំចូលជោគជ័យ!</h3>
                    <p class="text-gray-500">បាននាំចូល <span class="font-bold text-emerald-600">{{ $imported }}</span> នាក់ដោយជោគជ័យ។</p>
                @elseif($imported > 0 && $skipped > 0)
                    <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-exclamation-triangle text-amber-500 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">នាំចូលជាមួយកំហុស</h3>
                    <p class="text-gray-500">បាននាំចូល <span class="font-bold text-emerald-600">{{ $imported }}</span> នាក់ និងរំលង <span class="font-bold text-red-600">{{ $skipped }}</span> នាក់។</p>
                @else
                    <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-times-circle text-red-500 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">នាំចូលមិនជោគជ័យ</h3>
                    <p class="text-gray-500">មិនមានទិន្នន័យត្រូវបាននាំចូលទេ។</p>
                @endif
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 text-center">
                    <div class="text-3xl font-bold text-emerald-600">{{ $imported }}</div>
                    <p class="text-sm text-gray-500 mt-1">នាំចូលបាន</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 text-center">
                    <div class="text-3xl font-bold text-red-600">{{ $skipped }}</div>
                    <p class="text-sm text-gray-500 mt-1">រំលង</p>
                </div>
            </div>

            {{-- Errors List --}}
            @if($errors->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        កំហុស ({{ count($errors) }})
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                    @foreach($errors as $error)
                    <div class="px-6 py-3 text-sm text-red-600">
                        {{ $error }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Default Password Notice --}}
            @if($imported > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-key text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-800">ពាក្យសម្ងាត់លំនាំដើម</h4>
                        <p class="text-sm text-blue-600 mt-1">សិស្សទាំងអស់មានពាក្យសម្ងាត់លំនាំដើម៖ <code class="bg-blue-100 px-2 py-0.5 rounded font-mono">password123</code></p>
                        <p class="text-xs text-blue-500 mt-1">សូមផ្តល់ព័ត៌មាននេះដល់សិស្សដើម្បីចូលប្រើប្រាស់ប្រព័ន្ធលើកដំបូង។</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('admin.import.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    <i class="fas fa-plus"></i> នាំចូលម្តងទៀត
                </a>
                @if($imported > 0)
                <a href="{{ route('admin.manage-users', ['tab' => $settings['role'] === 'student' ? 'students' : 'professors']) }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-600 rounded-xl font-bold text-white hover:from-emerald-700 hover:to-emerald-700 transition shadow-md">
                    <i class="fas fa-eye"></i> មើលអ្នកប្រើប្រាស់
                </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
