<?php

namespace App\Domain\Assessment\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;

class GradingScale extends Model
{
    use BelongsToSchool;
    protected $table = 'grading_scale';

    protected $fillable = [
        'school_id',
        'grade',
        'descriptor',
        'min_score',
        'max_score',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'min_score' => 'decimal:2',
            'max_score' => 'decimal:2',
        ];
    }
}
