<?php

namespace App\Services\Contracts;

use App\Models\LearningOutcome;
use Illuminate\Database\Eloquent\Collection;

interface LearningOutcomeServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?LearningOutcome;

    public function create(array $data): LearningOutcome;

    public function update(int $id, array $data): LearningOutcome;

    public function delete(int $id): bool;

    public function getOutcomesForTheme(int $themeId): Collection;
}
