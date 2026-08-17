<x-app-layout>
    <div class="min-h-screen bg-slate-50 py-8 px-4">
        <div class="max-w-xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-black text-slate-900">Attendance ID Card</h1>
                <p class="text-sm text-slate-500 mt-1">Save this card on your phone and show it to your professor when attendance is open.</p>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            @if ($card && ! $card->revoked_at)
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-6 text-center">
                    <div id="attendance-card-image" class="rounded-2xl bg-slate-900 p-5 text-white">
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-300">NMU Attendance</p>
                        @if($photoUrl)
                            <div class="mt-3 mx-auto w-20 h-20 rounded-full overflow-hidden ring-4 ring-emerald-400/40 border-2 border-white shadow-xl">
                                <img src="{{ $photoUrl }}" alt="Student photo" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <h2 class="mt-3 text-xl font-black">{{ $user->studentProfile?->full_name_km ?? $user->name }}</h2>
                        <p class="mt-1 text-sm text-slate-300">{{ $user->student_id_code }}</p>
                        <div class="mt-5 rounded-2xl bg-white p-3">
                            <div class="relative mx-auto w-full max-w-xs">
                                <img src="{{ route('student.attendance-card.qr') }}" alt="Attendance card QR code" class="mx-auto w-full">
                                @if($photoCenter)
                                    <img src="{{ $photoUrl }}" alt="" class="absolute inset-0 m-auto w-16 h-16 rounded-full border-4 border-white shadow-lg object-cover">
                                @endif
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">This QR card identifies you for an active professor attendance session. It does not replace the professor's session validation.</p>
                    @if($photoUrl && ! $photoCenter)
                        <a href="{{ route('student.profile.edit') }}" wire:navigate class="mt-3 inline-block text-[11px] font-bold text-emerald-600 hover:text-emerald-700">ផ្លាស់ប្តូររូបភាពកាត</a>
                    @endif
                    <div class="mt-5 flex gap-3">
                        <button type="button" onclick="downloadAttendanceCard('png')" class="flex-1 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-700">Save PNG</button>
                        <button type="button" onclick="downloadAttendanceCard('jpg')" class="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white hover:bg-blue-700">Save JPG</button>
                        <form method="POST" action="{{ route('student.attendance-card.revoke') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-100">Revoke Card</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-6">
                    <h2 class="text-lg font-black text-slate-900">Create your attendance card</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Create a private QR card once, then save it to your phone. You can revoke it and generate a new one if your phone is lost.</p>
                    <form method="POST" action="{{ route('student.attendance-card.create') }}" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-700">Create Attendance Card</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/html-to-image@1.11.11/dist/html-to-image.min.js"></script>
    <script>
        function downloadAttendanceCard(format) {
            const card = document.getElementById('attendance-card-image');
            if (!card || !window.htmlToImage) return;

            const filename = 'attendance-card-{{ $user->student_id_code ?? $user->id }}.' + format;
            const renderer = format === 'jpg' ? window.htmlToImage.toJpeg : window.htmlToImage.toPng;

            renderer(card, { pixelRatio: 2, cacheBust: true, backgroundColor: '#0f172a' })
                .then(function (dataUrl) {
                    const link = document.createElement('a');
                    link.download = filename;
                    link.href = dataUrl;
                    link.click();
                });
        }
    </script>

    <style>
        @media print { body { display: none; } }
    </style>
</x-app-layout>
