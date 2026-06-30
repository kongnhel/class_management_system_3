@props([])

{{-- Toast container --}}
<div id="toast-container" class="fixed top-6 right-6 z-[9999] w-full max-w-sm space-y-3 pointer-events-none"></div>

{{-- Render flash-message toasts (server-side) --}}
@if(session('success'))
    <script>window.addEventListener('DOMContentLoaded', () => showToast('{{ addslashes(session('success')) }}', 'success'));</script>
@endif
@if(session('error'))
    <script>window.addEventListener('DOMContentLoaded', () => showToast('{{ addslashes(session('error')) }}', 'error'));</script>
@endif
@if(session('warning'))
    <script>window.addEventListener('DOMContentLoaded', () => showToast('{{ addslashes(session('warning')) }}', 'warning'));</script>
@endif
@if(session('info'))
    <script>window.addEventListener('DOMContentLoaded', () => showToast('{{ addslashes(session('info')) }}', 'info'));</script>
@endif
@if(session('status'))
    <script>window.addEventListener('DOMContentLoaded', () => showToast('{{ addslashes(session('status')) }}', 'info'));</script>
@endif

{{-- showToast JS engine --}}
<script>
(function() {
    const typeConfig = {
        success: {
            title: 'ជោគជ័យ',
            iconBg: 'bg-green-50 text-green-600',
            bar: 'bg-green-500',
            svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>'
        },
        error: {
            title: 'មានបញ្ហា',
            iconBg: 'bg-red-50 text-red-600',
            bar: 'bg-red-500',
            svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>'
        },
        warning: {
            title: 'ព្រមាន',
            iconBg: 'bg-amber-50 text-amber-600',
            bar: 'bg-amber-500',
            svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>'
        },
        info: {
            title: 'ព័ត៌មាន',
            iconBg: 'bg-emerald-50 text-emerald-600',
            bar: 'bg-emerald-500',
            svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>'
        }
    };

    window.showToast = function(message, type) {
        const cfg = typeConfig[type] || typeConfig.info;
        const container = document.getElementById('toast-container');
        if (!container) return;

        const id = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 5);
        const toastEl = document.createElement('div');
        toastEl.id = id;
        toastEl.className = 'bg-white rounded-2xl shadow-xl border border-gray-100 p-4 transform translate-x-full opacity-0 transition-all duration-300 pointer-events-auto';
        toastEl.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl ${cfg.iconBg} flex items-center justify-center shrink-0">
                    ${cfg.svg}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900">${cfg.title}</p>
                    <p class="text-sm text-gray-500 leading-relaxed break-words">${message}</p>
                </div>
                <button onclick="document.getElementById('${id}')?.remove()" class="text-gray-400 hover:text-gray-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full ${cfg.bar} transition-all duration-100 ease-linear" style="width: 100%"></div>
            </div>
        `;

        container.appendChild(toastEl);
        requestAnimationFrame(() => {
            toastEl.classList.remove('translate-x-full', 'opacity-0');
        });

        const bar = toastEl.querySelector('.h-full');
        let progress = 100;
        const interval = setInterval(() => {
            progress -= 0.5;
            if (bar) bar.style.width = progress + '%';
            if (progress <= 0) {
                clearInterval(interval);
                toastEl.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toastEl.remove(), 300);
            }
        }, 50);
    };
})();
</script>