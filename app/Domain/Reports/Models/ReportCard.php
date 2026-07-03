<?php

namespace App\Domain\Reports\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCard extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'student_id',
        'term_id',
        'stream_id',
        'days_present',
        'days_absent',
        'class_teacher_comment',
        'head_teacher_comment',
        'next_term_begins',
        'date_issued',
    ];

    protected function casts(): array
    {
        return [
            'next_term_begins' => 'date',
            'date_issued' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }
}
