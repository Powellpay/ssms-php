<?php

namespace App\Domain\Finance\Services\Contracts;

use App\Domain\Finance\Models\FeeStructure;
use Illuminate\Database\Eloquent\Collection;

interface FeeStructureServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?FeeStructure;

    public function create(array $data): FeeStructure;

    public function update(int $id, array $data): FeeStructure;

    public function delete(int $id): bool;

    public function getFeesForClass(int $classLevelId, int $termId): Collection;
}
