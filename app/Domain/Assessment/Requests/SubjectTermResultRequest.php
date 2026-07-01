<?php

namespace App\Domain\Assessment\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectTermResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'ca_score' => 'nullable|numeric|min:0|max:20',
            'eot_score' => 'nullable|numeric|min:0|max:80',
        ];
    }
}
