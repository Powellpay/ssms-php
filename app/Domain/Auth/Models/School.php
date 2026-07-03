<?php

namespace App\Domain\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $table = 'schools';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'logo',
        'motto',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
