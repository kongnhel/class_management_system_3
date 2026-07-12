<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4 landscape; margin: 1cm; }
        body { 
            font-family: 'Khmer OS Siemreap', 'Battambang', sans-serif; 
            font-size: 10pt;
            color: #000;
            margin: 0;
            padding: 10px;
        }
        
        /* Header */
        .header-container { 
            display: flex; 
            align-items: flex-start; 
            margin-bottom: 10px;
        }
        .header-left { 
            width: 200px; 
            text-align: center;
        }
        .header-right { 
            flex: 1; 
            text-align: center;
        }
        .uni-name-kh { 
            font-size: 16pt; 
            font-weight: bold; 
            color: #003366;
            margin-bottom: 2px;
        }
        .uni-name-en { 
            font-size: 11pt; 
            color: #003366;
            margin-bottom: 2px;
        }
        .uni-address { 
            font-size: 9pt; 
            color: #666;
        }
        
        /* Title Section */
        .title-section {
            text-align: center;
            margin: 15px 0 20px 0;
        }
        .report-title {
            font-size: 13pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
        .report-subtitle {
            font-size: 10pt;
            color: #333;
        }
        
        /* Table */
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin-top: 10px;
        }
        table, th, td { 
            border: 1.5px solid #000; 
        }
        th, td { 
            padding: 4px 3px; 
            text-align: center; 
            font-size: 9pt;
            vertical-align: middle;
        }
        
        /* Header Row */
        thead tr { 
            background-color: #1a365d; 
        }
        thead th { 
            color: white; 
            font-weight: bold;
            font-size: 8pt;
            padding: 6px 3px;
        }
        
        /* Column Widths */
        .col-no { width: 25px; }
        .col-name-kh { width: 80px; text-align: left; padding-left: 4px; }
        .col-name-en { width: 100px; text-align: left; padding-left: 4px; }
        .col-id { width: 90px; }
        .col-gender { width: 35px; }
        .col-course { width: 65px; }
        .col-total { width: 45px; font-weight: bold; }
        .col-grade { width: 40px; font-weight: bold; }
        .col-rank { width: 30px; }
        .col-status { width: 45px; }
        
        /* Data */
        .student-name-km { font-weight: bold; font-size: 9pt; }
        .student-name-en { font-size: 8pt; color: #333; }
        .student-id { font-size: 8pt; }
        
        /* Grade Colors */
        .grade-a { color: #006600; font-weight: bold; }
        .grade-b { color: #0066cc; font-weight: bold; }
        .grade-c { color: #cc6600; font-weight: bold; }
        .grade-f { color: #cc0000; font-weight: bold; }
        
        /* Missing Grade - Yellow Highlight */
        .missing-grade { 
            background-color: #ffeb3b; 
        }
        
        /* Status */
        .status-pass { color: #006600; font-weight: bold; }
        .status-fail { color: #cc0000; font-weight: bold; }
        
        /* Footer */
        .footer-section {
            margin-top: 20px;
            font-size: 9pt;
            color: #666;
        }
        .signature-area {
            text-align: right;
            margin-top: 40px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    {{-- University Header --}}
    <table style="border: none; width: 100%;">
        <tr style="border: none;">
            <td style="border: none; width: 150px; vertical-align: top; text-align: center;">
                <div style="font-size: 14pt; font-weight: bold; color: #003366;">NMU</div>
                <div style="font-size: 8pt; color: #003366;"> NATIONAL MEANCHEY</div>
                <div style="font-size: 8pt; color: #003366;">UNIVERSITY</div>
            </td>
            <td style="border: none; vertical-align: top;">
                <div class="uni-name-kh">សាកលវិទ្យាល័យជាតិមាត្រជាយ</div>
                <div class="uni-name-en">National Meanchey University</div>
                <div class="uni-address">អគារលេខ ៨៣២ ខេត្តបាត់ដំបង</div>
            </td>
        </tr>
    </table>

    {{-- Report Title --}}
    <div class="title-section">
        <div class="report-title">របាយការណ៍ការសិក្សារបស់និស្សិត និងការប្រឡងក្នុងឆ្នាំសិក្សា</div>
        <div class="report-subtitle">
            ឆ្នាំសិក្សា៖ {{ $courseOffering->academic_year }} &nbsp; ឆមាស៖ {{ $courseOffering->semester }}
            &nbsp;|&nbsp; មុខវិជ្ជា៖ {{ $courseOffering->course->title_km }}
            &nbsp;|&nbsp; សាស្ត្រាចារ្យ៖ {{ Auth::user()->name }}
        </div>
    </div>

    {{-- Grade Table --}}
    <table>
        <thead>
            <tr>
                <th class="col-no">ល.រ</th>
                <th class="col-name-kh">ឈ្មោះ-និស្សិត</th>
                <th class="col-name-en">អត្តលេខ</th>
                <th class="col-id">ភេទ</th>
                <th class="col-gender">មុខវិជ្ជា/ការពិន្ទុ</th>
                
                @foreach($assessments as $assessment)
                    @php 
                        $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                        $typeLabel = ($type === 'assignment' ? 'Assignment' : ($type === 'quiz' ? 'Quiz' : 'Exam'));
                    @endphp
                    <th class="col-course">{{ $assessment->title_en ?? $typeLabel }}</th>
                @endforeach

                <th class="col-total">ពិន្ទុសរុប</th>
                <th class="col-grade">និទ្ទេស</th>
                <th class="col-rank">ល.រ</th>
                <th class="col-status">ស្ថានភាព</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $index => $student)
                @php 
                    $attendanceScore = $student->getAttendanceScoreByCourse($courseOffering->id);
                    $baseScore = $attendanceScore;
                    $quizBonus = 0;
                @endphp
                <tr>
                    <td class="col-no">{{ $index + 1 }}</td>
                    <td class="col-name-kh">
                        <span class="student-name-km">{{ $student->studentProfile?->full_name_km ?? $student->name }}</span>
                    </td>
                    <td class="col-name-en">
                        <span class="student-name-en">{{ $student->studentProfile?->full_name_en ?? strtoupper($student->name) }}</span>
                    </td>
                    <td class="col-id student-id">{{ $student->student_id_code ?? '—' }}</td>
                    <td class="col-gender">{{ $student->studentProfile?->gender === 'male' ? 'ប្រុស' : ($student->studentProfile?->gender === 'female' ? 'ស្រី' : '—') }}</td>

                    @foreach ($assessments as $assessment)
                        @php 
                            $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                            $score = $gradebook[$student->id][$type . '_' . $assessment->id] ?? null;
                            if ($type === 'quiz') { $quizBonus += ($score ?? 0); } else { $baseScore += ($score ?? 0); }
                        @endphp
                        <td class="col-course {{ is_null($score) ? 'missing-grade' : '' }}">
                            @if(!is_null($score))
                                @php
                                    $pct = $assessment->max_score > 0 ? ($score / $assessment->max_score) * 100 : 0;
                                    $letter = 'F';
                                    if ($pct >= 85) $letter = 'A';
                                    elseif ($pct >= 75) $letter = 'B';
                                    elseif ($pct >= 65) $letter = 'C';
                                    $letterClass = match(true) {
                                        $letter === 'A' => 'grade-a',
                                        $letter === 'B' => 'grade-b',
                                        $letter === 'C' => 'grade-c',
                                        default => 'grade-f'
                                    };
                                @endphp
                                <span class="{{ $letterClass }}">{{ $letter }}</span>
                            @else
                                &nbsp;
                            @endif
                        </td>
                    @endforeach

                    @php $rowTotal = min($baseScore + $quizBonus, 100); @endphp
                    <td class="col-total">{{ number_format($rowTotal, 1) }}</td>

                    @php
                        $grade = 'F';
                        if ($rowTotal >= 85) $grade = 'A';
                        elseif ($rowTotal >= 80) $grade = 'B+';
                        elseif ($rowTotal >= 70) $grade = 'B';
                        elseif ($rowTotal >= 65) $grade = 'C+';
                        elseif ($rowTotal >= 50) $grade = 'C';
                        
                        $gradeClass = match(true) {
                            $grade === 'A' => 'grade-a',
                            in_array($grade, ['B+', 'B']) => 'grade-b',
                            in_array($grade, ['C+', 'C']) => 'grade-c',
                            default => 'grade-f'
                        };
                        
                        $isPassing = $rowTotal >= 50;
                    @endphp
                    <td class="col-grade {{ $gradeClass }}">{{ $grade }}</td>
                    <td class="col-rank">{{ $index + 1 }}</td>
                    <td class="col-status {{ $isPassing ? 'status-pass' : 'status-fail' }}">
                        {{ $isPassing ? 'ជាប់' : 'មិនជាប់' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Signature --}}
    <div class="signature-area">
        <div>ថ្ងៃទី {{ date('d') }} ខែ{{ ['មករា','កុម្ភៈ','មីនា','មេសា','ឧសភា','មិថុនា','កក្កដា','សីហា','កញ្ញា','តុលា','វិច្ឆិកា','ធ្នូ'][date('m')-1] }} ឆ្នាំ{{ date('Y') }}</div>
        <div style="margin-top: 5px; font-style: italic;">អ្នកនិពន្ធ</div>
    </div>
</body>
</html>
