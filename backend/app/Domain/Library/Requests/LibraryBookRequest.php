<?php

namespace App\Domain\Library\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LibraryBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('library_book');

        return [
            'title' => 'required|string|max:150',
            'author' => 'nullable|string',
            'isbn' => 'nullable|string|max:30|unique:library_books,isbn,' . $id,
            'total_copies' => 'nullable|integer|min:1',
            'available_copies' => 'nullable|integer|min:0',
        ];
    }
}
