<?php

namespace App\Domain\Attendance\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'term_id' => 'required|integer|exists:terms,id',
            'attendance_date' => 'required|date',
            'records' => 'required|array|min:1',
            'records.*.student_id' => 'required|integer|exists:students,id',
            'records.*.status' => 'required|string|in:Present,Absent,Late,Excused',
        ];
    }

    public function messages(): array
    {
        return [
            'records.required' => 'At least one attendance record is required.',
            'records.*.student_id.required' => 'Student ID is required for each record.',
            'records.*.status.required' => 'Attendance status is required for each student.',
            'records.*.status.in' => 'Status must be one of: Present, Absent, Late, Excused.',
        ];
    }
}
