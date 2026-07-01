<?php

namespace App\Domain\Curriculum\Services;

use App\Domain\Curriculum\Models\ClassSubject;
use App\Domain\Curriculum\Repositories\Contracts\ClassSubjectRepositoryInterface;
use App\Domain\Curriculum\Services\Contracts\ClassSubjectServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ClassSubjectService implements ClassSubjectServiceInterface
{
    public function __construct(
        protected ClassSubjectRepositoryInterface $classSubjectRepository
    ) {}

    public function all(): Collection
    {
        return $this->classSubjectRepository->all();
    }

    public function find(int $id): ?ClassSubject
    {
        return $this->classSubjectRepository->find($id);
    }

    public function create(array $data): ClassSubject
    {
        return $this->classSubjectRepository->create($data);
    }

    public function update(int $id, array $data): ClassSubject
    {
        return $this->classSubjectRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->classSubjectRepository->delete($id);
    }

    public function getSubjectsForClass(int $classLevelId): Collection
    {
        return ClassSubject::with('subject')
            ->where('class_level_id', $classLevelId)
            ->get();
    }
}
