<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>តារាងពិន្ទុ - {{ $courseOffering->course->code }}</title>
    <style>
        @font-face {
            font-family: 'KhmerOSMoul';
            src: url('{{ public_path('fonts/KhmerOSMoul.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        body { 
            font-family: 'KhmerOSMoul', sans-serif; /* Use the Khmer font */
            font-size: 14px; /* Increased for better readability */
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Added subtle shadow for depth */
            border-radius: 8px; /* Rounded corners */
            overflow: hidden; /* Ensures shadow and border-radius work together */
        }
        th, td {
            border: 1px solid #dee2e6; /* Lighter border color for a softer look */
            padding: 12px; /* Increased padding */
            text-align: left;
            word-wrap: break-word;
        }
        th {
            background-color: #e9ecef; /* Slightly darker header background */
            font-weight: bold;
            color: #495057; /* Darker text for contrast */
            text-transform: uppercase;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px; /* Increased font size */
            font-weight: bold;
            color: #212529;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #6c757d;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa; /* Zebra stripes for readability */
        }
        tbody tr:hover {
            background-color: #e2f4f1; /* subtle hover effect */
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>តារាងពិន្ទុ</h1>
        <p><strong>{{ __('មុខវិជ្ជា:') }}</strong> {{ $courseOffering->course->title_km }} ({{ $courseOffering->course->code }})</p>
        <p><strong>ឆ្នាំសិក្សា:</strong> {{ $courseOffering->academic_year }} | <strong>ឆមាស:</strong> {{ $courseOffering->semester }}</p>
        <p><strong>សាស្រ្តាចារ្យ:</strong> {{ $courseOffering->lecturer->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('ឈ្មោះនិស្សិត') }}</th>
                @foreach($assessments as $assessment)
                    <th>{{ $assessment->title_km }} ({{ $assessment->max_score }})</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr>
                    <td>{{ $student->profile->full_name_km ?? $student->name }}</td>
                    @foreach ($assessments as $assessment)
                        <td style="text-align: center;">
                            {{ $gradebook[$student->id][$assessment->id] ?? '-' }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $assessments->count() + 1 }}" style="text-align: center;">
                        មិនទាន់មាននិស្សិតចុះឈ្មោះក្នុងមុខវិជ្ជានេះទេ។
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
