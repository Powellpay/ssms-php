<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillRatingScale extends Model
{
    protected $table = 'skill_rating_scale';

    protected $fillable = [
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
