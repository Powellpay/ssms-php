<?php

namespace App\Domain\Assessment\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenericSkillRatingRequest extends FormRequest
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
            'generic_skill_id' => 'required|exists:generic_skills,id',
            'rating_id' => 'required|exists:skill_rating_scale,id',
        ];
    }
}
