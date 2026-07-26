<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>តារាងពិន្ទុរួម</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@300;400;700&family=Moul:wght@400&display=swap" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Battambang', 'Khmer OS Battambang', sans-serif; font-size: 14px; color: #000; padding: 0; }
        .container { max-width: 100%; margin: 0 auto; padding: 10px 15px; }

        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
        .toolbar h2 { margin: 0; font-size: 18px; color: #333; }
        .btn { display: inline-block; padding: 7px 18px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-back { background: #6b7280; color: #fff; }
        .btn-back:hover { background: #4b5563; }

        .doc-header { display: flex; align-items: flex-start; margin-bottom: 10px; }
        .doc-header .logo { flex-shrink: 0; width: 80px; margin-right: 15px; padding-top: 5px; }
        .doc-header .logo img { width: 80px; height: 80px; object-fit: contain; }
        .doc-header .text { flex: 1; text-align: center; }
        .header-kh { font-family: 'Moul', 'Khmer OS Muol', serif !important; }
        .line1 { font-size: 14px; margin-bottom: 1px; }
        .line2 { font-size: 14px; font-weight: bold; margin-bottom: 1px; }
        .line-motto { font-size: 10px; margin-bottom: 1px; letter-spacing: 2px; }
        .line3 { font-size: 12px; margin-bottom: 1px; }
        .line4 { font-size: 16px; font-weight: bold; margin-bottom: 1px; }
        .line5 { font-size: 13px; margin-bottom: 2px; }
        .line6 { font-size: 18px; font-weight: bold; color: #b91c1c; margin-bottom: 3px; }
        .line7 { font-size: 12px; font-style: italic; color: #555; }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 14px; line-height: 1.6; }

        table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 5px 6px; text-align: center; vertical-align: middle; font-size: 13px; word-wrap: break-word; }
        th { background-color: #fff; font-weight: bold; font-size: 13px; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }

        .col-stt { width: 35px; }
        .col-id { width: 110px; }
        .col-name { width: 150px; }
        .col-gender { width: 35px; }
        .col-score { width: auto; }
        .col-grade { width: 60px; }

        .signature-section { display: flex; justify-content: space-between; margin-top: 30px; padding: 0 30px; }
        .signature-block { text-align: center; font-size: 14px; }
        .signature-block .title { font-weight: bold; font-size: 14px; }
        .signature-block .date-line { margin-top: 5px; font-style: italic; font-size: 13px; }
        .signature-block .sign-line { margin-top: 40px; border-top: 1px solid #000; width: 200px; display: inline-block; }

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
        <h2>តារាងពិន្ទុរួម - {{ $courseOffering->course->title_km }}</h2>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-back">ត្រឡប់ក្រោយ</a>
            <button onclick="window.print()" class="btn btn-print">🖨️ Print</button>
        </div>
    </div>

    @php
        $totalStudents = $students->count();
        $currentYear = \Carbon\Carbon::now()->year + 543;
        $academicYearName = $courseOffering->academic_year ?? ($currentYear . ' - ' . ($currentYear + 1));
        $programNames = $students->pluck('program.name_km')->filter()->unique()->values()->implode(', ');
        $facultyNames = $students->pluck('program.department.faculty.name_km')->filter()->unique()->values()->implode(', ');
        $generationNames = $students->pluck('generation')->filter()->unique()->values()->implode(', ');
        $lecturerName = $courseOffering->lecturer->name ?? '';

        $assessmentHeaders = $assessments->map(function($a) {
            $type = ($a instanceof \App\Models\Assignment) ? 'assignment' : (($a instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
            $typeName = $type === 'assignment' ? 'កិច្ចការ' : ($type === 'quiz' ? 'Quiz' : 'ប្រឡង');
            return [
                'name' => $a->title ?? $typeName,
                'type' => $type,
                'key' => $type . '_' . $a->id,
                'max' => $a->max_score ?? 100,
            ];
        });

        $assignmentHeaders = $assessmentHeaders->where('type', 'assignment');
        $examHeaders = $assessmentHeaders->where('type', 'exam');
        $quizHeaders = $assessmentHeaders->where('type', 'quiz');

        $passCount = 0;
        $failCount = 0;
        foreach ($students as $student) {
            $grade = \App\Services\GradingService::getLetterGrade($student->temp_total);
            if (\App\Services\GradingService::isPassing($grade)) { $passCount++; } else { $failCount++; }
        }

        function toKhmerNums($n) {
            $khmer = ['០','១','២','៣','៤','៥','៦','៧','៨','៩'];
            return str_replace(range(0,9), $khmer, $n);
        }
    @endphp

    <div class="doc-header">
        <div class="logo">
            <img src="{{ asset('assets/image/nmu_Logo.png') }}" alt="NMU Logo">
        </div>
        <div class="text header-kh">
            <p class="line1">ព្រះរាជាណាចក្រកម្ពុជា</p>
            <p class="line2">ជាតិ សាសនា ព្រះមហាក្សត្រ</p>
            <p class="line-motto"><img src="{{ asset('assets/image/2.png') }}" alt="motto" style="height:30px;"></p>
            <p class="line3">ឆ្នាំសិក្សា {{ $academicYearName }}</p>
            <p class="line4">សាកលវិទ្យាល័យជាតិមានជ័យ</p>
            <p class="line5">{{ $facultyNames }}</p>
            <p class="line6">តារាងពិន្ទុរួម</p>
            <p class="line7">មុខវិជ្ជា៖ {{ $courseOffering->course->title_km }} | ចំនួនសិស្សចុះឈ្មោះ៖ {{ toKhmerNums((string) $totalStudents) }} នាក់</p>
        </div>
    </div>

    <div class="info-row">
        <span>គ្រូបង្រៀន៖ <strong>{{ $lecturerName }}</strong></span>
        <span>កម្មវិធីសិក្សា៖ <strong>{{ $programNames }}</strong></span>
        <span>ជំនាន់៖ <strong>{{ $generationNames }}</strong></span>
        <span>សរុប៖ <strong>{{ $totalStudents }}</strong> នាក់ | ជាប់ <strong>{{ $passCount }}</strong> | ធ្លាក់ <strong>{{ $failCount }}</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-stt">ល.រ</th>
                <th class="col-id">អត្តសញ្ញាណ</th>
                <th class="col-name text-left">គោត្តនាម និងនាម</th>
                <th class="col-gender">ភេទ</th>
                <th class="col-score" style="width:50px;">វត្តមាន</th>
                @foreach($assignmentHeaders as $ah)
                    <th class="col-score" style="width:50px;">{{ $ah['name'] }}<br><span style="font-size:11px;color:#666;">/ {{ $ah['max'] }}</span></th>
                @endforeach
                @foreach($examHeaders as $eh)
                    <th class="col-score" style="width:50px;">{{ $eh['name'] }}<br><span style="font-size:11px;color:#666;">/ {{ $eh['max'] }}</span></th>
                @endforeach
                @foreach($quizHeaders as $qh)
                    <th class="col-score" style="width:50px;">{{ $qh['name'] }}<br><span style="font-size:11px;color:#666;">/ {{ $qh['max'] }}</span></th>
                @endforeach
                <th class="col-score" style="width:50px; background:#f0f0f0;">សរុប</th>
                <th class="col-grade" style="width:55px;">និទ្ទេស</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                @php
                    $gender = $student->profile?->gender ?? '';
                    $genderText = $gender === 'male' ? 'ប' : ($gender === 'female' ? 'ស' : '');
                    $attendanceScore = (float) ($student->getAttendanceScoreByCourse($courseOffering->id) ?? 0);
                @endphp
                <tr>
                    <td>{{ $student->rank ?? ($index + 1) }}</td>
                    <td>{{ $student->student_id_code ?? $student->id }}</td>
                    <td class="text-left">{{ $student->studentProfile->full_name_km ?? $student->profile->full_name_km ?? $student->name }}</td>
                    <td>{{ $genderText }}</td>
                    <td>{{ $attendanceScore }}</td>
                    @foreach($assignmentHeaders as $ah)
                        <td>{{ $gradebook[$student->id][$ah['key']] ?? 0 }}</td>
                    @endforeach
                    @foreach($examHeaders as $eh)
                        <td>{{ $gradebook[$student->id][$eh['key']] ?? 0 }}</td>
                    @endforeach
                    @foreach($quizHeaders as $qh)
                        <td>{{ $gradebook[$student->id][$qh['key']] ?? 0 }}</td>
                    @endforeach
                    <td style="background:#f0f0f0; font-weight:bold;">{{ number_format($student->temp_total, 1) }}</td>
                    <td style="font-weight:bold;">{{ $student->letterGrade }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        @php
            $now = \Carbon\Carbon::now();
            $day = toKhmerNums($now->format('d'));
            $monthKhmer = ['មករា','កុម្ភៈ','មីនា','មេសា','ឧសភា','មិថុនា','កក្កដា','សីហា','កញ្ញា','តុលា','វិច្ឆិកា','ធ្នូ'][$now->month - 1];
            $year = toKhmerNums((string)$now->year);
        @endphp
        <div class="signature-block">
            <div class="date-line">ថ្ងៃទី {{ $day }} ខែ {{ $monthKhmer }} ឆ្នាំ {{ $year }}</div>
            <div class="title">ហត្ថលេខារបស់អ្នករៀបចំ</div>
            <div class="sign-line"></div>
        </div>
        <div class="signature-block">
            <div class="date-line">ថ្ងៃទី {{ $day }} ខែ {{ $monthKhmer }} ឆ្នាំ {{ $year }}</div>
            <div class="title">ហត្ថលេខារបស់នាយកសាលា</div>
            <div class="sign-line"></div>
        </div>
    </div>
</div>

</body>
</html>
