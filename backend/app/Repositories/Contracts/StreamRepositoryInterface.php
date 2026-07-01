<?php

namespace App\Repositories\Contracts;

use App\Models\Stream;
use Illuminate\Database\Eloquent\Collection;

interface StreamRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Stream;
    public function create(array $data): Stream;
    public function update(int $id, array $data): Stream;
    public function delete(int $id): bool;
    public function findByClassLevel(int $classLevelId): Collection;
    public function findByAcademicYear(int $academicYearId): Collection;
    public function findByClassTeacher(int $staffId): Collection;
}
