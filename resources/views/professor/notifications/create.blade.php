<x-app-layout>
<x-slot name="header">
<h2 class="font-bold text-3xl text-gray-900 leading-tight flex items-center gap-2">
📢 {{ __('create_new_notification') }}
</h2>
</x-slot>

<div class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-100">
            
            <h2 class="text-2xl font-extrabold text-gray-800 mb-6 flex items-center gap-2">
             {{ __('new_notification') }}
            </h2>

            @if (session('success'))
                <div class="bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('professor.notifications.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="title" class="block text-gray-700 text-sm font-semibold mb-2">
                        {{ __('title') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div>
                    <label for="course_offering_id" class="block text-gray-700 text-sm font-semibold mb-2">
                        {{ __('course_offering') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="course_offering_id" id="course_offering_id" required
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">-- {{ __('select_course') }} --</option>
                        @foreach($courseOfferings as $offering)
                            <option value="{{ $offering->id }}">
                                {{ $offering->course?->title_km ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-gray-700 text-sm font-semibold">
                            {{ __('recipients') }} <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="toggle-select-all"
                                class="text-sm font-semibold text-green-600 hover:text-green-800 hidden">
                            {{ __('select_all') }}
                        </button>
                    </div>
                    <div id="students-list" class="space-y-2 border rounded-lg p-3 h-64 overflow-y-auto bg-gray-50">
                        <p class="text-gray-500 text-sm">{{ __('select_offering_to_see_students') }}</p>
                    </div>
                </div>

                <div>
                    <label for="message" class="block text-gray-700 text-sm font-semibold mb-2">
                        {{ __('message_content') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" id="message" rows="6" required
                              class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">{{ old('message') }}</textarea>
                </div>
{{-- 
                <div class="flex items-center justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-8 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-transform transform hover:scale-105">
                     
                    </button>
                </div> --}}
                      <div class="flex items-center justify-between mt-6">
                        <a wire:navigate href="{{ route('professor.notifications.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-full font-semibold text-xs text-gray-700 uppercase tracking-widest hover:text-gray-900 transition ease-in-out duration-150">
                            <i class="fas fa-arrow-left mr-2"></i> {{ __('back_2') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-600 border border-transparent rounded-full font-semibold text-sm text-white uppercase tracking-widest hover:from-green-700 hover:to-green-700 active:from-green-800 active:to-green-800 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                               <i class="fas fa-paper-plane mr-2"></i> {{ __('send_notification_2') }}
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('course_offering_id').addEventListener('change', function() {
    let courseId = this.value;
    let container = document.getElementById('students-list');
    let toggleBtn = document.getElementById('toggle-select-all');
    container.innerHTML = '';
    toggleBtn.classList.add('hidden');

    if (!courseId) {
        container.innerHTML = '<p class="text-gray-500 text-sm">{{ __("khmer_fba6be422e") }}</p>';
        return;
    }

    container.innerHTML = '<p class="text-gray-500 text-sm">{{ __("loading_3") }}</p>';

    fetch(`/professor/course-offerings/${courseId}/students-data`)
        .then(response => response.json())
        .then(data => {
            container.innerHTML = '';

            // ✅ ប្រាកដថា response ជា array
            if (!Array.isArray(data)) {
                container.innerHTML = `<p class="text-red-500 text-sm">${data.error || '{{ __("problem_fetching_data_2") }}'}</p>`;
                return;
            }

            if (data.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-sm">{{ __("no_students") }}</p>';
                return;
            }

            toggleBtn.classList.remove('hidden');
            data.forEach(student => {
                container.innerHTML += `
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="recipient_ids[]" value="${student.id}" class="rounded">
                            <span>${student.name} </span>
                        </label>
                    </div>
                `;
            });
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<p class="text-red-500 text-sm">{{ __("problem_fetching_data_2") }}</p>';
        });
});

// ✅ Select / Unselect all
document.getElementById('toggle-select-all').addEventListener('click', function() {
    let checkboxes = document.querySelectorAll('#students-list input[type="checkbox"]');
    let allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    this.textContent = allChecked ? '{{ __("khmer_68073503b6") }}' : '{{ __("khmer_a9860ef48c") }}';
});
</script>

</x-app-layout>
