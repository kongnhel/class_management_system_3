<x-app-layout>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700;900&family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap');

    .sl-wrap * { box-sizing: border-box; }
    .sl-wrap { font-family: 'DM Sans', sans-serif; }
    .kh { font-family: 'Hanuman', serif; }

    /* Stat cards */
    .stat-card {
        background: #fff;
        border: 1px solid #e8f5e9;
        border-radius: 18px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform 0.18s, box-shadow 0.18s, border-color 0.18s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(34,197,94,0.1);
        border-color: #86efac;
    }
    .stat-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 16px;
    }

    /* Table */
    .students-table { width: 100%; border-collapse: collapse; }
    .students-table thead tr {
        background: #f0fdf4;
        border-bottom: 2px solid #d1fae5;
    }
    .students-table thead th {
        padding: 14px 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #6b7280;
        white-space: nowrap;
    }
    .students-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    .students-table tbody tr:hover { background: #f0fdf4; }
    .students-table tbody tr:last-child { border-bottom: none; }
    .students-table td { padding: 14px 20px; vertical-align: middle; }

    /* Avatar */
    .avatar-wrap {
        width: 42px; height: 42px;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #f0fdf4;
    }
    .avatar-wrap.is-leader { border-color: #fbbf24; box-shadow: 0 0 0 3px #fef9c3; }

    /* Leader badge */
    .leader-dot {
        position: absolute; top: -4px; right: -4px;
        width: 16px; height: 16px;
        background: #f59e0b;
        border-radius: 5px;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    }

    /* ID badge */
    .id-badge {
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 0.75rem;
        font-weight: 600;
        color: #4b5563;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        padding: 3px 10px;
        border-radius: 8px;
        letter-spacing: 0.05em;
    }

    /* Role pill */
    .pill-leader { background:#fef9c3; color:#92400e; border:1px solid #fde68a; font-size:0.68rem; font-weight:700; padding:2px 8px; border-radius:999px; }
    .pill-student { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; font-size:0.68rem; font-weight:700; padding:2px 8px; border-radius:999px; }

    /* Action buttons */
    .btn-view {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 14px;
        background: #f0fdf4; color: #16a34a;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        font-size: 0.75rem; font-weight: 700;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
        white-space: nowrap;
    }
    .btn-view:hover { background: #16a34a; color: #fff; border-color: #16a34a; }

    .btn-leader-on {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 14px;
        background: #fef9c3; color: #92400e;
        border: 1px solid #fde68a;
        border-radius: 10px;
        font-size: 0.75rem; font-weight: 700;
        cursor: pointer; transition: background 0.15s;
        white-space: nowrap;
    }
    .btn-leader-off {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 14px;
        background: #f9fafb; color: #9ca3af;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.75rem; font-weight: 700;
        cursor: pointer; transition: all 0.15s;
        white-space: nowrap;
    }
    .btn-leader-off:hover { background: #fef9c3; color: #92400e; border-color: #fde68a; }

    /* Back & Print buttons */
    .btn-back {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px;
        background: #fff; color: #374151;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.82rem; font-weight: 600;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s;
    }
    .btn-back:hover { background: #f9fafb; border-color: #d1d5db; }
    .btn-print {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px;
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 0.82rem; font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(22,163,74,0.25);
        transition: all 0.15s;
    }
    .btn-print:hover { background: linear-gradient(135deg, #15803d,#166534); box-shadow: 0 6px 18px rgba(22,163,74,0.35); transform: translateY(-1px); }

    /* Flash */
    .flash-ok {
        display: flex; align-items: center; gap: 10px;
        background: #f0fdf4; border-left: 4px solid #22c55e;
        color: #166534; padding: 13px 16px;
        border-radius: 12px; font-size: 0.875rem; font-weight: 500;
        margin-bottom: 20px;
    }

    /* Empty state */
    .empty-row td { padding: 60px 20px; text-align: center; }

    /* Report link */
    .btn-report {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 18px;
        background: #fff; color: #374151;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.78rem; font-weight: 700;
        text-decoration: none;
        text-transform: uppercase; letter-spacing: 0.04em;
        transition: background 0.15s;
    }
    .btn-report:hover { background: #f0fdf4; border-color: #bbf7d0; color: #16a34a; }

    [x-cloak] { display: none !important; }

    @media (max-width: 640px) {
        .hide-sm { display: none !important; }
        .students-table td, .students-table th { padding: 10px 12px; }
    }
    @media (max-width: 1024px) {
        .hide-lg { display: none !important; }
    }

    @media print {
        .no-print { display: none !important; }
        @page { size: A4; margin: 1.5cm; }
        body { -webkit-print-color-adjust: exact; }
    }
</style>

<div class="sl-wrap no-print" style="min-height:100vh; background: linear-gradient(160deg,#f0fdf4 0%,#f8fafc 55%); padding: 36px 0 60px;">
    <div style="max-width:1100px; margin:0 auto; padding:0 20px;">

        {{-- ===== Header ===== --}}
        <div style="margin-bottom:28px; display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:16px;">
            <div>
                {{-- Breadcrumb --}}
                <div class="kh" style="display:flex; align-items:center; gap:5px; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af; margin-bottom:8px;">
                    <span>សាស្ត្រាចារ្យ</span>
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3"/></svg>
                    <span style="color:#16a34a;">បញ្ជីឈ្មោះនិស្សិត</span>
                </div>

                <div style="display:flex; align-items:center; gap:14px; margin-bottom:8px;">
                    <div style="width:5px; height:38px; background:linear-gradient(180deg,#16a34a,#4ade80); border-radius:4px; flex-shrink:0;"></div>
                    <h1 class="kh" style="font-size:1.9rem; font-weight:900; color:#111827; margin:0; line-height:1.2;">
                        {{ __('បញ្ជីឈ្មោះនិស្សិត') }}
                    </h1>
                </div>

                <span class="kh" style="display:inline-block; background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; font-size:0.78rem; font-weight:700; padding:4px 14px; border-radius:999px; margin-left:19px;">
                    {{ $courseOffering->course->name_km }}
                </span>
            </div>

            {{-- Buttons --}}
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('professor.my-course-offerings', ['offering_id' => $courseOffering->id]) }}" class="btn-back kh">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('ត្រឡប់') }}
                </a>
                <button onclick="window.print()" class="btn-print kh">
                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/>
                    </svg>
                    {{ __('បោះពុម្ព') }}
                </button>
            </div>
        </div>

        {{-- ===== Flash ===== --}}
        @if (session('success'))
            <div class="flash-ok kh">
                <svg style="width:18px;height:18px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ===== Stats ===== --}}
        @php
            $statItems = [
                ['label' => 'និស្សិតសរុប',  'value' => $stats['total']   ?? 0, 'icon' => '👥', 'bg' => '#eff6ff', 'color' => '#2563eb'],
                ['label' => 'និស្សិតប្រុស', 'value' => $stats['male']    ?? 0, 'icon' => '♂',  'bg' => '#eef2ff', 'color' => '#4f46e5'],
                ['label' => 'និស្សិតស្រី',  'value' => $stats['female']  ?? 0, 'icon' => '♀',  'bg' => '#fff1f2', 'color' => '#e11d48'],
                ['label' => 'ប្រធានថ្នាក់',  'value' => $stats['leaders'] ?? 0, 'icon' => '★',  'bg' => '#fffbeb', 'color' => '#d97706'],
            ];
        @endphp
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:14px; margin-bottom:24px;">
            @foreach($statItems as $item)
                <div class="stat-card">
                    <div class="stat-icon" style="background:{{ $item['bg'] }}; color:{{ $item['color'] }};">
                        <span style="font-size:18px; line-height:1;">{{ $item['icon'] }}</span>
                    </div>
                    <div>
                        <div class="kh" style="font-size:0.68rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.07em; margin-bottom:2px;">{{ $item['label'] }}</div>
                        <div class="kh" style="font-size:1.25rem; font-weight:900; color:#111827; line-height:1;">{{ $item['value'] }} <span style="font-size:0.75rem; font-weight:600; color:#6b7280;">នាក់</span></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ===== Table Card ===== --}}
        <div style="background:#fff; border:1px solid #e8f5e9; border-radius:22px; overflow:hidden; box-shadow:0 4px 20px rgba(34,197,94,0.07);">
            <div style="overflow-x:auto;">
                <table class="students-table">
                    <thead>
                        <tr>
                            <th class="kh" style="text-align:left;">ព័ត៌មាននិស្សិត</th>
                            <th class="kh hide-sm" style="text-align:center;">លេខសម្គាល់</th>
                            <th class="kh hide-lg" style="text-align:left;">ទំនាក់ទំនង</th>
                            <th class="kh" style="text-align:right;">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paginatedStudents as $student)
                            @php
                                $profilePictureUrl = $student->userProfile?->profile_picture_url ?? $student->studentProfile?->profile_picture_url;
                                $isLeader = DB::table('student_course_enrollments')
                                    ->where('course_offering_id', $courseOffering->id)
                                    ->where('student_user_id', $student->id)
                                    ->where('is_class_leader', 1)->exists();
                            @endphp
                            <tr>
                                {{-- Student Info --}}
                                <td>
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <div style="position:relative; flex-shrink:0;">
                                            <div class="avatar-wrap {{ $isLeader ? 'is-leader' : '' }}">
                                                @if($profilePictureUrl)
                                                    <img src="{{ $profilePictureUrl }}?tr=w-100,h-100,fo-face"
                                                         style="width:100%;height:100%;object-fit:cover;" alt="Profile">
                                                @else
                                                    <span class="kh" style="font-size:1rem; font-weight:900; color:#16a34a;">
                                                        {{ Str::substr($student->studentProfile->full_name_km ?? $student->name, 0, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($isLeader)
                                                <div class="leader-dot">
                                                    <svg style="width:8px;height:8px;" fill="white" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1.01 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="kh" style="font-size:0.92rem; font-weight:700; color:#111827; line-height:1.3;">
                                                {{ $student->studentProfile->full_name_km ?? $student->name }}
                                            </div>
                                            <div style="margin-top:4px;">
                                                @if($isLeader)
                                                    <span class="pill-leader kh">ប្រធានថ្នាក់</span>
                                                @else
                                                    <span class="pill-student kh">និស្សិត</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- ID --}}
                                <td class="hide-sm" style="text-align:center;">
                                    <span class="id-badge">{{ $student->student_id_code ?? 'ID-000' }}</span>
                                </td>

                                {{-- Contact --}}
                                <td class="hide-lg">
                                    <div style="font-size:0.8rem; font-weight:600; color:#374151;">{{ $student->email }}</div>
                                    <div style="font-size:0.72rem; color:#9ca3af; margin-top:2px;">{{ $student->studentProfile->phone_number ?? '-' }}</div>
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div style="display:flex; align-items:center; justify-content:flex-end; gap:8px;">
                                        <a href="{{ route('professor.students.show', ['courseOffering' => $courseOffering->id, 'student' => $student->id]) }}"
                                           class="btn-view kh">
                                            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>{{ __('មើល') }}</span>
                                        </a>

                                        <form action="{{ route('professor.toggleClassLeader', [$courseOffering->id, $student->id]) }}" method="POST" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="kh {{ $isLeader ? 'btn-leader-on' : 'btn-leader-off' }}">
                                                <svg style="width:13px;height:13px;" fill="{{ $isLeader ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                </svg>
                                                {{ $isLeader ? __('ប្រធាន') : __('តែងតាំង') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="4">
                                    <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                                        <div style="width:56px;height:56px;background:#f0fdf4;border-radius:16px;display:flex;align-items:center;justify-content:center;">
                                            <svg style="width:28px;height:28px;color:#86efac;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <p class="kh" style="font-size:1rem;font-weight:700;color:#374151;margin:0;">{{ __('មិនទាន់មាននិស្សិត') }}</p>
                                        <p class="kh" style="font-size:0.8rem;color:#9ca3af;margin:0;">{{ __('មិនទាន់មាននិស្សិតចុះឈ្មោះក្នុងមុខវិជ្ជានេះ') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer: Report + Pagination --}}
            <div style="padding:16px 20px; background:#f9fafb; border-top:1px solid #f0fdf4; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
                <a href="{{ route('professor.attendance.report', $courseOffering->id) }}" class="btn-report kh">
                    <svg style="width:14px;height:14px;color:#16a34a;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h5a2 2 0 012 2v14a2 2 0 01-2 2z"/>
                    </svg>
                    {{ __('របាយការណ៍វត្តមាន') }}
                </a>
                <div>{{ $paginatedStudents->links('pagination::tailwind') }}</div>
            </div>
        </div>

    </div>
</div>


{{-- ================= PRINT SECTION (unchanged) ================= --}}
<div class="hidden print:block font-serif text-black px-10 py-8 bg-white">
    <div class="flex flex-col items-center text-center mb-8">
        <div class="mb-2">
            <h2 class="text-[16px] font-bold mb-1" style="font-family:'Khmer OS Muol Light',serif;">ព្រះរាជាណាចក្រកម្ពុជា</h2>
            <h2 class="text-[15px] font-bold" style="font-family:'Khmer OS Muol Light',serif;">ជាតិ សាសនា ព្រះមហាក្សត្រ</h2>
            <div class="mt-1 flex justify-center"><span class="w-24 border-b border-black"></span></div>
        </div>
        <div class="mt-6">
            <h1 class="text-xl font-bold uppercase tracking-widest" style="font-family:'Khmer OS Muol Light',serif;">
                {{ __('បញ្ជីរាយនាមនិស្សិតសរុប') }}
            </h1>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-2 gap-y-2 text-[13px]">
        <div>
            <p><span class="font-bold">មុខវិជ្ជា៖</span> <span class="ml-1">{{ $courseOffering->course->title_en }}</span></p>
            <p><span class="font-bold">ជំនាន់៖</span> <span class="ml-1">{{ $courseOffering->generation ?? $courseOffering->targetPrograms->pluck('generation')->filter()->first() ?? '...' }}</span></p>
        </div>
        <div class="text-right">
            <p><span class="font-bold">កាលបរិច្ឆេទបោះពុម្ព៖</span> <span class="ml-1">{{ now()->format('d/m/Y') }}</span></p>
            <p><span class="font-bold">សរុបនិស្សិត៖</span> <span class="ml-1">{{ count($paginatedStudents) }} នាក់</span></p>
        </div>
    </div>

    <table class="w-full border-collapse border border-black text-[12px]">
        <thead>
            <tr class="bg-gray-100 border border-black">
                <th class="border border-black px-2 py-3 w-[5%] text-center">ល.រ</th>
                <th class="border border-black px-2 py-3 w-[12%] text-center">អត្តលេខ</th>
                <th class="border border-black px-2 py-3 text-left w-[20%]">ឈ្មោះនិស្សិត</th>
                <th class="border border-black px-2 py-3 w-[8%] text-center">ភេទ</th>
                <th class="border border-black px-2 py-3 w-[12%] text-center">ថ្ងៃខែឆ្នាំកំណើត</th>
                <th class="border border-black px-2 py-3 text-left w-[28%]">ដេប៉ាតឺម៉ង់ / កម្មវិធីសិក្សា</th>
                <th class="border border-black px-2 py-3 w-[15%] text-center">លេខទូរស័ព្ទ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paginatedStudents as $index => $student)
                @php
                    $profile = $student->studentProfile;
                    $enrollment = $student->studentProgramEnrollments->first();
                    $genderKm = in_array(strtoupper($profile->gender ?? ''), ['M', 'MALE']) ? 'ប្រុស' : 'ស្រី';
                @endphp
                <tr>
                    <td class="border border-black px-2 py-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black px-2 py-2 text-center font-mono">{{ $student->student_id_code ?? '-' }}</td>
                    <td class="border border-black px-2 py-2 font-medium">{{ $profile->full_name_km ?? $student->name }}</td>
                    <td class="border border-black px-2 py-2 text-center">{{ $genderKm }}</td>
                    <td class="border border-black px-2 py-2 text-center font-mono">
                        {{ $profile->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="border border-black px-2 py-2 leading-tight">{{ $enrollment->program->name_km ?? 'មិនទាន់កំណត់' }}</td>
                    <td class="border border-black px-2 py-2 text-center font-mono">{{ $profile->phone_number ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-12 flex justify-between">
        <div class="text-center w-1/3">
            <p class="text-[13px]">បានពិនិត្យដោយ</p>
            <p class="mt-16 font-bold underline">..........................................</p>
        </div>
        <div class="text-center w-1/3">
            <p class="text-[12px] italic">ធ្វើនៅ រាជធានីភ្នំពេញ, ថ្ងៃទី....... ខែ....... ឆ្នាំ២០...</p>
            <p class="text-[13px] font-bold mt-1">អ្នករៀបចំបញ្ជី</p>
            <p class="mt-16 font-bold">..........................................</p>
        </div>
    </div>
</div>

</x-app-layout>