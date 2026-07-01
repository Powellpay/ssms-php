<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassLevel extends Model
{
    protected $fillable = [
        'level_name',
        'numeric_level',
        'description',
    ];

    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class);
    }

    public function classSubjects(): HasMany
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function curriculumThemes(): HasMany
    {
        return $this->hasMany(CurriculumTheme::class);
    }

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }
}
