<?php

namespace App\Domain\Assessment\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkillRatingScaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('skill_rating_scale');

        return [
            'rating_code' => 'required|string|max:5|unique:skill_rating_scale,rating_code,' . $id,
            'rating_label' => 'required|string|max:30',
            'rating_value' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
        ];
    }
}
