<?php

namespace App\Domain\Assessment\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentRecord extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'student_id',
        'subject_id',
        'theme_id',
        'learning_outcome_id',
        'assessment_type_id',
        'term_id',
        'score',
        'max_score',
        'grade',
        'remarks',
        'date_recorded',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'date_recorded' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(CurriculumTheme::class, 'theme_id');
    }

    public function learningOutcome(): BelongsTo
    {
        return $this->belongsTo(LearningOutcome::class);
    }

    public function assessmentType(): BelongsTo
    {
        return $this->belongsTo(AssessmentType::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'recorded_by');
    }
}
