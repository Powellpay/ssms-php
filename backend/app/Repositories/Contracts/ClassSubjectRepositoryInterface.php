<?php

namespace App\Repositories\Contracts;

use App\Models\ClassSubject;
use Illuminate\Database\Eloquent\Collection;

interface ClassSubjectRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?ClassSubject;
    public function create(array $data): ClassSubject;
    public function update(int $id, array $data): ClassSubject;
    public function delete(int $id): bool;
    public function findByClassLevel(int $classLevelId): Collection;
    public function findBySubject(int $subjectId): Collection;
}
