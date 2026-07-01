<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssessmentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_name' => 'required|string|max:60',
            'category' => 'required|in:Formative,Summative',
            'weight_percentage' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ];
    }
}
