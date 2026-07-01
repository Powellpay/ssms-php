<?php

namespace App\Domain\Announcements\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:150',
            'message' => 'required|string',
            'target_role' => 'nullable|string|max:50',
        ];
    }
}
