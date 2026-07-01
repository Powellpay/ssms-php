<?php

namespace App\Domain\Curriculum\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LearningOutcomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'theme_id' => 'required|exists:curriculum_themes,id',
            'outcome_code' => 'nullable|string|max:20',
            'description' => 'required|string',
        ];
    }
}
