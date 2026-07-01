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
            'role_id' => 'required|exists:roles,id',
            'username' => 'required|string|max:50|unique:users,username,' . $userId,
            'name' => 'nullable|string',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => $userId ? 'nullable|string|min:8' : 'required|string|min:8',
            'phone' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
