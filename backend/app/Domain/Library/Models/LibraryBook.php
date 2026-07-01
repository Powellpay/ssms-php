<?php

namespace App\Domain\Library\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryBook extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'category',
        'total_copies',
        'available_copies',
    ];

    public function bookLoans(): HasMany
    {
        return $this->hasMany(BookLoan::class, 'book_id');
    }
}
