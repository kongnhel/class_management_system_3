<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>បញ្ជីឈ្មោះសាស្ត្រាចារ្យ</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Moul:wght@400&display=swap" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Khmer OS Battambang', 'KhmerOSbattambang', sans-serif; font-size: 12px; color: #000; padding: 0; }
        .container { max-width: 100%; margin: 0 auto; padding: 10px 20px; }

        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
        .toolbar h2 { margin: 0; font-size: 15px; color: #333; }
        .btn { display: inline-block; padding: 7px 18px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; text-decoration: none; }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-back { background: #6b7280; color: #fff; }
        .btn-back:hover { background: #4b5563; }

        .doc-header { display: flex; align-items: flex-start; margin-bottom: 10px; }
        .doc-header .logo { flex-shrink: 0; width: 100px; margin-right: 15px; padding-top: 5px; }
        .doc-header .logo img { width: 100px; height: 100px; object-fit: contain; }
        .doc-header .text { flex: 1; text-align: center; }
        .header-kh { font-family: 'Moul', 'Khmer OS Muol', 'KhmerOSmuol', 'Khmer OS Battambang', serif; }
        .line1 { font-size: 14px; margin-bottom: 2px; }
        .line2 { font-size: 14px; font-weight: bold; margin-bottom: 2px; }
        .line-motto { font-size: 11px; margin-bottom: 2px; letter-spacing: 2px; }
        .line3 { font-size: 13px; margin-bottom: 2px; }
        .line4 { font-size: 15px; font-weight: bold; margin-bottom: 2px; }
        .line5 { font-size: 12px; margin-bottom: 4px; }
        .line6 { font-size: 16px; font-weight: bold; color: #b91c1c; margin-bottom: 4px; }
        .line7 { font-size: 11px; font-style: italic; color: #555; }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 12px; line-height: 1.6; }

        table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: center; vertical-align: middle; font-size: 11px; word-wrap: break-word; }
        th { background-color: #fff; font-weight: bold; }
        .text-left { text-align: left; }

        .col-stt { width: 30px; }
        .col-id { width: 100px; }
        .col-name-kh { width: 150px; }
        .col-name-en { width: 130px; }
        .col-gender { width: 35px; }
        .col-dob { width: 80px; }
        .col-dept { width: auto; }
        .col-phone { width: 80px; }
        .col-pos { width: auto; }

        .signature-section { display: flex; justify-content: space-between; margin-top: 40px; padding: 0 30px; }
        .signature-block { text-align: center; font-size: 12px; }
        .signature-block .title { font-weight: bold; }
        .signature-block .date-line { margin-top: 5px; font-style: italic; font-size: 11px; }
        .signature-block .sign-line { margin-top: 50px; border-top: 1px solid #000; width: 200px; display: inline-block; }

        @media print {
            @page { size: A4 landscape; margin: 12mm; }
            body { padding: 0; }
            .toolbar { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="toolbar">
        <h2>បញ្ជីឈ្មោះសាស្ត្រាចារ្យ</h2>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-back">ត្រឡប់ក្រោយ</a>
            <button onclick="window.print()" class="btn btn-print">🖨️ Print</button>
        </div>
    </div>

    @php
        $totalProfessors = $professors->count();
        $maleCount = $professors->filter(fn($p) => ($p->profile?->gender) === 'male')->count();
        $femaleCount = $totalProfessors - $maleCount;
        $facultyName = $faculty?->name_km ?? 'មិនកំណត់';
        $departmentName = $department?->name_km ?? 'មិនកំណត់';
        $currentAcademicYear = \App\Models\AcademicYear::getCurrent();
        $academicYearName = $currentAcademicYear?->name ?? '';
        $currentYear = \Carbon\Carbon::now()->year + 543;
    @endphp

    <div class="doc-header">
        <div class="logo">
            <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="NMU Logo">
        </div>
        <div class="text header-kh">
            <p class="line1">ព្រះរាជាណាចក្រកម្ពុជា</p>
            <p class="line2">ជាតិ សាសនា ព្រះមហាក្សត្រ</p>
            <p class="line-motto"><img src="{{ asset('assets/image/2.png') }}" alt="motto" style="height:18px;"></p>
            @if($academicYearName)
                <p class="line3">ឆ្នាំសិក្សា {{ $academicYearName }}</p>
            @endif
            <p class="line4">សាកលវិទ្យាល័យជាតិមានជ័យ</p>
            <p class="line5">{{ $facultyName }}</p>
            <p class="line6">បញ្ជីឈ្មោះសាស្ត្រាចារ្យ</p>
            <p class="line7">ចំនួន {{ $totalProfessors }} នាក់</p>
        </div>
    </div>

    <div class="info-row">
        <span>មហវិទ្យាល័យ៖ <strong>{{ $facultyName }}</strong></span>
        <span>ដេប៉ាតឺម៉ង់៖ <strong>{{ $departmentName }}</strong></span>
        <span>សរុប៖ <strong>{{ $totalProfessors }}</strong> នាក់ (ប្រុស <strong>{{ $maleCount }}</strong> នាក់, ស្រី <strong>{{ $femaleCount }}</strong> នាក់)</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-stt">ល.រ</th>
                <th class="col-id">អត្តសញ្ញាណ</th>
                <th class="col-name-kh">គោត្តនាម និងនាម</th>
                <th class="col-name-en">ឈ្មោះអង់គ្លេស</th>
                <th class="col-gender">ភេទ</th>
                <th class="col-dob">ថ្ងៃខែឆ្នាំកំណើត</th>
                <th class="col-dept text-left">ដេប៉ាតឺម៉ង់</th>
                <th class="col-phone">ទូរស័ព្ទ</th>
                <th class="col-pos text-left">តួនាទី</th>
            </tr>
        </thead>
        <tbody>
            @foreach($professors as $index => $professor)
                @php
                    $gender = $professor->professorProfile?->gender ?? $professor->profile?->gender ?? '';
                    $genderText = $gender === 'male' ? 'ប' : ($gender === 'female' ? 'ស' : '');
                @endphp
                <tr>
                    <td class="col-stt">{{ $index + 1 }}</td>
                    <td class="col-id">{{ $professor->professorProfile->staff_id ?? $professor->name }}</td>
                    <td class="col-name-kh text-left">{{ $professor->professorProfile->full_name_km ?? $professor->profile->full_name_km ?? $professor->name }}</td>
                    <td class="col-name-en text-left">{{ $professor->professorProfile->full_name_en ?? $professor->profile?->full_name_en ?? '' }}</td>
                    <td class="col-gender">{{ $genderText }}</td>
                    <td class="col-dob" style="font-size:10px;">@if($professor->professorProfile?->date_of_birth ?? $professor->profile?->date_of_birth){{ \Carbon\Carbon::parse($professor->professorProfile?->date_of_birth ?? $professor->profile?->date_of_birth)->format('d/m/Y') }}@endif</td>
                    <td class="col-dept text-left">{{ $professor->professorProfile->department?->name_km ?? $professor->department?->name_km ?? '' }}</td>
                    <td class="col-phone">{{ $professor->professorProfile->phone_number ?? $professor->profile?->phone_number ?? '' }}</td>
                    <td class="col-pos text-left">{{ $professor->professorProfile->position ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-block">
            <div class="date-line">ថ្ងៃទី ..... ខែ ..... ឆ្នាំ {{ $currentYear }}</div>
            <div class="title">ហត្ថលេខារបស់អ្នករៀបចំ</div>
            <div class="sign-line"></div>
        </div>
        <div class="signature-block">
            <div class="date-line">ថ្ងៃទី ..... ខែ ..... ឆ្នាំ {{ $currentYear }}</div>
            <div class="title">ហត្ថលេខារបស់នាយកសាលា</div>
            <div class="sign-line"></div>
        </div>
    </div>
</div>

</body>
</html>
