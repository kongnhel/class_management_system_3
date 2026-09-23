<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a wire:navigate href="{{ route('professor.dashboard') }}"
               class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50 transition-colors shadow-sm"
               aria-label="{{ __('go_back') }}" title="{{ __('go_back') }}">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="font-black text-2xl text-slate-800">{{ __('trusted_devices') }}</h2>
                <p class="text-sm text-slate-500">{{ __('manage_devices_that_can_sign_in_without_warning') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            <div class="rounded-3xl border border-slate-100 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="font-black text-slate-800">{{ __('registered_devices') }}</h3>
                    <p class="mt-1 text-xs text-slate-400">{{ __('revoke_unrecognized_devices') }}</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($devices as $device)
                        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl {{ $device->revoked_at ? 'bg-slate-100 text-slate-400' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center shrink-0">
                                    <i class="fas fa-{{ $device->revoked_at ? 'ban' : 'laptop' }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-bold text-slate-800 truncate">{{ $device->device_name ?: __('unknown_device') }}</p>
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $device->revoked_at ? 'bg-slate-100 text-slate-500' : 'bg-emerald-50 text-emerald-600' }}">
                                            {{ $device->revoked_at ? __('revoked') : __('active') }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ __('trusted_at') }} {{ optional($device->trusted_at)->format('d M Y H:i') }}
                                        @if($device->last_used_at)
                                            · {{ __('last_used') }} {{ $device->last_used_at->format('d M Y H:i') }}
                                        @endif
                                    </p>
                                    <p class="mt-1 text-[11px] text-slate-400">IP: {{ $device->ip_address ?: __('unknown') }}</p>
                                </div>
                            </div>

                            @if(!$device->revoked_at)
                                <form method="POST" action="{{ route('professor.security.trusted-device.revoke', $device) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-rose-50 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-100 transition-colors">
                                        <i class="fas fa-shield-halved"></i> {{ __('revoke') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <i class="fas fa-laptop text-3xl text-slate-200"></i>
                            <p class="mt-4 text-sm font-bold text-slate-500">{{ __('no_trusted_devices') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
