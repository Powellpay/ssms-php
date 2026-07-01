<?php

namespace App\Domain\Students\Repositories\Contracts;

use App\Domain\Students\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;

interface EnrollmentRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Enrollment;
    public function create(array $data): Enrollment;
    public function update(int $id, array $data): Enrollment;
    public function delete(int $id): bool;
    public function findByStudent(int $studentId): Collection;
    public function findByStream(int $streamId): Collection;
    public function findByAcademicYear(int $academicYearId): Collection;
    public function findActiveByStudent(int $studentId): ?Enrollment;
}
