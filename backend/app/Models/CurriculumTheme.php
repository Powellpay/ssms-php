<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumTheme extends Model
{
    protected $fillable = [
        'subject_id',
        'class_level_id',
        'theme_code',
        'theme_name',
        'description',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function learningOutcomes(): HasMany
    {
        return $this->hasMany(LearningOutcome::class, 'theme_id');
    }

    public function assessmentRecords(): HasMany
    {
        return $this->hasMany(AssessmentRecord::class, 'theme_id');
    }
}
