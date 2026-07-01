<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingScale extends Model
{
    protected $table = 'grading_scale';

    protected $fillable = [
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
