<?php

namespace App\Domain\Attendance\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $attendanceId = $this->route('attendance');

        return [
            'student_id' => [
                'required',
                'exists:students,id',
                Rule::unique('attendance')->where(fn($q) => $q->where('attendance_date', $this->attendance_date))->ignore($attendanceId),
            ],
            'term_id' => 'required|exists:terms,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:Present,Absent,Late,Excused',
        ];
    }
}
