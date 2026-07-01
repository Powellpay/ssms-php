<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradingScaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('grading_scale');

        return [
            'grade' => 'required|string|max:2|unique:grading_scale,grade,' . $id,
            'descriptor' => 'required|string|max:30',
            'min_score' => 'required|numeric',
            'max_score' => 'required|numeric|gt:min_score',
            'remarks' => 'nullable|string',
        ];
    }
}
