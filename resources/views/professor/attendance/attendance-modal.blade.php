<div>
    {{-- Custom CSS --}}
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
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    {{-- 1. Main Backdrop --}}
    <div style="{{ $isOpen ? '' : 'display: none' }}"
         class="fixed inset-0 z-[60] flex items-start md:items-start justify-center bg-slate-950/95 backdrop-blur-sm transition-opacity duration-300">
        
        {{-- 2. Main Modal Container --}}
        <div class="bg-white md:rounded-[2rem] shadow-2xl w-full md:max-w-7xl mx-auto flex flex-col lg:flex-row h-[100dvh] md:h-[90vh] overflow-hidden relative border border-white/10"
             @if($isOpen) wire:poll.30s @endif> 

            {{-- Close Button --}}
            <button wire:click="close" class="absolute top-4 right-4 lg:top-6 lg:right-6 z-[70] p-2 rounded-full bg-black/20 hover:bg-red-500 hover:text-white text-white/70 backdrop-blur-md transition-all">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            {{-- === LEFT PANEL: QR Presenter === --}}
            <div class="w-full lg:w-5/12 bg-slate-900 relative overflow-hidden flex flex-col items-center justify-center p-4 lg:p-10 shrink-0">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-900 via-slate-900 to-slate-950"></div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22n%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23n)%22/%3E%3C/svg%3E');"></div>

                <div class="relative z-10 flex flex-col items-center w-full max-w-md mx-auto">
                    <div class="text-center mb-4 lg:mb-8">
                        <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full {{ $scanStatus === 'active' ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-slate-500/10 border border-slate-500/20' }} mb-2">
                            @if($scanStatus === 'active')
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="text-[10px] font-bold tracking-widest uppercase text-emerald-400">Live Attendance</span>
                            @else
                                <span class="relative flex h-2 w-2">
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-slate-500"></span>
                                </span>
                                <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400">{{ $scanStatus === 'not_started' ? 'រង់ចាំ' : ($scanStatus === 'ended' ? 'បានបញ្ចប់' : 'មិនដំណើរការ') }}</span>
                            @endif
                        </div>
                        <h2 class="text-xl lg:text-4xl font-black text-white tracking-tight mb-1">ស្កែនវត្តមាន</h2>
                        <p class="text-emerald-300 text-xs lg:text-sm font-bold uppercase truncate max-w-[250px] lg:max-w-none">{{ $courseName }}</p>
                    </div>

                    {{-- Schedule Info Bar --}}
                    @if($scheduleInfo)
                    <div class="w-full mb-4 lg:mb-6 bg-white/5 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                        <div class="flex items-center justify-between text-white text-xs lg:text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span class="font-bold">{{ $scheduleInfo['start_time'] }} - {{ $scheduleInfo['end_time'] }}</span>
                            </div>
                            @if($scanStatus === 'active' && isset($scheduleInfo['minutes_remaining']))
                                <span class="text-emerald-400 font-bold text-xs">នៅសល់ {{ $scheduleInfo['minutes_remaining'] }} នាទី</span>
                            @elseif($scanStatus === 'not_started' && isset($scheduleInfo['minutes_until']))
                                <span class="text-yellow-400 font-bold text-xs">ចាប់ផ្តើមក្នុង {{ $scheduleInfo['minutes_until'] }} នាទី</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- QR Code Box / Status Messages --}}
                    @if($scanStatus === 'active')
                        <div class="relative group mx-auto">
                            <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-purple-500 rounded-2xl blur opacity-30"></div>
                            <div class="relative bg-white p-2 lg:p-4 rounded-xl lg:rounded-2xl shadow-2xl">
                                <div class="relative overflow-hidden rounded-lg w-[140px] h-[140px] lg:w-[240px] lg:h-[240px] bg-white flex items-center justify-center">
                                    @if($qrCodeImage)
                                        {!! $qrCodeImage !!}
                                    @endif
                                    <div class="scan-line"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Countdown Timer --}}
                        <div class="mt-4 lg:mt-8 w-48 lg:w-full" x-data="{ timeLeft: 30 }" x-init="setInterval(() => { timeLeft = timeLeft > 1 ? timeLeft - 1 : 30 }, 1000)">
                            <div class="flex items-center justify-between text-slate-400 text-[10px] lg:text-sm font-medium mb-1.5 px-1">
                                <span>QR ប្តូរថ្មី</span>
                                <span class="font-mono text-white font-bold"><span x-text="timeLeft">30</span>s</span>
                            </div>
                            <div class="h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500 transition-all duration-1000 ease-linear"
                                     :style="'width: ' + (timeLeft / 30 * 100) + '%'"></div>
                            </div>
                        </div>
                    @elseif($scanStatus === 'not_started')
                        <div class="flex flex-col items-center text-center py-8">
                            <div class="w-20 h-20 bg-yellow-500/10 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-1">មិនទាន់ដល់ម៉ោង</h3>
                            <p class="text-slate-400 text-xs">សូមរង់ចាំដល់ម៉ោងកំណត់ទើបអាចស្កែនបាន។</p>
                            @if(isset($scheduleInfo['minutes_until']))
                                <div class="mt-4 text-3xl font-black text-yellow-400">{{ $scheduleInfo['minutes_until'] }}</div>
                                <div class="text-xs text-slate-400">នាទីទៀត</div>
                            @endif
                        </div>
                    @elseif($scanStatus === 'ended')
                        <div class="flex flex-col items-center text-center py-8">
                            <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-1">ការស្កែនបានបញ្ចប់</h3>
                            <p class="text-slate-400 text-xs">ម៉ោងកាលវិភាគបានចប់ហើយ។</p>
                        </div>
                    @else
                        <div class="flex flex-col items-center text-center py-8">
                            <div class="w-20 h-20 bg-slate-500/10 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-1">មិនមានកាលវិភាគ</h3>
                            <p class="text-slate-400 text-xs">ថ្ងៃនេះមិនមានកាលវិភាគសម្រាប់មុខវិជ្ជានេះទេ។</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- === RIGHT PANEL: Student List === --}}
            <div class="flex-1 bg-slate-50 flex flex-col min-h-0 relative z-20 lg:rounded-none rounded-t-[2rem] mt-[-1.5rem] lg:mt-0 shadow-[0_-10px_30px_rgba(0,0,0,0.3)] lg:shadow-none overflow-hidden">
                
                <div class="px-6 py-4 border-b border-slate-200 bg-white sticky top-0 z-30 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="text-lg lg:text-xl font-bold text-slate-800">បញ្ជីឈ្មោះសិស្ស</h3>
                        <p class="text-[10px] text-slate-500 font-medium uppercase tracking-wider">
                            @if($scanStatus === 'active')
                                កំពុងរង់ចាំសិស្សស្កែន...
                            @else
                                ការស្កែនមិនទាន់ដំណើរការ
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if(isset($attendances) && count($attendances) > 0)
                            @php
                                $totalEnrolled = \App\Models\StudentCourseEnrollment::where('course_offering_id', $this->courseId)->count();
                            @endphp
                            <div class="bg-slate-100 px-3 py-1.5 rounded-xl flex flex-col items-center">
                                <span class="text-sm font-black text-slate-600 leading-none">
                                    {{ count($attendances) }}/{{ $totalEnrolled }}
                                </span>
                                <span class="text-[8px] font-bold text-slate-400 uppercase">សិស្ស</span>
                            </div>
                        @endif
                        <div class="bg-emerald-50 px-4 py-1.5 rounded-xl border border-emerald-100 flex flex-col items-center">
                            <span class="text-xl lg:text-2xl font-black text-emerald-600 leading-none">
                                {{ isset($attendances) ? str_pad(count($attendances), 2, '0', STR_PAD_LEFT) : '00' }}
                            </span>
                            <span class="text-[9px] font-bold text-emerald-400 uppercase">Scanned</span>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar p-4 lg:p-6 bg-slate-50">
                    @if(isset($attendances) && count($attendances) > 0)
                        {{-- Summary Bar --}}
                        @php
                            $presentCount = $attendances->where('status', 'present')->count();
                            $permissionCount = $attendances->where('status', 'permission')->count();
                        @endphp
                        <div class="flex items-center gap-3 mb-4 px-2">
                            <div class="flex items-center gap-1.5 bg-green-50 border border-green-200 px-3 py-1.5 rounded-lg">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-[11px] font-bold text-green-700">{{ $presentCount }} មក</span>
                            </div>
                            @if($permissionCount > 0)
                            <div class="flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                <span class="text-[11px] font-bold text-emerald-700">{{ $permissionCount }} ច្បាប់</span>
                            </div>
                            @endif
                        </div>

                        {{-- Student Cards --}}
                        <div class="space-y-2">
                        @foreach($attendances as $index => $record)
                            @php
                                $statusColors = [
                                    'present' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'dot' => 'bg-green-500', 'label' => 'មក'],
                                    'permission' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'ច្បាប់'],
                                    'absent' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700', 'dot' => 'bg-red-500', 'label' => 'អវត្តមាន'],
                                ];
                                $color = $statusColors[$record->status] ?? $statusColors['absent'];
                                $studentName = $record->student->profile->full_name_km ?? $record->student->name ?? 'N/A';
                                $studentCode = $record->student->student_id_code ?? '';
                            @endphp
                            <div class="flex items-center gap-3 {{ $color['bg'] }} p-3 rounded-xl border {{ $color['border'] }} transition-all animate-fade-in-up hover:shadow-md">
                                {{-- Avatar --}}
                                <div class="relative shrink-0">
                                    @php
                                        $pic = $record->student->profile->profile_picture_url ?? null;
                                        $av = $record->student->avatar ?? null;
                                        $profilePic = (!empty($pic) && $pic !== 'null') ? $pic : ((!empty($av) && $av !== 'null') ? $av : null);
                                    @endphp
                                    @if($profilePic)
                                        <img src="{{ $profilePic }}" alt="{{ $studentName }}" onerror="this.style.display='none';this.nextElementSibling.classList.remove('hidden');"
                                             class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-md ring-1 ring-slate-100">
                                        <div class="hidden w-11 h-11 rounded-full bg-gradient-to-br from-emerald-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                            {{ mb_substr($studentName, 0, 1) }}
                                        </div>
                                    @else
                                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-emerald-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                            {{ mb_substr($studentName, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 border-2 border-white rounded-full {{ $color['dot'] }} flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                    </div>
                                </div>
                                
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-slate-800 text-sm truncate">{{ $studentName }}</h4>
                                        @if($studentCode)
                                            <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded shrink-0">{{ $studentCode }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-bold {{ $color['text'] }} uppercase">{{ $color['label'] }}</span>
                                        <span class="text-slate-300">·</span>
                                        <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {{ $record->created_at->format('h:i:s A') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Number Badge --}}
                                <div class="w-7 h-7 rounded-full bg-white border border-slate-200 flex items-center justify-center shrink-0">
                                    <span class="text-[10px] font-bold text-slate-500">{{ $index + 1 }}</span>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-center py-16">
                            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" /></svg>
                            </div>
                            <h4 class="text-slate-500 font-bold text-sm mb-1">មិនទាន់មានសិស្សស្កែន</h4>
                            <p class="text-slate-400 text-xs">សូមរង់ចាំសិស្សស្កែន QR Code</p>
                        </div>
                    @endif
                </div>

                {{-- Action Bar --}}
                <div class="p-4 lg:p-6 border-t border-slate-200 bg-white flex flex-row gap-3 shrink-0 z-30 pb-10 lg:pb-6">
                    <button wire:click="close" class="flex-1 px-4 py-3 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-sm">
                        បិទផ្ទាំង
                    </button>

                    <button wire:click="$set('showConfirmation', true)"
                            class="flex-[2] relative px-4 py-3 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all active:scale-95 text-sm">
                        បញ្ចប់ និងរក្សាទុក
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- CONFIRMATION MODAL --}}
    @if($showConfirmation)
    <div class="fixed inset-0 z-[80] flex items-start justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" wire:click="$set('showConfirmation', false)"></div>
        <div class="bg-white p-6 rounded-3xl shadow-2xl max-w-sm w-full text-center relative z-[90]">
            <div class="mx-auto w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4 text-red-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <h3 class="text-lg font-black text-slate-800 mb-2">តើអ្នកប្រាកដទេ?</h3>
            <p class="text-slate-500 text-xs mb-6">
                ការបញ្ចប់នឹងកំណត់សិស្សដែលមិនទាន់ស្កែនជា "អវត្តមាន" ដោយស្វ័យប្រវត្តិ។
            </p>
            <div class="flex gap-3">
                <button wire:click="$set('showConfirmation', false)" class="flex-1 py-3 rounded-xl font-bold text-slate-600 bg-slate-100"> បោះបង់ </button>
                <button wire:click="closeAttendance" class="flex-1 py-3 rounded-xl font-bold text-white bg-red-600"> យល់ព្រម </button>
            </div>
        </div>
    </div>
    @endif
</div>
