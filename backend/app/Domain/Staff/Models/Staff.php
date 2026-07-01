<?php

namespace App\Domain\Staff\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $fillable = [
        'user_id',
        'staff_no',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'phone',
        'email',
        'address',
        'designation',
        'date_joined',
        'photo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'date_joined' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class, 'class_teacher_id');
    }

    public function subjectTeachers(): HasMany
    {
        return $this->hasMany(SubjectTeacher::class);
    }

    public function assessmentRecords(): HasMany
    {
        return $this->hasMany(AssessmentRecord::class, 'recorded_by');
    }

    public function genericSkillRatings(): HasMany
    {
        return $this->hasMany(GenericSkillRating::class, 'recorded_by');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'recorded_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'received_by');
    }

    public function disciplineRecords(): HasMany
    {
        return $this->hasMany(DisciplineRecord::class, 'recorded_by');
    }
}
