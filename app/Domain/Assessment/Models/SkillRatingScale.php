<?php

namespace App\Domain\Assessment\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillRatingScale extends Model
{
    use BelongsToSchool;
    protected $table = 'skill_rating_scale';

    protected $fillable = [
        'school_id',
        'rating_code',
        'rating_label',
        'rating_value',
        'description',
    ];

    public function genericSkillRatings(): HasMany
    {
        return $this->hasMany(GenericSkillRating::class, 'rating_id');
    }
}
