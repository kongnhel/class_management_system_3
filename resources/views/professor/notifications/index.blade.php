<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-900 leading-tight flex items-center gap-2">
            📢 {{ __('ការជូនដំណឹង') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen"
         x-data="{ 
            activeTab: 'received',
            showRecipientsModal: false, 
            recipients: [], 
            notificationTitle: '',
            showDeleteModal: false,
            deleteRoute: '',
            itemTitle: '',
            filter: 'all',
            successMessage: ''
         }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl p-6 lg:p-8 border border-gray-100">

                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 md:p-5 rounded-xl mb-6 shadow-sm flex items-center animate-bounce" role="alert">
                        <i class="fas fa-check-circle mr-3 text-green-500 text-xl"></i>
                        <span class="font-bold text-sm md:text-lg">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="flex items-center space-x-2">
                        <button @click="activeTab = 'received'" :class="{ 'bg-green-600 text-white': activeTab === 'received', 'bg-gray-200 text-gray-700': activeTab !== 'received' }" class="px-4 py-2 rounded-full font-semibold transition">
                            {{ __('ការជូនដំណឹងដែលបានទទួល') }}
                            @php
                                $unreadReceived = $receivedNotifications->where('is_read', false)->count();
                            @endphp
                            @if($unreadReceived > 0)
                                <span class="ml-1 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $unreadReceived }}</span>
                            @endif
                        </button>
                        <button @click="activeTab = 'sent'" :class="{ 'bg-green-600 text-white': activeTab === 'sent', 'bg-gray-200 text-gray-700': activeTab !== 'sent' }" class="px-4 py-2 rounded-full font-semibold transition">
                            {{ __('ការជូនដំណឹងដែលបានផ្ញើ') }}
                        </button>
                    </div>
                    <a href="{{ route('professor.notifications.create') }}"
                       class="inline-flex items-center w-full justify-center md:w-auto px-6 py-3 bg-green-600 text-white font-bold rounded-full shadow-lg hover:bg-green-700 transition duration-300 transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i> {{ __('បង្កើតការជូនដំណឹងថ្មី') }}
                    </a>
                </div>

                {{-- Received Notifications Tab --}}
                <div x-show="activeTab === 'received'">
                    <div class="flex items-center space-x-2 mb-4">
                        <button @click="filter = 'all'" :class="{ 'bg-green-100 text-green-700': filter === 'all', 'bg-gray-100 text-gray-600': filter !== 'all' }" class="px-3 py-1.5 rounded-full text-sm font-semibold transition">
                            {{ __('ទាំងអស់') }}
                        </button>
                        <button @click="filter = 'unread'" :class="{ 'bg-green-100 text-green-700': filter === 'unread', 'bg-gray-100 text-gray-600': filter !== 'unread' }" class="px-3 py-1.5 rounded-full text-sm font-semibold transition">
                            {{ __('មិនទាន់អាន') }}
                        </button>
                        <button onclick="markAllReceivedAsRead()" class="px-3 py-1.5 rounded-full bg-green-100 text-green-700 text-sm font-semibold hover:bg-green-200 transition">
                            {{ __('សម្គាល់ថាអានទាំងអស់') }}
                        </button>
                    </div>

                    @if ($receivedNotifications->isEmpty())
                        <div class="text-center py-16 text-gray-500 bg-gray-50 rounded-2xl shadow-inner">
                            <i class="fas fa-bell-slash text-5xl text-gray-300"></i>
                            <p class="text-xl mt-4 font-semibold">{{ __('មិនមានការជូនដំណឹងថ្មីសម្រាប់អ្នកទេ។') }}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($receivedNotifications as $item)
                                <div id="notification-item-{{ $item->type }}-{{ $item->id }}"
                                     data-read="{{ $item->is_read ? 'true' : 'false' }}"
                                     x-show="filter === 'all' || (filter === 'unread' && $el.dataset.read === 'false')"
                                     class="p-4 rounded-xl border transition-all duration-300 flex items-start gap-4 @if(!$item->is_read) bg-green-50 border-green-200 shadow-sm @else bg-white border-gray-200 @endif">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full @if($item->type === 'announcement') bg-blue-100 text-blue-600 @else bg-green-100 text-green-600 @endif flex items-center justify-center">
                                        @if($item->type === 'announcement')
                                            <i class="fas fa-bullhorn"></i>
                                        @else
                                            <i class="fas fa-bell"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow min-w-0">
                                        <p class="font-bold text-gray-800 text-base">{{ $item->title }}</p>
                                        <p class="text-gray-600 mt-1 text-sm">{{ \Illuminate\Support\Str::limit($item->content, 120) }}</p>
                                        <div class="text-xs text-gray-400 mt-2 flex items-center justify-between">
                                            <span>{{ __('ដោយ៖') }} <strong>{{ $item->from_user_name }}</strong> - {{ $item->created_at->locale('km')->diffForHumans() }}</span>
                                            <div class="flex items-center gap-2">
                                                @if($item->type === 'announcement')
                                                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">{{ __('ការជូនដំណឹង') }}</span>
                                                @endif
                                                @if(!$item->is_read)
                                                    @if($item->type === 'notification')
                                                        <button onclick="markNotificationAsRead({{ $item->id }})" class="text-xs font-semibold text-green-600 hover:text-green-800 hover:underline">
                                                            {{ __('សម្គាល់ថាបានអាន') }}
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="text-xs text-green-600 font-semibold flex items-center gap-1">
                                                        <i class="fas fa-check-circle"></i> {{ __('បានអាន') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Sent Notifications Tab --}}
                <div x-show="activeTab === 'sent'">
                    @if ($sentNotifications->isEmpty())
                        <div class="text-center py-16 text-gray-500 bg-gray-50 rounded-2xl shadow-inner">
                            <i class="fas fa-bell-slash text-5xl text-gray-300"></i>
                            <p class="text-xl mt-4 font-semibold">{{ __('អ្នកមិនទាន់បានផ្ញើការជូនដំណឹងណាមួយនៅឡើយទេ។') }}</p>
                        </div>
                    @else
                        <div class="hidden md:block">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border rounded-lg">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/4">{{ __('ចំណងជើង') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/3">{{ __('សារ') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/6">{{ __('អ្នកទទួល') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/6">{{ __('បានផ្ញើនៅ') }}</th>
                                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/12">{{ __('សកម្មភាព') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @php
                                            $allRecipients = \App\Models\User::whereIn('id', $sentNotifications->flatMap(fn($n) => $n->data['recipient_ids'] ?? []))->pluck('name', 'id');
                                        @endphp

                                        @foreach ($sentNotifications as $notification)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                                    {{ \Illuminate\Support\Str::limit($notification->data['title'] ?? 'N/A', 40) }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">
                                                    {{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 60) }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    @php
                                                        $recipientIds = $notification->data['recipient_ids'] ?? [];
                                                        $recipientNames = collect($recipientIds)->map(fn($id) => $allRecipients[$id] ?? 'Unknown User')->all();
                                                    @endphp
                                                    <button @click="recipients = {{ json_encode($recipientNames) }}; notificationTitle = '{{ addslashes($notification->data['title'] ?? '') }}'; showRecipientsModal = true;" class="text-green-600 hover:underline font-semibold">
                                                        {{ count($recipientIds) }} {{ __('និស្សិត') }}
                                                    </button>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    {{ $notification->created_at->locale('km')->diffForHumans() }}
                                                </td>
                                                <td class="px-6 py-4 text-right text-sm font-medium">
                                                    <button type="button" 
                                                        @click="showDeleteModal = true; deleteRoute = '{{ route('professor.notifications.destroy', $notification->id) }}'; itemTitle = '{{ addslashes($notification->data['title'] ?? '') }}'"
                                                        class="text-red-600 hover:text-red-900 font-semibold transition duration-200">
                                                        {{ __('លុប') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="md:hidden space-y-4">
                            @foreach ($sentNotifications as $notification)
                                <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-4 transition duration-300 hover:shadow-xl">
                                    <div class="border-b pb-3 mb-3">
                                        <h4 class="text-lg font-extrabold text-green-700 mb-1">{{ $notification->data['title'] ?? 'N/A' }}</h4>
                                        <p class="text-xs text-gray-500">{{ __('បានផ្ញើ:') }} {{ $notification->created_at->locale('km')->diffForHumans() }}</p>
                                    </div>
                                    <div class="space-y-3 text-sm">
                                        <p class="text-gray-600">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 100) }}</p>
                                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                            <p class="font-semibold text-gray-700">{{ __('អ្នកទទួល') }}:</p>
                                            <button @click="recipients = {{ json_encode($allRecipients->filter(fn($id) => in_array($id, $notification->data['recipient_ids'] ?? []))->all()) }}; notificationTitle = '{{ addslashes($notification->data['title'] ?? '') }}'; showRecipientsModal = true;" class="text-sm text-green-600 font-bold">
                                                {{ count($notification->data['recipient_ids'] ?? []) }} {{ __('និស្សិត') }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-100 text-right">
                                        <button type="button" 
                                            @click="showDeleteModal = true; deleteRoute = '{{ route('professor.notifications.destroy', $notification->id) }}'; itemTitle = '{{ addslashes($notification->data['title'] ?? '') }}'"
                                            class="px-3 py-1 text-xs bg-red-100 text-red-600 font-bold rounded-lg">
                                            <i class="fas fa-trash-alt mr-1"></i> {{ __('លុបការជូនដំណឹង') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recipients Modal --}}
        <div x-show="showRecipientsModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div @click="showRecipientsModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 border-b pb-3 mb-4" x-text="'{{ __('អ្នកទទួលសម្រាប់:') }} ' + notificationTitle"></h3>
                    <ul class="space-y-2 max-h-80 overflow-y-auto p-2">
                        <template x-for="recipient in recipients" :key="recipient">
                            <li class="bg-green-50 p-3 rounded-md text-gray-800 flex items-center">
                                <i class="fas fa-user-circle mr-3 text-green-400"></i>
                                <span x-text="recipient"></span>
                            </li>
                        </template>
                    </ul>
                    <div class="mt-6 text-right">
                        <button @click="showRecipientsModal = false" class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">{{ __('បិទ') }}</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div x-show="showDeleteModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"></div>

                <div x-show="showDeleteModal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:translate-y-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:translate-y-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full p-8">
                    
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-6">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('បញ្ជាក់ការលុប') }}</h3>
                    <p class="text-gray-600 mb-1" x-text="'{{ __('តើអ្នកពិតជាចង់លុបការជូនដំណឹង') }} &quot;' + itemTitle + '&quot; ' + '{{ __('មែនទេ?') }}'"></p>
                    <p class="text-sm text-red-500 font-medium bg-red-50 p-3 rounded-lg mt-4">
                        <i class="fas fa-info-circle mr-1"></i> {{ __('សកម្មភាពនេះនឹងលុបការជូនដំណឹងចេញពីសិស្សទាំងអស់ដែលបានទទួល ហើយមិនអាចត្រឡប់ក្រោយវិញបានទេ។') }}
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button @click="showDeleteModal = false" type="button" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">
                            {{ __('បោះបង់') }}
                        </button>
                        
                        <form :action="deleteRoute" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 shadow-lg shadow-red-200 transition">
                                {{ __('លុបចោល') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function markNotificationAsRead(id) {
            fetch('/professor/notifications/' + id + '/mark-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const item = document.getElementById('notification-item-notification-' + id);
                    if (item) {
                        item.classList.remove('bg-green-50', 'border-green-200', 'shadow-sm');
                        item.classList.add('bg-white', 'border-gray-200');
                        item.dataset.read = 'true';

                        const button = item.querySelector('button[onclick*="markNotificationAsRead"]');
                        if (button) {
                            const readStatus = document.createElement('span');
                            readStatus.className = 'text-xs text-green-600 font-semibold flex items-center gap-1';
                            readStatus.innerHTML = '<i class="fas fa-check-circle"></i> បានអាន';
                            button.parentNode.replaceChild(readStatus, button);
                        }
                    }
                }
            });
        }

        function markAllReceivedAsRead() {
            fetch('/professor/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('[data-read="false"]').forEach(el => {
                        el.classList.remove('bg-green-50', 'border-green-200', 'shadow-sm');
                        el.classList.add('bg-white', 'border-gray-200');
                        el.dataset.read = 'true';

                        const button = el.querySelector('button[onclick*="markNotificationAsRead"]');
                        if (button) {
                            const readStatus = document.createElement('span');
                            readStatus.className = 'text-xs text-green-600 font-semibold flex items-center gap-1';
                            readStatus.innerHTML = '<i class="fas fa-check-circle"></i> បានអាន';
                            button.parentNode.replaceChild(readStatus, button);
                        }
                    });
                }
            });
        }
    </script>
</x-app-layout>