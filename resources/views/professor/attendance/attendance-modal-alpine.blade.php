<div x-data="attendanceModal()" x-on:open-attendance.window="open($event.detail.courseOfferingId, $event.detail.scheduleId, $event.detail.readOnly || false)"
     x-show="isOpen" x-cloak
     class="fixed inset-0 z-[60]" style="display: none;">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-950/95 backdrop-blur-sm transition-opacity duration-300"
         x-show="isOpen" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    {{-- Main Modal Container --}}
    <div class="fixed inset-0 z-[61] flex items-start md:items-start justify-center">
        <div class="bg-white md:rounded-[2rem] shadow-2xl w-full md:max-w-7xl mx-auto flex flex-col lg:flex-row h-[100dvh] md:h-[90vh] overflow-hidden relative border border-white/10"
             :class="isOpen ? '' : 'pointer-events-none'">

            {{-- Close Button --}}
            <button @click="closeModal()" class="absolute top-4 right-4 lg:top-6 lg:right-6 z-[70] p-2 rounded-full bg-black/20 hover:bg-red-500 hover:text-white text-white/70 backdrop-blur-md transition-all">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            {{-- === LEFT PANEL: QR Presenter === --}}
            <div x-show="!isReadOnly" class="w-full lg:w-5/12 bg-slate-900 relative overflow-hidden flex flex-col items-center justify-center p-4 lg:p-10 shrink-0">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-900 via-slate-900 to-slate-950"></div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22n%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23n)%22/%3E%3C/svg%3E');"></div>

                <div class="relative z-10 flex flex-col items-center w-full max-w-md mx-auto">
                    <div class="text-center mb-4 lg:mb-8">
                        <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-2">
                            <span class="relative flex h-2 w-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span class="text-[10px] font-bold tracking-widest uppercase text-emerald-400">Live Attendance</span>
                        </div>
                        <h2 class="text-xl lg:text-4xl font-black text-white tracking-tight mb-1">ស្កែនវត្តមាន</h2>
                        <p class="text-emerald-300 text-xs lg:text-sm font-bold uppercase truncate max-w-[250px] lg:max-w-none" x-text="courseName"></p>
                    </div>

                    {{-- QR Code Box --}}
                    <div class="relative group mx-auto">
                        <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-purple-500 rounded-2xl blur opacity-30"></div>
                        <div class="relative bg-white p-2 lg:p-4 rounded-xl lg:rounded-2xl shadow-2xl">
                            <div class="relative rounded-lg w-[140px] h-[140px] lg:w-[240px] lg:h-[240px] bg-white flex items-center justify-center [&_svg]:w-full [&_svg]:h-full">
                                <div x-html="qrSvg"></div>
                                <div class="scan-line"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Countdown Timer --}}
                    <div class="mt-4 lg:mt-8 w-48 lg:w-full">
                        <div class="flex items-center justify-between text-slate-400 text-[10px] lg:text-sm font-medium mb-1.5 px-1">
                            <span>QR ប្តូរថ្មី</span>
                            <span class="font-mono text-white font-bold"><span x-text="qrTimeLeft">10</span>s</span>
                        </div>
                        <div class="h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500 transition-all duration-1000 ease-linear"
                                 :style="'width: ' + (qrTimeLeft / 10 * 100) + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === RIGHT PANEL: Student List === --}}
            <div class="flex-1 bg-slate-50 flex flex-col min-h-0 relative z-20 lg:rounded-none rounded-t-[2rem] mt-[-1.5rem] lg:mt-0 shadow-[0_-10px_30px_rgba(0,0,0,0.3)] lg:shadow-none overflow-hidden">
                
                <div class="px-6 py-4 border-b border-slate-200 bg-white sticky top-0 z-30 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="text-lg lg:text-xl font-bold text-slate-800" x-text="isReadOnly ? 'ប្រវត្តិវត្តមាន' : 'បញ្ជីឈ្មោះសិស្ស'"></h3>
                        <p class="text-[10px] text-slate-500 font-medium uppercase tracking-wider" x-text="isReadOnly ? 'មើលប្រវត្តិវត្តមានសិស្ស' : 'កំពុងរង់ចាំសិស្សស្កែន...'"></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <template x-if="students.length > 0">
                            <div class="bg-slate-100 px-3 py-1.5 rounded-xl flex flex-col items-center">
                                <span class="text-sm font-black text-slate-600 leading-none"
                                      x-text="students.length + '/' + totalEnrolled"></span>
                                <span class="text-[8px] font-bold text-slate-400 uppercase">សិស្ស</span>
                            </div>
                        </template>
                        <div class="bg-emerald-50 px-4 py-1.5 rounded-xl border border-emerald-100 flex flex-col items-center">
                            <span class="text-xl lg:text-2xl font-black text-emerald-600 leading-none"
                                  x-text="String(students.length).padStart(2, '0')"></span>
                            <span class="text-[9px] font-bold text-emerald-400 uppercase">Scanned</span>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar p-4 lg:p-6 bg-slate-50">
                    <template x-if="students.length > 0">
                        <div>
                            {{-- Summary Bar --}}
                            <div class="flex items-center gap-3 mb-4 px-2 flex-wrap">
                                <div class="flex items-center gap-1.5 bg-green-50 border border-green-200 px-3 py-1.5 rounded-lg">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-[11px] font-bold text-green-700" x-text="counts.present + ' មក'"></span>
                                </div>
                                <template x-if="counts.permission > 0">
                                    <div class="flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                        <span class="text-[11px] font-bold text-emerald-700" x-text="counts.permission + ' ច្បាប់'"></span>
                                    </div>
                                </template>
                                <template x-if="counts.manual > 0">
                                    <div class="flex items-center gap-1.5 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg">
                                        <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                                        <span class="text-[11px] font-bold text-amber-700" x-text="counts.manual + ' បញ្ចូនដោយដៃ'"></span>
                                    </div>
                                </template>
                                <template x-if="counts.qr > 0">
                                    <div class="flex items-center gap-1.5 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        <span class="text-[11px] font-bold text-blue-700" x-text="counts.qr + ' QR'"></span>
                                    </div>
                                </template>
                            </div>

                            {{-- Student Cards --}}
                            <div class="space-y-2">
                                <template x-for="(student, index) in students" :key="student.id">
                                    <div class="flex items-center gap-3 p-3 rounded-xl border transition-all animate-fade-in hover:shadow-md"
                                         :class="{
                                            'bg-green-50 border-green-200': student.status === 'present',
                                            'bg-emerald-50 border-emerald-200': student.status === 'permission',
                                            'bg-red-50 border-red-200': student.status === 'absent'
                                         }">
                                        {{-- Avatar --}}
                                        <div class="relative shrink-0">
                                            <template x-if="student.profile_pic">
                                                <img :src="student.profile_pic" :alt="student.name"
                                                     class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-md ring-1 ring-slate-100">
                                            </template>
                                            <template x-if="!student.profile_pic">
                                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-emerald-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md"
                                                     x-text="student.initial"></div>
                                            </template>
                                            <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 border-2 border-white rounded-full flex items-center justify-center"
                                                 :class="{
                                                    'bg-green-500': student.status === 'present',
                                                    'bg-emerald-500': student.status === 'permission',
                                                    'bg-red-500': student.status === 'absent'
                                                 }">
                                                <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                            </div>
                                        </div>
                                        
                                        {{-- Info --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-bold text-slate-800 text-sm truncate" x-text="student.name"></h4>
                                                <template x-if="student.student_code">
                                                    <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded shrink-0" x-text="student.student_code"></span>
                                                </template>
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-bold uppercase"
                                                      :class="{
                                                        'text-green-700': student.status === 'present',
                                                        'text-emerald-700': student.status === 'permission',
                                                        'text-red-700': student.status === 'absent'
                                                      }"
                                                      x-text="student.status === 'present' ? 'មក' : (student.status === 'permission' ? 'ច្បាប់' : 'អវត្តមាន')"></span>
                                                <span class="text-slate-300">·</span>
                                                <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    <span x-text="student.time"></span>
                                                </span>
                                                <template x-if="student.source === 'manual'">
                                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-amber-600 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded">
                                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                        បញ្ចូនដោយដៃ
                                                    </span>
                                                </template>
                                                <template x-if="student.source === 'qr'">
                                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-blue-600 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded">
                                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                                        QR Code
                                                    </span>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Number Badge --}}
                                        <div class="w-7 h-7 rounded-full bg-white border border-slate-200 flex items-center justify-center shrink-0">
                                            <span class="text-[10px] font-bold text-slate-500" x-text="index + 1"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="students.length === 0">
                        <div class="flex flex-col items-center justify-center h-full text-center py-16">
                            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" /></svg>
                            </div>
                            <h4 class="text-slate-500 font-bold text-sm mb-1">មិនទាន់មានសិស្សស្កែន</h4>
                            <p class="text-slate-400 text-xs">សូមរង់ចាំសិស្សស្កែន QR Code ឬបញ្ចូនវត្តមានដោយដៃ</p>
                        </div>
                    </template>
                </div>

                {{-- Action Bar --}}
                <div x-show="!isReadOnly" class="p-4 lg:p-6 border-t border-slate-200 bg-white flex flex-row gap-3 shrink-0 z-30 pb-10 lg:pb-6">
                    <button @click="closeModal()" class="flex-1 px-4 py-3 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-sm">
                        បិទផ្ទាំង
                    </button>
                    <button @click="openCardScanner()" class="flex-1 relative px-4 py-3 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95 text-sm">
                        ស្កែនកាតសិស្ស
                    </button>
                    <button @click="showConfirm = true" class="flex-[2] relative px-4 py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all active:scale-95 text-sm">
                        បញ្ចប់ និងរក្សាទុក
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirm Close Modal --}}
    <div x-show="showConfirm" x-cloak
         class="fixed inset-0 z-[80] flex items-start justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="showConfirm = false"></div>
        <div class="bg-white p-6 rounded-3xl shadow-2xl max-w-sm w-full text-center relative z-[90]">
            <div class="mx-auto w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4 text-red-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <h3 class="text-lg font-black text-slate-800 mb-2">តើអ្នកប្រាកដទេ?</h3>
            <p class="text-slate-500 text-xs mb-6">
                ការបញ្ចប់នឹងកំណត់សិស្សដែលមិនទាន់ស្កែនជា "អវត្តមាន" ដោយស្វ័យប្រវត្តិ។
            </p>
            <div class="flex gap-3">
                <button @click="showConfirm = false" class="flex-1 py-3 rounded-xl font-bold text-slate-600 bg-slate-100"> បោះបង់ </button>
                <button @click="closeSession()" class="flex-1 py-3 rounded-xl font-bold text-white bg-red-600"
                        :disabled="closing" x-text="closing ? 'កំពុងបញ្ចប់...' : 'យល់ព្រម'">យល់ព្រម</button>
            </div>
        </div>
    </div>
</div>

<style>
    .scan-line {
        width: 100%; height: 4px; background: #60a5fa; box-shadow: 0 0 15px #60a5fa;
        position: absolute; animation: scan 2s cubic-bezier(0.4, 0, 0.2, 1) infinite; border-radius: 50%;
        z-index: 20;
    }
    @keyframes scan {
        0% { top: 0%; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
    .animate-fade-in {
        animation: fadeInUp 0.4s ease-out forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
function attendanceModal() {
    return {
        isOpen: false,
        showConfirm: false,
        closing: false,
        isReadOnly: false,
        courseOfferingId: null,
        scheduleId: null,
        sessionId: null,
        courseName: '...',
        qrSvg: '',
        students: [],
        totalEnrolled: 0,
        counts: { present: 0, permission: 0, absent: 0, manual: 0, qr: 0 },
        pollInterval: null,
        qrInterval: null,
        qrTimeLeft: 15,
        qrDuration: 15,
        cardScanner: null,
        _scanCooldown: false,

        async open(courseOfferingId, scheduleId, readOnly = false) {
            this.courseOfferingId = courseOfferingId;
            this.scheduleId = scheduleId;
            this.isOpen = true;
            this.showConfirm = false;
            this.isReadOnly = readOnly;

            if (!readOnly) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch("{{ route('professor.attendance.api.start') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ course_offering_id: courseOfferingId, schedule_id: scheduleId })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.qrSvg = data.qr_svg;
                        this.courseName = data.course_name;
                        this.sessionId = data.session_id;
                        this.qrDuration = data.expires_in || 15;
                    }
                } catch (e) {
                    console.error('Failed to start session:', e);
                }
                this.startQrCountdown();
            } else {
                this.courseName = 'ប្រវត្តិវត្តមាន';
                this.stopPolling();
            }

            this.fetchStudents();
            this.startPolling();
        },

        async openCardScanner() {
            if (!this.isOpen || this.isReadOnly || ! this.courseOfferingId) return;

            if (! document.getElementById('attendance-card-scanner')) {
                const scanner = document.createElement('div');
                scanner.id = 'attendance-card-scanner';
                scanner.className = 'fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/90 p-4';
                scanner.innerHTML = '<div class="w-full max-w-md rounded-3xl bg-white p-4"><div class="mb-3 flex items-center justify-between"><h3 class="font-black text-slate-800">ស្កែនកាតសិស្ស</h3><button type="button" id="close-card-scanner" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-bold">បិទ</button></div><div id="card-scanner-reader" class="aspect-square overflow-hidden rounded-2xl bg-black"></div><p id="card-scanner-status" class="mt-3 text-center text-xs text-slate-500">សូមបង្ហាញកាតសិស្សទៅកាមេរ៉ា</p></div>';
                document.body.appendChild(scanner);
                document.getElementById('close-card-scanner').addEventListener('click', () => this.closeCardScanner());
            }

            if (typeof Html5Qrcode === 'undefined') {
                document.getElementById('card-scanner-status').textContent = 'QR scanner is unavailable.';
                return;
            }

            this.cardScanner = new Html5Qrcode('card-scanner-reader');
            await this.cardScanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 240, height: 240 } },
                (decodedText) => this.submitCardScan(decodedText),
                () => {}
            );
        },

        async submitCardScan(decodedText) {
            if (! this.cardScanner) return;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if (this._scanCooldown) return;
            this._scanCooldown = true;

            try {
                const response = await fetch("{{ route('professor.attendance.card-scan') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: JSON.stringify({ card_token: decodedText, session_id: this.sessionId })
                });
                const data = await response.json();
                const status = document.getElementById('card-scanner-status');
                if (status) {
                    status.textContent = data.message || 'Scan complete.';
                    status.classList.toggle('text-emerald-600', !!data.success);
                    status.classList.toggle('text-rose-600', !data.success);
                }
                if (data.success) {
                    this.playScanSound();
                    await this.fetchStudents();
                }
            } catch (e) {
                const status = document.getElementById('card-scanner-status');
                if (status) { status.textContent = 'ការស្កែនបរាជ័យ។'; status.classList.remove('text-emerald-600'); status.classList.add('text-rose-600'); }
            } finally {
                // keep the scanner camera open for the next student
                setTimeout(() => { this._scanCooldown = false; }, 1200);
            }
        },

        playScanSound() {
            try {
                const ctx = this._audioCtx || (this._audioCtx = new (window.AudioContext || window.webkitAudioContext)());
                const play = (freq, start, dur) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.value = freq;
                    gain.gain.setValueAtTime(0.001, ctx.currentTime + start);
                    gain.gain.exponentialRampToValueAtTime(0.4, ctx.currentTime + start + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + start + dur);
                    osc.connect(gain).connect(ctx.destination);
                    osc.start(ctx.currentTime + start);
                    osc.stop(ctx.currentTime + start + dur + 0.05);
                };
                play(880, 0, 0.18);
                play(1320, 0.18, 0.25);
            } catch (e) { /* audio unavailable */ }
        },

        async closeCardScanner() {
            if (this.cardScanner) {
                try { await this.cardScanner.stop(); } catch (e) {}
                this.cardScanner = null;
            }
            document.getElementById('attendance-card-scanner')?.remove();
        },

        async refreshQr() {
            if (!this.isOpen || !this.courseOfferingId) return;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch("{{ route('professor.attendance.api.refresh-qr') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ course_offering_id: this.courseOfferingId, schedule_id: this.scheduleId })
                });
                const data = await res.json();
                if (data.success) {
                    this.qrSvg = data.qr_svg;
                    this.qrDuration = data.expires_in || 15;
                }
            } catch (e) {
                console.error('Failed to refresh QR:', e);
            }
        },

        async fetchStudents() {
            if (!this.courseOfferingId) return;
            try {
                const url = "{{ route('professor.attendance.api.students', ':id') }}".replace(':id', this.courseOfferingId);
                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.students = data.attendances;
                    this.totalEnrolled = data.total_enrolled;
                    this.counts = data.counts;
                }
            } catch (e) {
                console.error('Failed to fetch students:', e);
            }
        },

        startPolling() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
            this.pollInterval = setInterval(() => {
                if (this.isOpen) this.fetchStudents();
            }, 5000);
        },

        startQrCountdown() {
            if (this.qrInterval) clearInterval(this.qrInterval);
            this.qrTimeLeft = this.qrDuration;
            this.qrInterval = setInterval(() => {
                if (!this.isOpen) { clearInterval(this.qrInterval); return; }
                if (this.qrTimeLeft > 1) {
                    this.qrTimeLeft--;
                } else {
                    this.qrTimeLeft = this.qrDuration;
                    this.refreshQr();
                }
            }, 1000);
        },

        stopPolling() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
            if (this.qrInterval) {
                clearInterval(this.qrInterval);
                this.qrInterval = null;
            }
        },

        async closeSession() {
            if (!this.courseOfferingId) return;
            this.closing = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch("{{ route('professor.attendance.api.close') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ course_offering_id: this.courseOfferingId })
                });
                const data = await res.json();
                if (data.success) {
                    this.showConfirm = false;
                    this.isOpen = false;
                    this.stopPolling();
                    if (typeof Swal !== 'undefined') {
                        await Swal.fire('ជោគជ័យ', data.message, 'success');
                    }
                    window.location.reload();
                }
            } catch (e) {
                console.error('Failed to close session:', e);
            } finally {
                this.closing = false;
            }
        },

        closeModal() {
            this.closeCardScanner();
            this.isOpen = false;
            this.stopPolling();
            this.courseOfferingId = null;
            this.scheduleId = null;
            this.qrSvg = '';
            this.students = [];
        }
    }
}
</script>
