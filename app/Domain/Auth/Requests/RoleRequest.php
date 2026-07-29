<?php

namespace App\Domain\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('role_name') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->input('role_name')),
            ]);
        }
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'role_name' => 'required|string|max:50|unique:roles,role_name,' . $roleId,
            'slug' => 'nullable|string|max:50|unique:roles,slug,' . $roleId,
            'description' => 'nullable|string',
        ];
    }
}
