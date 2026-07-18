<x-app-layout>
    <style>
        #reader { border: none !important; }
        #reader video { object-fit: cover; border-radius: 1.5rem; width: 100% !important; height: 100% !important; }
        #reader__dashboard_section_csr span, 
        #reader__dashboard_section_swaplink { display: none !important; }
        #scan-status {
            position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%);
            background: rgba(0,0,0,0.6); color: #34d399; font-size: 10px; font-weight: bold;
            padding: 3px 10px; border-radius: 20px; z-index: 15; white-space: nowrap;
        }
    </style>

    <div class="min-h-screen bg-gray-50 flex flex-col items-center pt-6 px-4">
        
        {{-- HTTPS Warning --}}
        <div id="https-warning" class="hidden w-full max-w-md mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md">
            <p class="font-bold">⚠️ Security Error</p>
            <p class="text-sm">{!! __('សូមប្រើប្រាស់ HTTPS ទើបកាមេរ៉ាដំណើរការ។') !!}</p>
        </div>

        {{-- Header --}}
        <div class="w-full max-w-md flex justify-between items-center mb-6">
            <a href="{{ route('student.dashboard') }}" class="p-3 rounded-full bg-white shadow-sm text-gray-500 hover:text-gray-900 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h1 class="text-xl font-bold text-gray-800">{{ __('QR Attendance') }}</h1>
            <div class="w-10"></div>
        </div>

        {{-- Main Scanner Card --}}
        <div class="w-full max-w-md relative">
            <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden p-4">
                
                {{-- 1. Error display --}}
                <div id="error-message-box" class="hidden mb-4 p-3 bg-red-50 text-red-600 text-sm rounded-xl border border-red-100 text-center"></div>

                {{-- 2. Camera box --}}
                <div class="relative rounded-2xl overflow-hidden bg-black aspect-square shadow-inner isolate mb-4">
                    <div id="reader" class="w-full h-full absolute inset-0"></div>
                    <div id="scan-status" class="hidden">⚡ កំពុងស្កែន...</div>

                    {{-- Overlay UI --}}
                    <div id="scan-overlay" class="hidden absolute inset-0 pointer-events-none p-6 flex-col justify-between z-10">
                        <div class="flex justify-between">
                            <div class="w-10 h-10 border-t-4 border-l-4 border-white/80 rounded-tl-3xl"></div>
                            <div class="w-10 h-10 border-t-4 border-r-4 border-white/80 rounded-tr-3xl"></div>
                        </div>
                        {{-- Laser Animation --}}
                        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500/80 shadow-[0_0_20px_rgba(59,130,246,1)] animate-scan"></div>
                        <div class="flex justify-between">
                            <div class="w-10 h-10 border-b-4 border-l-4 border-white/80 rounded-bl-3xl"></div>
                            <div class="w-10 h-10 border-b-4 border-r-4 border-white/80 rounded-br-3xl"></div>
                        </div>
                    </div>

                    {{-- 3. Start button (for Brave/iOS) --}}
                    <div id="start-screen" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-gray-900 text-white p-6 text-center">
                        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mb-4 backdrop-blur-sm">
                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2">{{ __('qr_enable_camera') }}</h3>
                        <p class="text-sm text-gray-400 mb-6">{{ __('qr_enable_desc') }}</p>
                        
                        <button onclick="startCamera()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-emerald-500/30 transition-all active:scale-95 flex items-center gap-2">
                            <span>📷 {{ __('qr_enable_btn') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Upload Option (Fallback) --}}
                <div class="text-center">
                    <p class="text-xs text-gray-400 mb-2">{{ __('or') }}</p>
                    <input type="file" id="qr-input-file" accept="image/*" class="hidden" onchange="handleFileUpload(this)">
                    <button onclick="document.getElementById('qr-input-file').click()" class="text-sm font-bold text-emerald-600 bg-emerald-50 px-4 py-2 rounded-lg hover:bg-emerald-100 transition-colors w-full">
                        📂 {{ __('qr_upload_gallery') }}
                    </button>
                </div>

                {{-- Manual Token Input Fallback --}}
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-400 mb-2 text-center">ឬ បញ្ចូលកូដដោយដៃ：</p>
                    <div class="flex gap-2">
                        <input type="text" id="manual-token" placeholder="បញ្ចូល token..." class="flex-1 px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-400">
                        <button onclick="submitManualToken()" class="px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700">ផ្ញើ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Result --}}
    <div id="result-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center relative overflow-hidden">
            <div id="status-icon-container" class="mx-auto w-24 h-24 rounded-full flex items-center justify-center mb-6"></div>
            <h3 id="modal-title" class="text-2xl font-black text-gray-900 mb-2"></h3>
            <p id="modal-message" class="text-gray-500 mb-8"></p>
            <button onclick="window.location.reload()" class="w-full py-4 rounded-2xl font-bold text-white bg-emerald-600 shadow-lg" id="modal-btn">OK</button>
        </div>
    </div>

    <script>
        // Check HTTPS
        if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
            document.getElementById('https-warning').classList.remove('hidden');
        }

        // Check if html5-qrcode library loaded
        if (typeof Html5Qrcode === 'undefined') {
            document.getElementById('start-screen').innerHTML = `
                <div class="text-red-400 mb-4">
                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-lg font-bold mb-2 text-red-400">បណ្ណាល័យ QR មិនផ្ទុក</h3>
                <p class="text-sm text-gray-400 mb-4">សូមផ្ទុកទំព័រឡើងវិញ ឬប្រើប៊ូតុងបញ្ចូលកូដដោយដៃខាងក្រោម។</p>
            `;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        let isProcessing = false;
        let html5QrCode = null;

        try {
            html5QrCode = new Html5Qrcode("reader");
        } catch (e) {
            console.error('Html5Qrcode init failed:', e);
        }

        // Responsive qrbox — uses 80% of the smaller dimension
        function getQrBox() {
            const w = Math.min(window.innerWidth, window.innerHeight) * 0.8;
            return { width: Math.floor(w), height: Math.floor(w) };
        }

        function startCamera() {
            if (!html5QrCode) {
                showOnScreenError('QR Library មិនផ្ទុក! សូមប្រើប៊ូតុងបញ្ចូលកូដដោយដៃ។');
                return;
            }

            document.getElementById('start-screen').classList.add('hidden');
            document.getElementById('scan-overlay').classList.remove('hidden');
            document.getElementById('scan-overlay').classList.add('flex');
            document.getElementById('scan-status').classList.remove('hidden');

            const config = { fps: 10, qrbox: getQrBox(), aspectRatio: 1.0 };

            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
            .then(() => {
                console.log('[QR] Camera started successfully');
                document.getElementById('scan-status').textContent = '⚡ កំពុងស្កែន...';
            })
            .catch(err => {
                console.error("[QR] Camera Error:", err);
                showOnScreenError("{{ __('qr_camera_error') }} " + err);
                document.getElementById('start-screen').classList.remove('hidden');
                document.getElementById('scan-overlay').classList.add('hidden');
                document.getElementById('scan-status').classList.add('hidden');
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log('[QR] Scan detected! Token:', decodedText);
            if (isProcessing) return;
            isProcessing = true;

            document.getElementById('scan-status').textContent = '✓ បានស្កែន! កំពុងផ្ញើ...';

            // Stop Camera
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(err => console.log('[QR] Stop error:', err));
            }

            if (navigator.vibrate) navigator.vibrate(200);
            showModal('processing');
            sendTokenToServer(decodedText);
        }

        function sendTokenToServer(token) {
            console.log('[QR] Sending token to server:', '{{ route("student.process-scan") }}');
            fetch('{{ route("student.process-scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ token: token })
            })
            .then(async response => {
                console.log('[QR] Server response status:', response.status);
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || response.statusText);
                return data;
            })
            .then(data => {
                console.log('[QR] Server response data:', data);
                showModal(data.success ? 'success' : 'error', data.message);
            })
            .catch(error => {
                console.error('[QR] Fetch error:', error);
                showModal('error', error.message || 'មានបញ្ហាក្នុងការទាក់ទង server');
            });
        }

        function submitManualToken() {
            const token = document.getElementById('manual-token').value.trim();
            if (!token) {
                showOnScreenError('សូមបញ្ចូល token!');
                return;
            }
            if (isProcessing) return;
            isProcessing = true;
            showModal('processing');
            sendTokenToServer(token);
        }

        let failureCount = 0;
        function onScanFailure(error) {
            failureCount++;
            if (failureCount % 30 === 0) {
                console.log('[QR] Still scanning... (' + failureCount + ' frames processed)');
            }
        }

        function handleFileUpload(input) {
            if (input.files.length === 0) return;
            const imageFile = input.files[0];

            if (!html5QrCode) {
                showOnScreenError('QR Library មិនផ្ទុក!');
                return;
            }

            html5QrCode.scanFile(imageFile, true)
            .then(decodedText => {
                console.log('[QR] File scan detected:', decodedText);
                onScanSuccess(decodedText, null);
            })
            .catch(err => {
                showOnScreenError("{{ __('qr_read_error') }} " + err);
            });
        }

        function showOnScreenError(msg) {
            const errBox = document.getElementById('error-message-box');
            errBox.innerText = msg;
            errBox.classList.remove('hidden');
        }

        function showModal(type, message = '') {
            const overlay = document.getElementById('result-overlay');
            const iconContainer = document.getElementById('status-icon-container');
            const title = document.getElementById('modal-title');
            const msg = document.getElementById('modal-message');
            const btn = document.getElementById('modal-btn');

            overlay.classList.remove('hidden');
            overlay.classList.add('flex');

            if (type === 'processing') {
                iconContainer.className = "mx-auto w-24 h-24 rounded-full flex items-center justify-center mb-6 bg-emerald-50 text-emerald-600 animate-spin";
                iconContainer.innerHTML = `<svg class="w-10 h-10" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
                title.innerText = "{{ __('qr_verifying') }}";
                msg.innerText = "{{ __('qr_verifying_please') }}";
                btn.classList.add('hidden');
            } else if (type === 'success') {
                iconContainer.className = "mx-auto w-24 h-24 rounded-full flex items-center justify-center mb-6 bg-green-100 text-green-600";
                iconContainer.innerHTML = `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>`;
                title.innerText = "{{ __('qr_success_title') }}";
                msg.innerText = message;
                btn.className = "w-full py-4 rounded-2xl font-bold text-white bg-green-500 shadow-lg block";
                btn.innerText = "{{ __('qr_success_done') }}";
                btn.classList.remove('hidden');
            } else {
                iconContainer.className = "mx-auto w-24 h-24 rounded-full flex items-center justify-center mb-6 bg-red-100 text-red-500";
                iconContainer.innerHTML = `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>`;
                title.innerText = "{{ __('qr_failed_title') }}";
                msg.innerText = message;
                btn.className = "w-full py-4 rounded-2xl font-bold text-white bg-red-500 shadow-lg block";
                btn.innerText = "{{ __('qr_retry') }}";
                btn.classList.remove('hidden');
            }
        }

        // CSS Animation
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes scan { 0% { top: 0; } 50% { top: 100%; } 100% { top: 0; } }
            .animate-scan { animation: scan 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite; }
        `;
        document.head.appendChild(style);
    </script>
</x-app-layout>
