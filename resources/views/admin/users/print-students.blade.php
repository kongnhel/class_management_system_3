<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <title>បញ្ជីឈ្មោះនិស្សិត</title>
    <style>
        @page { size: A4 landscape; margin: 15px; }
        body { font-family: 'Khmer OS Battambang', 'KhmerOSbattambang', 'Noto Sans Khmer', sans-serif; font-size: 11px; color: #000; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: center; vertical-align: middle; }
        th { background-color: #d9d9d9; font-weight: bold; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-center { text-align: center; }
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mt-2 { margin-top: 10px; }
        .mt-4 { margin-top: 20px; }
        .mt-6 { margin-top: 30px; }
        .page-break { page-break-before: always; }
        .no-border td, .no-border th { border: none; }
    </style>
</head>
<body>

    {{-- Header --}}
    <p class="text-center font-bold mb-0" style="font-size:13px;">ព្រះរាជាណាចក្រកម្ពុជា</p>
    <p class="text-center font-bold mb-3" style="font-size:13px;">ជាតិ សាសនា ព្រះមហាក្សត្រ</p>

    <p class="text-center font-bold mb-1" style="font-size:14px;">បញ្ជីឈ្មោះនិស្សិត</p>

    @php
        $totalStudents = $students->count();
        $maleCount = $students->filter(fn($s) => ($s->studentProfile->gender ?? $s->profile?->gender) === 'male')->count();
        $femaleCount = $totalStudents - $maleCount;
        $programName = $students->first()?->program->name_km ?? 'មិនកំណត់';
        $generation = $students->first()?->generation ?? 'មិនកំណត់';
    @endphp

    <table class="mb-2" style="border:none;">
        <tr class="no-border">
            <td class="text-left" style="border:none; font-size:11px;">កម្មវិធីសិក្សា៖ <strong>{{ $programName }}</strong></td>
            <td class="text-right" style="border:none; font-size:11px;">ជំនាន់ទី៖ <strong>{{ $generation }}</strong></td>
        </tr>
    </table>

    {{-- Student Table --}}
    <table>
        <thead>
            <tr>
                <th style="width:30px;">ល.រ</th>
                <th style="width:70px;">អត្តសញ្ញាណ</th>
                <th class="text-left" style="width:140px;">គោត្តនាម និងនាម</th>
                <th class="text-left" style="width:110px;">ឈ្មោះអង់គ្លេស</th>
                <th style="width:30px;">ភេទ</th>
                <th style="width:35px;">ឆ្នាំទី</th>
                <th style="width:80px;">ទូរស័ព្ទ</th>
                <th class="text-left">អាសយដ្ឋាន</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                @php
                    $gender = ($student->studentProfile->gender ?? $student->profile?->gender) ?? '';
                    $genderText = $gender === 'male' ? 'ប' : ($gender === 'female' ? 'ស' : '');
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-size:10px;">{{ $student->student_id_code ?? '' }}</td>
                    <td class="text-left">{{ $student->studentProfile->full_name_km ?? $student->profile->full_name_km ?? $student->name }}</td>
                    <td class="text-left" style="font-size:10px;">{{ $student->studentProfile->full_name_en ?? $student->profile?->full_name_en ?? '' }}</td>
                    <td>{{ $genderText }}</td>
                    <td>{{ $student->computed_year_level ?? '' }}</td>
                    <td style="font-size:10px;">{{ $student->studentProfile->phone_number ?? $student->profile?->phone_number ?? '' }}</td>
                    <td class="text-left" style="font-size:9px;">{{ $student->studentProfile->address ?? $student->profile?->address ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Summary --}}
    <table class="mt-2" style="border:none;">
        <tr class="no-border">
            <td class="text-left" style="border:none; font-size:11px;">សរុប៖ <strong>{{ $totalStudents }}</strong> នាក់ (ប្រុស <strong>{{ $maleCount }}</strong> នាក់, ស្រី <strong>{{ $femaleCount }}</strong> នាក់)</td>
        </tr>
    </table>

    {{-- Signature --}}
    <table class="mt-6" style="border:none;">
        <tr class="no-border">
            <td class="text-center" style="border:none; width:50%; font-size:11px;">
                <p class="font-bold">ហត្ថលេខារបស់អ្នករៀបចំ</p>
                <div style="margin-top:40px; border-top:1px solid #000; width:150px; margin-left:auto; margin-right:auto;"></div>
            </td>
            <td class="text-center" style="border:none; width:50%; font-size:11px;">
                <p class="font-bold">ហត្ថលេខារបស់នាយកសាលា</p>
                <div style="margin-top:40px; border-top:1px solid #000; width:150px; margin-left:auto; margin-right:auto;"></div>
            </td>
        </tr>
    </table>

</body>
</html>
