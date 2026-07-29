<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>របាយការណ៍វត្តមានសរុប</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@300;400;700&family=Moul:wght@400&display=swap" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Battambang', 'Khmer OS Battambang', sans-serif; font-size: 13px; color: #000; padding: 0; }
        .container { max-width: 100%; margin: 0 auto; padding: 10px 15px; }

        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
        .toolbar h2 { margin: 0; font-size: 15px; color: #333; }
        .btn { display: inline-block; padding: 7px 18px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; text-decoration: none; }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-back { background: #6b7280; color: #fff; }
        .btn-back:hover { background: #4b5563; }

        .doc-header { display: grid; grid-template-columns: 30% 40% 30%; align-items: flex-start; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 10px; text-align: center; }
        .header-left { display: flex; flex-direction: column; align-items: center; }
        .header-left img.logo { width: 80px; height: 80px; object-fit: contain; margin-bottom: 5px; }
        .header-kh { font-family: 'Moul', serif !important; }
        .line-univ { font-size: 13px; font-weight: bold; color: #2a58ad; margin-bottom: 1px; }
        .header-center { display: flex; flex-direction: column; align-items: center; }
        .line1 { font-size: 14px; margin-bottom: 1px; }
        .line2 { font-size: 14px; font-weight: bold; margin-bottom: 1px; }
        .line-motto img { height: 25px; margin-top: 3px; }

        .title { text-align: center; margin: 10px 0; }
        .title h1 { font-family: 'Moul', serif; font-size: 18px; font-weight: bold; text-decoration: underline double; }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; line-height: 1.6; }

        table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 5px 6px; text-align: center; vertical-align: middle; font-size: 12px; word-wrap: break-word; }
        th { background-color: #f1f5f9; font-weight: bold; }
        .text-left { text-align: left; }

        .col-stt { width: 35px; }
        .col-id { width: 100px; }
        .col-name { width: 150px; }
        .col-p { width: 50px; }
        .col-a { width: 50px; }
        .col-total { width: 55px; }
        .col-percent { width: 70px; }

        .signature-section { display: flex; justify-content: space-between; margin-top: 30px; padding: 0 30px; }
        .signature-block { text-align: center; font-size: 13px; }
        .signature-block .title { font-weight: bold; }
        .signature-block .sign-line { margin-top: 40px; border-top: 1px solid #000; width: 180px; display: inline-block; }

        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { padding: 0; }
            .toolbar { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="toolbar">
        <h2>របាយការណ៍វត្តមានសរុប</h2>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-back">ត្រឡប់ក្រោយ</a>
            <button onclick="window.print()" class="btn btn-print">🖨️ Print</button>
        </div>
    </div>

    @php
        $totalStudents = $students->count();
        $totalPresent = $students->sum('present_count');
        $totalPermission = $students->sum('permission_count');
        $totalAbsent = $students->sum('absent_count');
        $allSessions = $totalPresent + $totalPermission + $totalAbsent;
        $avgAttendance = $allSessions > 0 ? ($totalPresent / $allSessions) * 100 : 0;

        $now = \Carbon\Carbon::now();
        $khmerMonths = [1=>'មករា',2=>'កុម្ភៈ',3=>'មីនា',4=>'មេសា',5=>'ឧសភា',6=>'មិថុនា',7=>'កក្កដា',8=>'សីហា',9=>'កញ្ញា',10=>'តុលា',11=>'វិច្ឆិកា',12=>'ធ្នូ'];
        function toKhmerNumsAR($n) { return str_replace(range(0,9), ['០','១','២','៣','៤','៥','៦','៧','៨','៩'], $n); }
        $dayKh = toKhmerNumsAR($now->format('d'));
        $monthKh = $khmerMonths[$now->month];
        $yearKh = toKhmerNumsAR((string)$now->year);
    @endphp

    <div class="doc-header">
        <div class="header-left">
            <img class="logo" src="{{ asset('assets/image/nmu_Logo.png') }}" alt="Logo">
            <p class="line-univ header-kh">សាកលវិទ្យាល័យជាតិមានជ័យ</p>
            <p class="line-univ header-kh">ការិយាល័យសិក្សា</p>
        </div>
        <div class="header-center">
            <p class="line1 header-kh">ព្រះរាជាណាចក្រកម្ពុជា</p>
            <p class="line2 header-kh">ជាតិ សាសនា ព្រះមហាក្សត្រ</p>
            <div class="line-motto"><img src="{{ asset('assets/image/2.png') }}" alt="motto"></div>
        </div>
        <div></div>
    </div>

    <div class="title">
        <h1>របាយការណ៍វត្តមានសរុប</h1>
    </div>

    <div class="info-row">
        <span>មុខវិជ្ជា៖ <strong>{{ $courseOffering->course->name_km ?? $courseOffering->course->title_km }}</strong></span>
        <span>ឆ្នាំសិក្សា៖ <strong>{{ $courseOffering->academic_year }}</strong> | <strong>{{ $courseOffering->semester }}</strong></span>
        <span>សរុប៖ <strong>{{ $totalStudents }}</strong> នាក់ | អត្រាវត្តមាន៖ <strong>{{ number_format($avgAttendance, 1) }}%</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-stt">ល.រ</th>
                <th class="col-id">អត្តសញ្ញាណ</th>
                <th class="col-name text-left">គោត្តនាម និងនាម</th>
                <th class="col-p">P</th>
                <th class="col-a">A</th>
                <th class="col-total">សរុប</th>
                <th class="col-percent">ភាគរយ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $data)
                @php
                    $total = ($data->present_count ?? 0) + ($data->permission_count ?? 0) + ($data->absent_count ?? 0);
                    $percentage = $total > 0 ? (($data->present_count ?? 0) / $total) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-size:11px;">{{ $data->student_id_code ?? '' }}</td>
                    <td class="text-left" style="font-size:11px;">{{ $data->studentProfile->full_name_km ?? $data->profile->full_name_km ?? $data->name }}</td>
                    <td>{{ $data->present_count ?? 0 }}</td>
                    <td>{{ $data->absent_count ?? 0 }}</td>
                    <td style="font-weight:bold;">{{ $total }}</td>
                    <td style="font-weight:bold;">{{ number_format($percentage, 0) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-block">
            <div class="date-line">ថ្ងៃទី {{ $dayKh }} ខែ {{ $monthKh }} ឆ្នាំ {{ $yearKh }}</div>
            <div class="title">ហត្ថលេខារបស់អ្នករៀបចំ</div>
            <div class="sign-line"></div>
        </div>
        <div class="signature-block">
            <div class="date-line">ថ្ងៃទី {{ $dayKh }} ខែ {{ $monthKh }} ឆ្នាំ {{ $yearKh }}</div>
            <div class="title">ហត្ថលេខារបស់នាយកសាលា</div>
            <div class="sign-line"></div>
        </div>
    </div>
</div>

</body>
</html>
