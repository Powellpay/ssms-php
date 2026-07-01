<?php

namespace App\Domain\Finance\Repositories\Contracts;

use App\Domain\Finance\Models\FeeStructure;
use Illuminate\Database\Eloquent\Collection;

interface FeeStructureRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?FeeStructure;
    public function create(array $data): FeeStructure;
    public function update(int $id, array $data): FeeStructure;
    public function delete(int $id): bool;
    public function findByClassLevelAndTerm(int $classLevelId, int $termId): Collection;
}
