<?php

namespace App\Services;

use App\Models\LearningOutcome;
use App\Repositories\Contracts\LearningOutcomeRepositoryInterface;
use App\Services\Contracts\LearningOutcomeServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class LearningOutcomeService implements LearningOutcomeServiceInterface
{
    public function __construct(
        protected LearningOutcomeRepositoryInterface $learningOutcomeRepository
    ) {}

    public function all(): Collection
    {
        return $this->learningOutcomeRepository->all();
    }

    public function find(int $id): ?LearningOutcome
    {
        return $this->learningOutcomeRepository->find($id);
    }

    public function create(array $data): LearningOutcome
    {
        return $this->learningOutcomeRepository->create($data);
    }

    public function update(int $id, array $data): LearningOutcome
    {
        return $this->learningOutcomeRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->learningOutcomeRepository->delete($id);
    }

    public function getOutcomesForTheme(int $themeId): Collection
    {
        return LearningOutcome::where('theme_id', $themeId)->get();
    }
}
