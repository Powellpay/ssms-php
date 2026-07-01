<?php

namespace App\Domain\Curriculum\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('class_subject');

        return [
            'class_level_id' => 'required|exists:class_levels,id',
            'subject_id' => 'required|exists:subjects,id',
            'is_compulsory' => 'boolean',
        ];
    }
}
