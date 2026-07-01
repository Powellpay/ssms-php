<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeeStructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_level_id' => 'required|exists:class_levels,id',
            'term_id' => 'required|exists:terms,id',
            'fee_category' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
        ];
    }
}
