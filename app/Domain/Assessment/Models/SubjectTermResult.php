<?php

namespace App\Domain\Assessment\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectTermResult extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'student_id',
        'subject_id',
        'term_id',
        'ca_score',
        'eot_score',
        'final_score',
        'final_grade',
        'subject_teacher_comment',
    ];

    protected function casts(): array
    {
        return [
            'ca_score' => 'decimal:2',
            'eot_score' => 'decimal:2',
            'final_score' => 'decimal:2',
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

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
