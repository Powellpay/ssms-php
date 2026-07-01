<?php

namespace App\Domain\Students\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('student');

        return [
            'admission_no' => 'required|string|max:20|unique:students,admission_no,' . $id,
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female',
            'dob' => 'nullable|date',
            'admission_date' => 'required|date',
            'status' => 'nullable|in:active,transferred,graduated,dropped',
        ];
    }
}
