<?php

namespace App\Domain\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeStructure extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'class_level_id',
        'term_id',
        'fee_category',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
