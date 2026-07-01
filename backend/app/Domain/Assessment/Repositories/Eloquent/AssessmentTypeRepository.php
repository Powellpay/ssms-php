<?php

namespace App\Domain\Assessment\Repositories\Eloquent;

use App\Domain\Assessment\Models\AssessmentType;
use App\Domain\Assessment\Repositories\Contracts\AssessmentTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AssessmentTypeRepository implements AssessmentTypeRepositoryInterface
{
    public function all(): Collection
    {
        return AssessmentType::all();
    }

    public function find(int $id): ?AssessmentType
    {
        return AssessmentType::find($id);
    }

    public function create(array $data): AssessmentType
    {
        return AssessmentType::create($data);
    }

    public function update(int $id, array $data): AssessmentType
    {
        $assessmentType = $this->find($id);
        $assessmentType->update($data);
        return $assessmentType;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByCategory(string $category): Collection
    {
        return AssessmentType::where('category', $category)->get();
    }
}
