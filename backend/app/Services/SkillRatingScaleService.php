<?php

namespace App\Services;

use App\Models\SkillRatingScale;
use App\Repositories\Contracts\SkillRatingScaleRepositoryInterface;
use App\Services\Contracts\SkillRatingScaleServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class SkillRatingScaleService implements SkillRatingScaleServiceInterface
{
    public function __construct(
        protected SkillRatingScaleRepositoryInterface $skillRatingScaleRepository
    ) {}

    public function all(): Collection
    {
        return $this->skillRatingScaleRepository->all();
    }

    public function find(int $id): ?SkillRatingScale
    {
        return $this->skillRatingScaleRepository->find($id);
    }

    public function create(array $data): SkillRatingScale
    {
        return $this->skillRatingScaleRepository->create($data);
    }

    public function update(int $id, array $data): SkillRatingScale
    {
        return $this->skillRatingScaleRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->skillRatingScaleRepository->delete($id);
    }
}
