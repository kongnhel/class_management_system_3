<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-900 leading-tight flex items-center gap-2">
            📢 {{ __('ការជូនដំណឹងរបស់ខ្ញុំ') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen" x-data="notificationHandler()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-6 lg:p-8 border border-gray-100">

                {{-- ✅ Success Alert --}}
                <div x-show="successMessage"
                     role="alert"
                     class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3"
                     x-text="successMessage"
                     style="display: none;">
                </div>

                {{-- ✅ Header + Actions --}}
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-2xl font-extrabold text-gray-800">
                        {{ __('បញ្ជីការជូនដំណឹង') }}
                    </h3>
                    <div class="flex items-center space-x-3">
                        <button @click="filter = 'all'"
                                :class="{ 'bg-green-600 text-white': filter === 'all', 'bg-gray-200 text-gray-700': filter !== 'all' }"
                                class="px-4 py-2 rounded-full font-semibold transition">
                            {{ __('ទាំងអស់') }}
                        </button>
                        <button @click="filter = 'unread'"
                                :class="{ 'bg-green-600 text-white': filter === 'unread', 'bg-gray-200 text-gray-700': filter !== 'unread' }"
                                class="px-4 py-2 rounded-full font-semibold transition">
                            {{ __('មិនទាន់អាន') }}
                        </button>
                        {{-- ✅ Bulk mark all as read --}}
                        <button @click="markAllAsRead"
                                class="px-4 py-2 rounded-full bg-green-100 text-green-700 font-semibold hover:bg-green-200 transition">
                            {{ __('សម្គាល់ថាអានទាំងអស់') }}
                        </button>
                    </div>
                </div>

                {{-- ✅ Notifications List --}}
                <div class="space-y-4">
                    @forelse ($notifications as $notification)
                        <div x-show="shouldShow($el)"
                             data-id="{{ $notification->id }}"
                             data-read="{{ $notification->read_at ? 'true' : 'false' }}"
                             class="p-5 rounded-xl border transition-all duration-300 flex items-start gap-4 @if(!$notification->read_at) bg-green-50 border-green-200 shadow-sm @else bg-white border-gray-200 @endif">

                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-200 text-green-600 flex items-center justify-center">
                                <i class="fas fa-bell"></i>
                            </div>

                            <div class="flex-grow">
                                <p class="font-bold text-gray-800 text-lg">
                                    {{ $notification->data['title'] ?? __('ការជូនដំណឹង') }}
                                </p>
                                <p class="text-gray-600 mt-1">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <div class="text-xs text-gray-400 mt-2 flex items-center justify-between">
                                    <span>
                                        {{ __('ដោយ៖') }} <strong>{{ $notification->data['from_user_name'] ?? 'System' }}</strong>
                                        - {{ $notification->created_at->locale('km')->diffForHumans() }}
                                    </span>
                                    @if (!$notification->read_at)
                                        <button @click="markAsRead('{{ $notification->id }}', $el)"
                                                class="text-sm font-semibold text-green-600 hover:text-green-800 hover:underline">
                                            {{ __('សម្គាល់ថាបានអាន') }}
                                        </button>
                                    @else
                                         <span class="text-sm text-green-600 font-semibold flex items-center gap-1">
                                             <i class="fas fa-check-circle"></i> {{ __('បានអាន') }}
                                         </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 text-gray-500 bg-gray-50 rounded-2xl shadow-inner">
                            <i class="fas fa-bell-slash text-5xl text-gray-300"></i>
                            <p class="text-xl mt-4 font-semibold">{{ __('មិនមានការជូនដំណឹងថ្មីសម្រាប់អ្នកទេ។') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>

            </div>
        </div>
    </div>

    {{-- ✅ AlpineJS Logic --}}
    <script>
        function notificationHandler() {
            return {
                filter: 'all',
                successMessage: '',

                shouldShow(element) {
                    if (this.filter === 'all') return true;
                    return element.dataset.read === 'false';
                },

                markAsRead(notificationId, element) {
                    fetch('{{ route("student.notifications.markAsRead") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ id: notificationId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.updateNotificationElement(element);
                            this.successMessage = 'ការជូនដំណឹងត្រូវបានសម្គាល់ថាបានអាន។';
                            setTimeout(() => this.successMessage = '', 3000);
                        }
                    });
                },

                markAllAsRead() {
                    const unreadElements = document.querySelectorAll('[data-read="false"]');
                    unreadElements.forEach(el => {
                        const id = el.dataset.id;
                        this.markAsRead(id, el);
                    });
                    this.successMessage = 'បានសម្គាល់ថាការជូនដំណឹងទាំងអស់អានហើយ។';
                    setTimeout(() => this.successMessage = '', 3000);
                },

                updateNotificationElement(element) {
                    element.classList.remove('bg-green-50', 'border-green-200', 'shadow-sm');
                    element.classList.add('bg-white', 'border-gray-200');
                    element.dataset.read = 'true';

                    const button = element.querySelector('button');
                    if (button) {
                        const readStatus = document.createElement('span');
                        readStatus.className = 'text-sm text-green-600 font-semibold flex items-center gap-1';
                        readStatus.innerHTML = '<i class="fas fa-check-circle"></i> បានអាន';
                        button.parentNode.replaceChild(readStatus, button);
                    }
                }
            }
        }
    </script>
</x-app-layout>
{{-- student.notifications --}}
