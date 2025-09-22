<!DOCTYPE html>
<html lang="bn">
<html>

<head>
    <meta charset="utf-8">
    <title>Training Assignments Report</title>
    <style>
        body {
            font-family: 'solaimanlipi', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .bangla-text {
            font-family: 'solaimanlipi', sans-serif;
            direction: ltr;
            unicode-bidi: embed;
        }

        .filters {
            margin-bottom: 15px;
            background-color: #f9f9f9;
            padding: 10px;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 8px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
        }

        .government-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .government-header h1 {
            font-size: 16px;
            margin: 5px 0;
        }

        .government-header p {
            font-size: 12px;
            margin: 3px 0;
        }

        .report-subject {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <!-- Government Header Section -->
    <div class="government-header bangla-text">
        <h1>গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</h1>
        <p>পরিকল্পনা মন্ত্রণালয়</p>
        <p>পরিকল্পনা বিভাগ</p>
        <div class="report-subject">
            বিষয়ঃ প্রশিক্ষণ বরাদ্দের প্রতিবেদন
        </div>
    </div>

    <table>
        <thead>
            <tr class="bangla-text">
                <th class="bangla-text">ক্রমিক নং</th>
                <th class="bangla-text">কর্মচারীর নাম</th>
                <th class="bangla-text">পদবী</th>
                <th class="bangla-text">প্রশিক্ষণের নাম</th>
                <th class="bangla-text">আয়োজক</th>
                <th class="bangla-text">কর্মক্ষেত্র</th>
                <th class="bangla-text">শুরুর তারিখ</th>
                <th class="bangla-text">শেষের তারিখ</th>
                <th class="bangla-text">মোট দিন</th>
                <th class="bangla-text">বরাদ্দের তারিখ</th>
            </tr>
        </thead>
        <tbody>
            @php $serial = 1; @endphp
            @forelse($assignmentsData as $assignment)
                <tr>
                    <td>{{ $serial++ }}</td>
                    <td>{{ $assignment->employee->name ?? 'প্রযোজ্য নয়' }}</td>
                    <td>{{ $assignment->employee->designation->name ?? 'প্রযোজ্য নয়' }}</td>
                    <td>{{ $assignment->training->name ?? 'প্রযোজ্য নয়' }}</td>
                    <td>{{ $assignment->training->organizer->name ?? 'প্রযোজ্য নয়' }}</td>
                    <td>
                        @php
                            $workingPlaceNames = [
                                1 => 'কেন্দ্রীয় কার্যালয়',
                                2 => 'বিভাগীয় কার্যালয়',
                                3 => 'জেলা কার্যালয়',
                                4 => 'উপজেলা কার্যালয়',
                            ];
                        @endphp
                        {{ $workingPlaceNames[$assignment->employee->working_place] ?? 'প্রযোজ্য নয়' }}
                    </td>
                    <td>{{ $assignment->groupTraining->start_date ? \Carbon\Carbon::parse($assignment->groupTraining->start_date)->format('d/m/Y') : 'প্রযোজ্য নয়' }}
                    </td>
                    <td>{{ $assignment->groupTraining->end_date ? \Carbon\Carbon::parse($assignment->groupTraining->end_date)->format('d/m/Y') : 'প্রযোজ্য নয়' }}
                    </td>
                    <td>{{ $assignment->groupTraining->total_days ?? 'প্রযোজ্য নয়' }}</td>
                    <td>{{ $assignment->assigned_at ? \Carbon\Carbon::parse($assignment->assigned_at)->format('d/m/Y') : 'প্রযোজ্য নয়' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; font-style: italic;" class="bangla-text">
                        নির্বাচিত মানদণ্ডের জন্য কোন বরাদ্দ পাওয়া যায়নি।
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer bangla-text">
        <p>মোট বরাদ্দ: {{ count($assignmentsData) }} টি</p>
        <p>তৈরির তারিখ: {{ $generatedAt->format('d/m/Y, g:i A') }}</p>
    </div>
</body>

</html>
