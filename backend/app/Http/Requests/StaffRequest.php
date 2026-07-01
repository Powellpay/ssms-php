<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('staff');

        return [
            'staff_no' => 'required|string|max:20|unique:staff,staff_no,' . $id,
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string',
            'email' => 'nullable|email|unique:staff,email,' . $id,
            'designation' => 'nullable|string',
            'status' => 'nullable|in:active,on leave,left',
        ];
    }
}
