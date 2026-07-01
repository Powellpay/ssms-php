<?php

namespace App\Domain\Curriculum\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenericSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('generic_skill');

        return [
            'skill_name' => 'required|string|max:60|unique:generic_skills,skill_name,' . $id,
            'description' => 'nullable|string',
        ];
    }
}
