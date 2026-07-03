<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use BelongsToSchool;
    protected $table = 'attendance';
    protected $fillable = [
        'school_id',
        'student_id',
        'term_id',
        'attendance_date',
        'status',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
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

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'recorded_by');
    }
}
