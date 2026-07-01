<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'role_name' => 'required|string|max:50|unique:roles,role_name,' . $roleId,
            'description' => 'nullable|string',
        ];
    }
}
