<?php

namespace App\Domain\Curriculum\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GenericSkill extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'skill_name',
        'description',
    ];

    public function genericSkillRatings(): HasMany
    {
        return $this->hasMany(GenericSkillRating::class);
    }
}
