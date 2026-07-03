<?php

namespace App\Domain\Assessment\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenericSkillRating extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'student_id',
        'term_id',
        'generic_skill_id',
        'rating_id',
        'remarks',
        'recorded_by',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function genericSkill(): BelongsTo
    {
        return $this->belongsTo(GenericSkill::class);
    }

    public function rating(): BelongsTo
    {
        return $this->belongsTo(SkillRatingScale::class, 'rating_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'recorded_by');
    }
}
