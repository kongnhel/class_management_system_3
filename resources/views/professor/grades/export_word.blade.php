<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Khmer OS Siemreap', 'Segoe UI', sans-serif; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #000000; padding: 6px 4px; text-align: center; font-size: 10px; }
        
        /* Header Styling */
        .header-title { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 5px; }
        .sub-header { font-size: 12px; text-align: center; margin-bottom: 15px; color: #475569; }
        
        /* Column Specific */
        .student-name { text-align: left; padding-left: 8px; font-weight: bold; width: 180px; }
        .rank-col { width: 35px; background-color: #f8fafc; }
        .attendance-col { background-color: #f0f9ff; width: 50px; }
        .total-col { background-color: #eef2ff; font-weight: bold; width: 60px; color: #4338ca; }
        .grade-col { width: 45px; font-weight: bold; }
        
        /* Assessment Labels (Colors like Web) */
        .label-assignment { color: #2563eb; font-size: 8px; }
        .label-quiz { color: #d97706; font-size: 8px; }
        .label-exam { color: #e11d48; font-size: 8px; }
        
        .fail-score { color: #e11d48; }
    </style>
</head>
<body>
    <div class="header-title">តារាងពិន្ទុរួម (Gradebook)</div>
    <div class="sub-header">
        មុខវិជ្ជា៖ {{ $courseOffering->course->title_km }}<br>
        សាស្ត្រាចារ្យ៖ {{ Auth::user()->name }}
    </div>

    <table>
        <thead>
            <tr style="background-color: #f1f5f9;">
                <th class="rank-col">Rank</th>
                <th>{{ __('ឈ្មោះនិស្សិត') }}</th>
                <th class="attendance-col">{{ __('វត្តមាន') }}<br>(15%)</th>
                
                @foreach($assessments as $assessment)
                    @php 
                        $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                        $typeLabel = ($type === 'assignment' ? __('កិច្ចការ') : ($type === 'quiz' ? 'Quiz' : __('ប្រឡង')));
                    @endphp
                    <th>
                        <span class="label-{{ $type }}">{{ $typeLabel }}</span><br>
                        {{ $assessment->title_km }}<br>
                        <span style="font-size: 8px; color: #64748b;">({{ $assessment->max_score }} ពិន្ទុ)</span>
                    </th>
                @endforeach

                <th class="total-col">{{ __('សរុប') }}<br>(100)</th>
                <th class="grade-col">{{ __('និទ្ទេស') }}</th>
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
                    <td class="rank-col">{{ $index + 1 }}</td>
                    <td class="student-name">
                        {{ $student->profile->full_name_km ?? $student->name }}<br>
                        <span style="font-size: 8px; font-weight: normal; color: #64748b;">ID: {{ $student->student_id_code }}</span>
                    </td>
                    <td class="attendance-col">{{ number_format($attendanceScore, 1) }}</td>

                    @foreach ($assessments as $assessment)
                        @php 
                            $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                            $score = $gradebook[$student->id][$type . '_' . $assessment->id] ?? 0;
                            if ($type === 'quiz') { $quizBonus += $score; } else { $baseScore += $score; }
                        @endphp
                        <td class="{{ $score < ($assessment->max_score/2) ? 'fail-score' : '' }}">
                            {{ number_format($score, 1) }}
                        </td>
                        
                    @endforeach

                    @php $rowTotal = min($baseScore + $quizBonus, 100); @endphp
                    <td class="total-col">{{ number_format($rowTotal, 1) }}</td>

                    @php
                        $grade = 'F';
                        if ($rowTotal >= 85) $grade = 'A';
                        elseif ($rowTotal >= 80) $grade = 'B+';
                        elseif ($rowTotal >= 70) $grade = 'B';
                        elseif ($rowTotal >= 65) $grade = 'C+';
                        elseif ($rowTotal >= 50) $grade = 'C';
                    @endphp
                    <td class="grade-col" style="{{ $grade == 'F' ? 'color: #e11d48;' : 'color: #059669;' }}">
                        {{ $grade }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 15px; font-size: 9px; color: #64748b; text-align: right;">
        កាលបរិច្ឆេទបញ្ចេញរបាយការណ៍៖ {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>