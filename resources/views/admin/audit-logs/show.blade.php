<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('audit_log_details') }}
            </h2>
            <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('go_back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">{{ __('general_info') }}</h3>
                            <dl class="space-y-2">
                                <div class="flex">
                                    <dt class="w-32 font-medium text-gray-500">{{ __('users') }}:</dt>
                                    <dd>{{ $auditLog->user->name ?? 'System' }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-32 font-medium text-gray-500">{{ __('actions_2') }}:</dt>
                                    <dd>
                                        @if ($auditLog->action === 'create')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                បង្កើត
                                            </span>
                                        @elseif ($auditLog->action === 'update')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                កែប្រែ
                                            </span>
                                        @elseif ($auditLog->action === 'delete')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                លុប
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                                {{ $auditLog->action }}
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-32 font-medium text-gray-500">{{ __('date') }}:</dt>
                                    <dd>{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-32 font-medium text-gray-500">{{ __('IP Address') }}:</dt>
                                    <dd>{{ $auditLog->ip_address ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-2">{{ __('log_details') }}</h3>
                            <dl class="space-y-2">
                                <div class="flex">
                                    <dt class="w-32 font-medium text-gray-500">{{ __('type') }}:</dt>
                                    <dd>{{ class_basename($auditLog->auditable_type) ?? '-' }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-32 font-medium text-gray-500">{{ __('ID') }}:</dt>
                                    <dd>{{ $auditLog->auditable_id ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if ($auditLog->description)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('description') }}</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $auditLog->description }}</p>
                        </div>
                    @endif

                    @if ($auditLog->old_values)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('previous_values') }}</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-4 overflow-x-auto">
                                <pre class="text-sm text-gray-800 dark:text-gray-200">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif

                    @if ($auditLog->new_values)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('new_data') }}</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-4 overflow-x-auto">
                                <pre class="text-sm text-gray-800 dark:text-gray-200">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
