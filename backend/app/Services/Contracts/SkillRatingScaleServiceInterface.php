<?php

namespace App\Services\Contracts;

use App\Models\SkillRatingScale;
use Illuminate\Database\Eloquent\Collection;

interface SkillRatingScaleServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?SkillRatingScale;

    public function create(array $data): SkillRatingScale;

    public function update(int $id, array $data): SkillRatingScale;

    public function delete(int $id): bool;
}
