<?php

namespace App\Domain\Assessment\Services\Contracts;

use App\Domain\Assessment\Models\AssessmentType;
use Illuminate\Database\Eloquent\Collection;

interface AssessmentTypeServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?AssessmentType;

    public function create(array $data): AssessmentType;

    public function update(int $id, array $data): AssessmentType;

    public function delete(int $id): bool;

    public function getByCategory(string $category): Collection;
}
