<?php

namespace App\Domain\Curriculum\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurriculumThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'class_level_id' => 'required|exists:class_levels,id',
            'theme_code' => 'nullable|string|max:20',
            'theme_name' => 'required|string|max:150',
            'description' => 'nullable|string',
        ];
    }
}
