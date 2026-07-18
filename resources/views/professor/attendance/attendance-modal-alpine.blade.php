<div x-data="attendanceModal()" x-on:open-attendance.window="open($event.detail.courseOfferingId)"
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
            <div class="w-full lg:w-5/12 bg-slate-900 relative overflow-hidden flex flex-col items-center justify-center p-4 lg:p-10 shrink-0">
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
                            <div class="relative overflow-hidden rounded-lg w-[140px] h-[140px] lg:w-[240px] lg:h-[240px] bg-white flex items-center justify-center">
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
                        <h3 class="text-lg lg:text-xl font-bold text-slate-800">បញ្ជីឈ្មោះសិស្ស</h3>
                        <p class="text-[10px] text-slate-500 font-medium uppercase tracking-wider">កំពុងរង់ចាំសិស្សស្កែន...</p>
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
                            <div class="flex items-center gap-3 mb-4 px-2">
                                <div class="flex items-center gap-1.5 bg-green-50 border border-green-200 px-3 py-1.5 rounded-lg">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-[11px] font-bold text-green-700" x-text="counts.present + ' មក'"></span>
                                </div>
                                <template x-if="counts.late > 0">
                                    <div class="flex items-center gap-1.5 bg-yellow-50 border border-yellow-200 px-3 py-1.5 rounded-lg">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                        <span class="text-[11px] font-bold text-yellow-700" x-text="counts.late + ' យឺត'"></span>
                                    </div>
                                </template>
                                <template x-if="counts.permission > 0">
                                    <div class="flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                        <span class="text-[11px] font-bold text-emerald-700" x-text="counts.permission + ' ច្បាប់'"></span>
                                    </div>
                                </template>
                            </div>

                            {{-- Student Cards --}}
                            <div class="space-y-2">
                                <template x-for="(student, index) in students" :key="student.id">
                                    <div class="flex items-center gap-3 p-3 rounded-xl border transition-all animate-fade-in hover:shadow-md"
                                         :class="{
                                            'bg-green-50 border-green-200': student.status === 'present',
                                            'bg-yellow-50 border-yellow-200': student.status === 'late',
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
                                                    'bg-yellow-500': student.status === 'late',
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
                                                        'text-yellow-700': student.status === 'late',
                                                        'text-emerald-700': student.status === 'permission',
                                                        'text-red-700': student.status === 'absent'
                                                      }"
                                                      x-text="student.status === 'present' ? 'មក' : (student.status === 'late' ? 'យឺត' : (student.status === 'permission' ? 'ច្បាប់' : 'អវត្តមាន'))"></span>
                                                <span class="text-slate-300">·</span>
                                                <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    <span x-text="student.time"></span>
                                                </span>
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
                            <p class="text-slate-400 text-xs">សូមរង់ចាំសិស្សស្កែន QR Code</p>
                        </div>
                    </template>
                </div>

                {{-- Action Bar --}}
                <div class="p-4 lg:p-6 border-t border-slate-200 bg-white flex flex-row gap-3 shrink-0 z-30 pb-10 lg:pb-6">
                    <button @click="closeModal()" class="flex-1 px-4 py-3 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-sm">
                        បិទផ្ទាំង
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
        courseOfferingId: null,
        courseName: '...',
        qrSvg: '',
        students: [],
        totalEnrolled: 0,
        counts: { present: 0, late: 0, permission: 0 },
        pollInterval: null,
        qrInterval: null,
        qrTimeLeft: 10,

        async open(courseOfferingId) {
            this.courseOfferingId = courseOfferingId;
            this.isOpen = true;
            this.showConfirm = false;

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch("{{ route('professor.attendance.api.start') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ course_offering_id: courseOfferingId })
                });
                const data = await res.json();
                if (data.success) {
                    this.qrSvg = data.qr_svg;
                    this.courseName = data.course_name;
                }
            } catch (e) {
                console.error('Failed to start session:', e);
            }

            this.fetchStudents();
            this.startPolling();
            this.startQrCountdown();
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
                    body: JSON.stringify({ course_offering_id: this.courseOfferingId })
                });
                const data = await res.json();
                if (data.success) {
                    this.qrSvg = data.qr_svg;
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
            this.stopPolling();
            this.pollInterval = setInterval(() => {
                if (this.isOpen) this.fetchStudents();
            }, 5000);
        },

        startQrCountdown() {
            if (this.qrInterval) clearInterval(this.qrInterval);
            this.qrTimeLeft = 10;
            this.qrInterval = setInterval(() => {
                if (!this.isOpen) { clearInterval(this.qrInterval); return; }
                if (this.qrTimeLeft > 1) {
                    this.qrTimeLeft--;
                } else {
                    this.qrTimeLeft = 10;
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
            this.isOpen = false;
            this.stopPolling();
            this.courseOfferingId = null;
            this.qrSvg = '';
            this.students = [];
        }
    }
}
</script>
