<?php

namespace App\Domain\Curriculum\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSubject extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'class_level_id',
        'subject_id',
        'is_compulsory',
    ];

    protected function casts(): array
    {
        return [
            'is_compulsory' => 'boolean',
        ];
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
