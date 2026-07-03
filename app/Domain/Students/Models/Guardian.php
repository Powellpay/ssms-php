<?php

namespace App\Domain\Students\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Shared\Traits\BelongsToSchool;

class Guardian extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'first_name',
        'last_name',
        'relationship',
        'phone',
        'email',
        'occupation',
        'address',
    ];

    public function studentGuardians(): HasMany
    {
        return $this->hasMany(StudentGuardian::class);
    }
}
