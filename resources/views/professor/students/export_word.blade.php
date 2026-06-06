<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Khmer OS Siemreap', 'Segoe UI', sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; font-size: 10px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 10px; }
        .student-name { text-align: left; padding-left: 8px; font-weight: bold; }
        .total-cell { background-color: #eef2ff; font-weight: bold; color: #4338ca; }
    </style>
</head>
<body>
    <div class="header">{{ __('តារាងពិន្ទុរួម') }} (Gradebook)</div>
    <p>{{ __('មុខវិជ្ជា៖') }} {{ $courseOffering->course->title_km ?? $courseOffering->course->title_en }}</p>
    <p>{{ __('សាស្ត្រាចារ្យ៖') }} {{ Auth::user()->name }}</p>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">Rank</th>
                <th style="width: 180px;">{{ __('ឈ្មោះនិស្សិត') }}</th>
                <th style="width: 60px;">{{ __('វត្តមាន') }}<br>(15%)</th>
                
                {{-- បង្ហាញក្បាលតារាងតាមប្រភេទការវាយតម្លៃ --}}
                @foreach($assessments as $assessment)
                    @php 
                        $typeLabel = ($assessment instanceof \App\Models\Assignment) ? __('កិច្ចការ') : (($assessment instanceof \App\Models\Quiz) ? 'Quiz' : __('ប្រឡង'));
                    @endphp
                    <th>
                        {{ $typeLabel }}<br>
                        {{ $assessment->title_km }}<br>
                        ({{ $assessment->max_score }} ពិន្ទុ)
                    </th>
                @endforeach

                <th class="total-cell">{{ __('សរុប') }} (100)</th>
                <th style="width: 50px;">{{ __('និទ្ទេស') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                @php 
                    $attendanceScore = $student->getAttendanceScoreByCourse($courseOffering->id);
                    $rowTotal = $attendanceScore; 
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="student-name">
                        {{ $student->profile->full_name_km ?? $student->name }}<br>
                        <span style="font-size: 8px; color: #666;">ID: {{ $student->student_id_code }}</span>
                    </td>
                    <td>{{ number_format($attendanceScore, 1) }}</td>

                    {{-- បង្ហាញពិន្ទុតាមការវាយតម្លៃនីមួយៗ --}}
                    @foreach ($assessments as $assessment)
                        @php 
                            $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' : (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');
                            $score = $gradebook[$student->id][$type . '_' . $assessment->id] ?? 0;
                            $rowTotal += $score;
                        @endphp
                        <td style="{{ $score < ($assessment->max_score/2) ? 'color: red;' : '' }}">
                            {{ number_format($score, 1) }}
                        </td>
                    @endforeach

                    <td class="total-cell">{{ number_format($rowTotal, 1) }}</td>
                    
                    {{-- គណនានិទ្ទេស --}}
                    @php
                        $grade = 'F';
                        if ($rowTotal >= 85) $grade = 'A';
                        elseif ($rowTotal >= 80) $grade = 'B+';
                        elseif ($rowTotal >= 70) $grade = 'B';
                        elseif ($rowTotal >= 65) $grade = 'C+';
                        elseif ($rowTotal >= 50) $grade = 'C';
                    @endphp
                    <td style="font-weight: bold; {{ $grade == 'F' ? 'color: red;' : '' }}">
                        {{ $grade }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 10px;">
        <p>{{ __('កាលបរិច្ឆេទបញ្ចេញឯកសារ៖') }} {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>