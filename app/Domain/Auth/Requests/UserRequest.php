<?php

namespace App\Domain\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'school_id' => 'nullable|exists:schools,id',
            'role_id' => 'nullable|exists:roles,id',
            'school_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:50|unique:users,username,' . $userId,
            'name' => 'nullable|string',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => $userId ? 'nullable|string|min:8' : 'required|string|min:8',
            'phone' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
