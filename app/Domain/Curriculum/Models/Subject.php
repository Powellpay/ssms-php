<?php

namespace App\Domain\Curriculum\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'subject_code',
        'subject_name',
        'category',
        'description',
    ];

    public function classSubjects(): HasMany
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function subjectTeachers(): HasMany
    {
        return $this->hasMany(SubjectTeacher::class);
    }

    public function curriculumThemes(): HasMany
    {
        return $this->hasMany(CurriculumTheme::class);
    }

    public function assessmentRecords(): HasMany
    {
        return $this->hasMany(AssessmentRecord::class);
    }

    public function subjectTermResults(): HasMany
    {
        return $this->hasMany(SubjectTermResult::class);
    }

    public function timetable(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }
}
