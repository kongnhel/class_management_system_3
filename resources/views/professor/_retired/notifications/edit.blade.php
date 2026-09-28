<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('edit_notification') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form action="{{ route('professor.notifications.update', $notification->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label for="recipients" class="block text-sm font-medium text-gray-700">{{ __('recipients') }}</label>
                        <div class="mt-1 p-3 bg-gray-100 rounded-md">
                            @if ($recipients->isEmpty())
                                <p class="text-gray-500">{{ __('no_recipients') }}</p>
                            @else
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($recipients as $recipient)
                                        <li class="text-sm text-gray-900">{{ $recipient->name }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-500">{{ __('edit_recipients_above') }}</p>
                    </div>

                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-700">{{ __('message') }}</label>
                        <textarea name="message" id="message" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50" required>{{ old('message', $message) }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
