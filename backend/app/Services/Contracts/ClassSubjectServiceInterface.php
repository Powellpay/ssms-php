<?php

namespace App\Services\Contracts;

use App\Models\ClassSubject;
use Illuminate\Database\Eloquent\Collection;

interface ClassSubjectServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?ClassSubject;

    public function create(array $data): ClassSubject;

    public function update(int $id, array $data): ClassSubject;

    public function delete(int $id): bool;

    public function getSubjectsForClass(int $classLevelId): Collection;
}
