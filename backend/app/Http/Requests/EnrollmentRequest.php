<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $enrollmentId = $this->route('enrollment');

        return [
            'student_id' => [
                'required',
                'exists:students,id',
                Rule::unique('enrollments')->where(fn($q) => $q->where('academic_year_id', $this->academic_year_id))->ignore($enrollmentId),
            ],
            'stream_id' => 'required|exists:streams,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_date' => 'required|date',
            'status' => 'nullable|in:active,promoted,repeated,transferred,left',
        ];
    }
}
