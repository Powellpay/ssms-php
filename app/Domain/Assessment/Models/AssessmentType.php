<?php

namespace App\Domain\Assessment\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentType extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
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
