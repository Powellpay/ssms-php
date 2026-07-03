<?php

namespace App\Domain\Library\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryBook extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
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
