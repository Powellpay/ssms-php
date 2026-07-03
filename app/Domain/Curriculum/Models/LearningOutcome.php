<?php

namespace App\Domain\Curriculum\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningOutcome extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
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
