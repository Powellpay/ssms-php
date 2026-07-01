<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GenericSkill extends Model
{
    protected $fillable = [
        'skill_name',
        'description',
    ];

    public function genericSkillRatings(): HasMany
    {
        return $this->hasMany(GenericSkillRating::class);
    }
}
