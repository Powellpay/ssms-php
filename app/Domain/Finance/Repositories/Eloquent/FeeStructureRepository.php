<?php

namespace App\Domain\Finance\Repositories\Eloquent;

use App\Domain\Finance\Models\FeeStructure;
use App\Domain\Finance\Repositories\Contracts\FeeStructureRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FeeStructureRepository implements FeeStructureRepositoryInterface
{
    public function all(): Collection
    {
        return FeeStructure::all();
    }

    public function find(int $id): ?FeeStructure
    {
        return FeeStructure::find($id);
    }

    public function create(array $data): FeeStructure
    {
        return FeeStructure::create($data);
    }

    public function update(int $id, array $data): FeeStructure
    {
        $feeStructure = $this->find($id);
        $feeStructure->update($data);
        return $feeStructure;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByClassLevelAndTerm(int $classLevelId, int $termId): Collection
    {
        return FeeStructure::where('class_level_id', $classLevelId)
            ->where('term_id', $termId)
            ->get();
    }
}
