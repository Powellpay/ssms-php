@extends('reports.layouts.base')

@section('report-title')
    Student Report Card
@endsection

@section('content')
    {{-- Student Details --}}
    <table style="width: 100%; margin-bottom: 14px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding: 2px 4px;"><strong>Student Name:</strong> {{ $student_name }}</td>
            <td style="width: 50%; padding: 2px 4px;"><strong>Admission No:</strong> {{ $admission_no }}</td>
        </tr>
        <tr>
            <td style="padding: 2px 4px;"><strong>Class:</strong> {{ $class }}</td>
            <td style="padding: 2px 4px;"><strong>Term:</strong> {{ $term }}</td>
        </tr>
        @if(!empty($academic_year))
        <tr>
            <td style="padding: 2px 4px;" colspan="2"><strong>Academic Year:</strong> {{ $academic_year }}</td>
        </tr>
        @endif
    </table>

    {{-- Subject Results --}}
    <h4 style="color: #1f6f43; margin: 16px 0 8px; border-bottom: 2px solid #1f6f43; padding-bottom: 4px;">
        Subject Results
    </h4>

    <table style="width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 16px;">
        <colgroup>
            <col style="width: 30%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col style="width: 12%;">
            <col style="width: 10%;">
            <col style="width: 18%;">
        </colgroup>
        <thead>
            <tr style="background-color: #1f6f43; color: #fff;">
                <th style="padding: 6px 8px; text-align: left;">Subject</th>
                <th style="padding: 6px 8px; text-align: center;">CA Score</th>
                <th style="padding: 6px 8px; text-align: center;">Exam Score</th>
                <th style="padding: 6px 8px; text-align: center;">Total</th>
                <th style="padding: 6px 8px; text-align: center;">Grade</th>
                <th style="padding: 6px 8px; text-align: left;">Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subject_results as $result)
            <tr style="border-bottom: 1px solid #e0e0e0;">
                <td style="padding: 5px 8px;">{{ $result['subject'] }}</td>
                <td style="padding: 5px 8px; text-align: center;">{{ $result['ca_score'] }}</td>
                <td style="padding: 5px 8px; text-align: center;">{{ $result['eot_score'] }}</td>
                <td style="padding: 5px 8px; text-align: center; font-weight: 700;">{{ $result['total'] }}</td>
                <td style="padding: 5px 8px; text-align: center; font-weight: 700; color: #1f6f43;">{{ $result['grade'] }}</td>
                <td style="padding: 5px 8px;">{{ $result['remark'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 10px; text-align: center; color: #999;">No subject results recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Generic Skills Ratings --}}
    <h4 style="color: #1f6f43; margin: 16px 0 8px; border-bottom: 2px solid #1f6f43; padding-bottom: 4px;">
        Generic Skills Ratings
    </h4>

    <table style="width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 16px;">
        <colgroup>
            <col style="width: 35%;">
            <col style="width: 20%;">
            <col style="width: 45%;">
        </colgroup>
        <thead>
            <tr style="background-color: #1f6f43; color: #fff;">
                <th style="padding: 6px 8px; text-align: left;">Skill</th>
                <th style="padding: 6px 8px; text-align: center;">Rating</th>
                <th style="padding: 6px 8px; text-align: left;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($skill_ratings as $skill)
            <tr style="border-bottom: 1px solid #e0e0e0;">
                <td style="padding: 5px 8px;">{{ $skill['skill_name'] }}</td>
                <td style="padding: 5px 8px; text-align: center;">{{ $skill['rating'] }}</td>
                <td style="padding: 5px 8px;">{{ $skill['remarks'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="padding: 10px; text-align: center; color: #999;">No skill ratings recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Attendance Summary --}}
    <h4 style="color: #1f6f43; margin: 16px 0 8px; border-bottom: 2px solid #1f6f43; padding-bottom: 4px;">
        Attendance Summary
    </h4>

    <table style="width: 60%; border-collapse: collapse; font-size: 10pt; margin-bottom: 16px;">
        <colgroup>
            <col style="width: 50%;">
            <col style="width: 50%;">
        </colgroup>
        <tr>
            <td style="padding: 5px 8px; border: 1px solid #ddd;"><strong>Days Present:</strong></td>
            <td style="padding: 5px 8px; border: 1px solid #ddd; text-align: center;">{{ $days_present }}</td>
        </tr>
        <tr>
            <td style="padding: 5px 8px; border: 1px solid #ddd;"><strong>Days Absent:</strong></td>
            <td style="padding: 5px 8px; border: 1px solid #ddd; text-align: center;">{{ $days_absent }}</td>
        </tr>
    </table>

    {{-- Comments --}}
    <h4 style="color: #1f6f43; margin: 16px 0 8px; border-bottom: 2px solid #1f6f43; padding-bottom: 4px;">
        Teacher Comments
    </h4>

    <table style="width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 16px;">
        <tr>
            <td style="padding: 6px 8px; border: 1px solid #ddd; width: 25%; vertical-align: top;"><strong>Class Teacher:</strong></td>
            <td style="padding: 6px 8px; border: 1px solid #ddd;">{{ $class_teacher_comment ?: 'No comment.' }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 8px; border: 1px solid #ddd; vertical-align: top;"><strong>Head Teacher:</strong></td>
            <td style="padding: 6px 8px; border: 1px solid #ddd;">{{ $head_teacher_comment ?: 'No comment.' }}</td>
        </tr>
    </table>

    {{-- Next Term --}}
    <table style="width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 8px;">
        <tr>
            <td style="padding: 6px 8px; border: 1px solid #1f6f43; background-color: #e8f5e9; text-align: center;">
                <strong>Next Term Begins:</strong> {{ $next_term_begins }}
            </td>
        </tr>
    </table>

    <div style="font-size: 8pt; color: #999; text-align: center; margin-top: 20px;">
        Report issued on {{ $date_issued }}
    </div>
@endsection
