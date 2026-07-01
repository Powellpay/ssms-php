<?php

namespace App\Services;

use App\Models\AssessmentType;
use App\Repositories\Contracts\AssessmentTypeRepositoryInterface;
use App\Services\Contracts\AssessmentTypeServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class AssessmentTypeService implements AssessmentTypeServiceInterface
{
    public function __construct(
        protected AssessmentTypeRepositoryInterface $assessmentTypeRepository
    ) {}

    public function all(): Collection
    {
        return $this->assessmentTypeRepository->all();
    }

    public function find(int $id): ?AssessmentType
    {
        return $this->assessmentTypeRepository->find($id);
    }

    public function create(array $data): AssessmentType
    {
        return $this->assessmentTypeRepository->create($data);
    }

    public function update(int $id, array $data): AssessmentType
    {
        return $this->assessmentTypeRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->assessmentTypeRepository->delete($id);
    }

    public function getByCategory(string $category): Collection
    {
        return AssessmentType::where('category', $category)->get();
    }
}
