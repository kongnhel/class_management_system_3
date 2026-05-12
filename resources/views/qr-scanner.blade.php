<x-app-layout>
    <style>
        @keyframes scan {
            0% { top: 0; }
            50% { top: 100%; }
            100% { top: 0; }
        }
        .scanner-container {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background: #000;
        }
        .scanner-line {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, transparent, #10b981, transparent);
            box-shadow: 0 0 25px #10b981;
            animation: scan 2.5s infinite linear;
        }
    </style>

    <div class="min-h-[85vh] flex items-center justify-center p-4 bg-dark-50">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
                
                <div class="mx-auto w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mb-5">
                    <i class="fa-solid fa-qrcode text-5xl text-emerald-600"></i>
                </div>

                <h2 class="text-2xl font-black text-dark-800">ស្កែន QR Code</h2>
                <p class="text-dark-500 mt-1 mb-8">ស្កែនដើម្បីចូលកុំព្យូទ័រ</p>

                <!-- Scanner -->
                <div class="scanner-container border-4 border-emerald-400 shadow-inner" style="aspect-ratio: 1 / 1;">
                    <div id="reader" class="w-full h-full rounded-[14px]"></div>
                    <div class="scanner-line"></div>
                </div>

                <div id="status" class="mt-6 text-sm font-medium min-h-[70px]"></div>

                <!-- Instruction -->
                <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-2xl text-left text-xs leading-relaxed">
                    <strong class="text-blue-700">ការណែនាំ៖</strong><br>
                    1. ចុច <strong>"Request Camera Permissions"</strong><br>
                    2. ជ្រើសរើស <strong>Allow</strong> នៅពេល Browser សួរ<br>
                    3. សូមប្រើទូរស័ព្ទ និងដាក់ QR Code ឱ្យចំកណ្តាល
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        let scanner;

function onScanSuccess(decodedText, decodedResult) {
    const status = document.getElementById('status');
    status.innerHTML = `<span class="text-emerald-600">កំពុងផ្ទៀងផ្ទាត់...</span>`;

    html5QrcodeScanner.clear();

    fetch('/qr-authorize', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ token: decodedText })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            status.innerHTML = `✅ ជោគជ័យ! សូមត្រឡប់ទៅកុំព្យូទ័រ`;
        } else {
            status.innerHTML = `<span class="text-red-600">${data.message}</span>`;
        }
    })
    .catch(err => {
        console.error(err);
        status.innerHTML = `<span class="text-red-600">មានបញ្ហាបណ្តាញ</span>`;
    });
}

        function onScanFailure(error) {
            // មិនបង្ហាញ error ធម្មតា
        }

        // ចាប់ផ្តើម Scanner
        function startScanner() {
            scanner = new Html5QrcodeScanner("reader", {
                fps: 15,
                qrbox: { width: 260, height: 260 },
                aspectRatio: 1,
                rememberLastUsedCamera: true,
                showTorchButtonIfSupported: true
            });

            scanner.render(onScanSuccess, onScanFailure);
        }

        // រង់ចាំ DOM រួចចាប់ផ្តើម
        window.onload = function() {
            startScanner();
        };
    </script>
</x-app-layout>