<?php

namespace App\Domain\Students\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'admission_no',
        'lin',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'photo',
        'religion',
        'address',
        'admission_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'admission_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studentGuardians(): HasMany
    {
        return $this->hasMany(StudentGuardian::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function assessmentRecords(): HasMany
    {
        return $this->hasMany(AssessmentRecord::class);
    }

    public function genericSkillRatings(): HasMany
    {
        return $this->hasMany(GenericSkillRating::class);
    }

    public function subjectTermResults(): HasMany
    {
        return $this->hasMany(SubjectTermResult::class);
    }

    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function disciplineRecords(): HasMany
    {
        return $this->hasMany(DisciplineRecord::class);
    }

    public function bookLoans(): HasMany
    {
        return $this->hasMany(BookLoan::class);
    }
}
