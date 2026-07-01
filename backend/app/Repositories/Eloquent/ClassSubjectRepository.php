<?php

namespace App\Repositories\Eloquent;

use App\Models\ClassSubject;
use App\Repositories\Contracts\ClassSubjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ClassSubjectRepository implements ClassSubjectRepositoryInterface
{
    public function all(): Collection
    {
        return ClassSubject::all();
    }

    public function find(int $id): ?ClassSubject
    {
        return ClassSubject::find($id);
    }

    public function create(array $data): ClassSubject
    {
        return ClassSubject::create($data);
    }

    public function update(int $id, array $data): ClassSubject
    {
        $classSubject = $this->find($id);
        $classSubject->update($data);
        return $classSubject;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByClassLevel(int $classLevelId): Collection
    {
        return ClassSubject::where('class_level_id', $classLevelId)->get();
    }

    public function findBySubject(int $subjectId): Collection
    {
        return ClassSubject::where('subject_id', $subjectId)->get();
    }
}
