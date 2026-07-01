<?php

namespace App\Repositories\Eloquent;

use App\Models\FeeStructure;
use App\Repositories\Contracts\FeeStructureRepositoryInterface;
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
