<?php

namespace App\Domain\Reports\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'term_id' => 'required|exists:terms,id',
            'stream_id' => 'required|exists:streams,id',
            'days_present' => 'integer',
            'days_absent' => 'integer',
            'class_teacher_comment' => 'nullable|string',
            'head_teacher_comment' => 'nullable|string',
        ];
    }
}
