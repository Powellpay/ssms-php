<?php

namespace App\Domain\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'role_name',
        'description',
        'slug',
        'school_id',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public static function findBySlug(string $slug, ?int $schoolId = null): ?self
    {
        return static::where('slug', $slug)
            ->where(fn($q) => $q->where('school_id', $schoolId)->orWhereNull('school_id'))
            ->orderBy('school_id', 'desc')
            ->first();
    }
}
