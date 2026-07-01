<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningOutcome extends Model
{
    protected $fillable = [
        'theme_id',
        'outcome_code',
        'description',
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(CurriculumTheme::class, 'theme_id');
    }

    public function assessmentRecords(): HasMany
    {
        return $this->hasMany(AssessmentRecord::class);
    }
}
