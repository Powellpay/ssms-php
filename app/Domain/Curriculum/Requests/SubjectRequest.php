<?php

namespace App\Domain\Curriculum\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('subject');

        return [
            'subject_code' => 'required|string|max:10|unique:subjects,subject_code,' . $id,
            'subject_name' => 'required|string|max:60',
            'category' => 'required|in:Core,Elective,Pre-Vocational',
            'description' => 'nullable|string',
        ];
    }
}
