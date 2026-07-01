<?php

namespace App\Services;

use App\Models\Subject;
use App\Repositories\Contracts\SubjectRepositoryInterface;
use App\Services\Contracts\SubjectServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class SubjectService implements SubjectServiceInterface
{
    public function __construct(
        protected SubjectRepositoryInterface $subjectRepository
    ) {}

    public function all(): Collection
    {
        return $this->subjectRepository->all();
    }

    public function find(int $id): ?Subject
    {
        return $this->subjectRepository->find($id);
    }

    public function create(array $data): Subject
    {
        return $this->subjectRepository->create($data);
    }

    public function update(int $id, array $data): Subject
    {
        return $this->subjectRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->subjectRepository->delete($id);
    }
}
