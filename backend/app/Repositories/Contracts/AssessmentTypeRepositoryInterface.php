<?php

namespace App\Repositories\Contracts;

use App\Models\AssessmentType;
use Illuminate\Database\Eloquent\Collection;

interface AssessmentTypeRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?AssessmentType;
    public function create(array $data): AssessmentType;
    public function update(int $id, array $data): AssessmentType;
    public function delete(int $id): bool;
    public function findByCategory(string $category): Collection;
}
