{{-- filepath: /var/www/pm-training-app/resources/views/pdf/reports/employee-training.blade.php --}}
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Employee Training Report</title>
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: 'solaimanlipi', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .bangla-text {
            font-family: 'solaimanlipi', sans-serif;
            direction: ltr;
            unicode-bidi: embed;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            /*border-bottom: 2px solid #333;*/
        }

        .header h1 {
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .header p {
            margin: 3px 0;
            font-size: 14px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }

        td {
            font-size: 10px;
            line-height: 1.4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            /*border-top: 1px solid #ddd;*/
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header bangla-text">
        <h1>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</h1>
        <p>পরিকল্পনা মন্ত্রণালয়</p>
        <p>পরিকল্পনা বিভাগ</p>
        <p style="margin-top: 10px; font-weight: bold;">বিষয়ঃ একক কর্মচারী/কর্মকর্তার বিষয় ভিত্তিক প্রশিক্ষনের প্রতিবেদন</p>
    </div>

    <!-- Unified Employee Data Table -->
    <table>
        <thead>
            <tr>
                <th class="bangla-text">ক্রমিক নং</th>
                <th class="bangla-text">কর্মকর্তার নাম</th>
                <th class="bangla-text">পদবী</th>
                <th class="bangla-text">মোবাইল নম্বর</th>
                <th class="bangla-text">কর্মস্থল</th>
                <th class="bangla-text">আয়োজনকারী</th>
                <th class="bangla-text">প্রশিক্ষণের নাম</th>
                <th class="bangla-text">স্থানীয় প্রশিক্ষণ</th>
                <th class="bangla-text">বৈদেশিক প্রশিক্ষণ</th>
                <th class="bangla-text">আরম্ভের তারিখ</th>
                <th class="bangla-text">শেষের তারিখ</th>
                <th class="bangla-text">মোট দিন</th>
            </tr>
        </thead>
        <tbody>
            @php $serial = 1; @endphp
            {{-- @foreach ($reportData as $training)
                @if(isset($training['employees']) && count($training['employees']) > 0) --}}
                @foreach ($reportData as $employee)
                    <tr>
                        <td class="number-cell">{{ $serial++ }}</td>
                        <td class="employee-name bangla-text">{{ $employee['employee_name'] ?? 'তথ্য নেই' }}</td>
                        <td class="bangla-text">{{ $employee['designation'] ?? 'তথ্য নেই' }}</td>
                        <td class="mixed-content">{{ $employee['mobile'] ?? 'তথ্য নেই' }}</td>
                        <td class="bangla-text">{{ $employee['working_place'] ?? 'তথ্য নেই' }}</td>
                        <td class="bangla-text">{{ $employee['organizer_name'] ?? 'তথ্য নেই' }}</td>
                        <td class="training-subject bangla-text">{{ $employee['training_name'] ?? 'তথ্য নেই' }}</td>
                        <td class="check-mark">
                            {{ (isset($employee['training_type']) && $employee['training_type'] == 'স্থানীয় প্রশিক্ষণ') ? 'স্থানীয়' : '' }}
                        </td>
                        <td class="check-mark">
                            {{ (isset($employee['training_type']) && $employee['training_type'] == 'বৈদেশিক প্রশিক্ষণ') ? $employee['training_countries'] : '' }}
                        </td>
                        <td class="date-cell">{{ $employee['start_date'] ?? 'তথ্য নেই' }}</td>
                        <td class="date-cell">{{ $employee['end_date'] ?? 'তথ্য নেই' }}</td>
                        <td class="number-cell">{{ $employee['total_days'] ?? '০' }} দিন</td>
                    </tr>
                    @endforeach

        </tbody>
    </table>

    <!-- Summary Footer -->
    @if(count($reportData) > 1)
    <div class="footer bangla-text">
        {{-- <p><strong>মোট প্রশিক্ষণের সংখ্যা:</strong> {{ count($reportData) }}</p>
        <p><strong>মোট অংশগ্রহণকারী:</strong>
            {{ collect($reportData)->sum(function($training) {
                return isset($training['employees']) ? count($training['employees']) : 0;
            }) }}
        </p> --}}
        <p style="margin-top: 15px; font-size: 10px; color: #888;">
            এই প্রতিবেদনটি {{ $generatedAt->format('d/m/Y H:i:s') }} তারিখে স্বয়ংক্রিয়ভাবে তৈরি করা হয়েছে।
        </p>
    </div>
    @endif
</body>
</html>