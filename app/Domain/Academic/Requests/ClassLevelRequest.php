<?php

namespace App\Domain\Academic\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('class_level');

        return [
            'level_name' => 'required|string|max:20|unique:class_levels,level_name,' . $id,
            'numeric_level' => 'required|integer|min:1|max:6',
            'description' => 'nullable|string',
        ];
    }
}
