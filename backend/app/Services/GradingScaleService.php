<?php

namespace App\Services;

use App\Models\GradingScale;
use App\Repositories\Contracts\GradingScaleRepositoryInterface;
use App\Services\Contracts\GradingScaleServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class GradingScaleService implements GradingScaleServiceInterface
{
    public function __construct(
        protected GradingScaleRepositoryInterface $gradingScaleRepository
    ) {}

    public function all(): Collection
    {
        return $this->gradingScaleRepository->all();
    }

    public function find(int $id): ?GradingScale
    {
        return $this->gradingScaleRepository->find($id);
    }

    public function create(array $data): GradingScale
    {
        return $this->gradingScaleRepository->create($data);
    }

    public function update(int $id, array $data): GradingScale
    {
        return $this->gradingScaleRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->gradingScaleRepository->delete($id);
    }

    public function getGrade(float $score): ?GradingScale
    {
        return $this->gradingScaleRepository->findScoreGrade($score);
    }
}
