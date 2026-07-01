<?php

namespace App\Domain\Assessment\Repositories\Contracts;

use App\Domain\Assessment\Models\SkillRatingScale;
use Illuminate\Database\Eloquent\Collection;

interface SkillRatingScaleRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?SkillRatingScale;
    public function create(array $data): SkillRatingScale;
    public function update(int $id, array $data): SkillRatingScale;
    public function delete(int $id): bool;
    public function findByCode(string $code): ?SkillRatingScale;
}
