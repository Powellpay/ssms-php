<?php

namespace App\Domain\Finance\Services;

use App\Domain\Finance\Models\FeeStructure;
use App\Domain\Finance\Repositories\Contracts\FeeStructureRepositoryInterface;
use App\Domain\Finance\Services\Contracts\FeeStructureServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class FeeStructureService implements FeeStructureServiceInterface
{
    public function __construct(
        protected FeeStructureRepositoryInterface $feeStructureRepository
    ) {}

    public function all(): Collection
    {
        return $this->feeStructureRepository->all();
    }

    public function find(int $id): ?FeeStructure
    {
        return $this->feeStructureRepository->find($id);
    }

    public function create(array $data): FeeStructure
    {
        return $this->feeStructureRepository->create($data);
    }

    public function update(int $id, array $data): FeeStructure
    {
        return $this->feeStructureRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->feeStructureRepository->delete($id);
    }

    public function getFeesForClass(int $classLevelId, int $termId): Collection
    {
        return FeeStructure::where('class_level_id', $classLevelId)
            ->where('term_id', $termId)
            ->get();
    }
}
