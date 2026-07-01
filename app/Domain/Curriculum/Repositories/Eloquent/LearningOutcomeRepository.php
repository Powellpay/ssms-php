<?php

namespace App\Domain\Curriculum\Repositories\Eloquent;

use App\Domain\Curriculum\Models\LearningOutcome;
use App\Domain\Curriculum\Repositories\Contracts\LearningOutcomeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LearningOutcomeRepository implements LearningOutcomeRepositoryInterface
{
    public function all(): Collection
    {
        return LearningOutcome::all();
    }

    public function find(int $id): ?LearningOutcome
    {
        return LearningOutcome::find($id);
    }

    public function create(array $data): LearningOutcome
    {
        return LearningOutcome::create($data);
    }

    public function update(int $id, array $data): LearningOutcome
    {
        $learningOutcome = $this->find($id);
        $learningOutcome->update($data);
        return $learningOutcome;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByTheme(int $themeId): Collection
    {
        return LearningOutcome::where('theme_id', $themeId)->get();
    }
}
