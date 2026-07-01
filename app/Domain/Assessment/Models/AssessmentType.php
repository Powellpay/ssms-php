<?php

namespace App\Domain\Assessment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentType extends Model
{
    protected $fillable = [
        'type_name',
        'category',
        'weight_percentage',
        'description',
    ];

    public function assessmentRecords(): HasMany
    {
        return $this->hasMany(AssessmentRecord::class);
    }
}
