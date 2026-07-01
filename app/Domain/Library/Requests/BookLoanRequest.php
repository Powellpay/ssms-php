<?php

namespace App\Domain\Library\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:library_books,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after:borrow_date',
        ];
    }
}
