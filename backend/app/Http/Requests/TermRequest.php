<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('term');

        return [
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_name' => 'required|string|max:10|unique:terms,term_name,' . $id . ',id,academic_year_id,' . $this->academic_year_id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ];
    }
}
