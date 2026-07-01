<?php

namespace App\Domain\Assessment\Repositories\Eloquent;

use App\Domain\Assessment\Models\SkillRatingScale;
use App\Domain\Assessment\Repositories\Contracts\SkillRatingScaleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SkillRatingScaleRepository implements SkillRatingScaleRepositoryInterface
{
    public function all(): Collection
    {
        return SkillRatingScale::all();
    }

    public function find(int $id): ?SkillRatingScale
    {
        return SkillRatingScale::find($id);
    }

    public function create(array $data): SkillRatingScale
    {
        return SkillRatingScale::create($data);
    }

    public function update(int $id, array $data): SkillRatingScale
    {
        $skillRatingScale = $this->find($id);
        $skillRatingScale->update($data);
        return $skillRatingScale;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByCode(string $code): ?SkillRatingScale
    {
        return SkillRatingScale::where('rating_code', $code)->first();
    }
}
