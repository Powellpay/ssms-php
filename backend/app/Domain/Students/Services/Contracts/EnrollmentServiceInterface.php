<?php

namespace App\Domain\Students\Services\Contracts;

use App\Domain\Students\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;

interface EnrollmentServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Enrollment;

    public function create(array $data): Enrollment;

    public function update(int $id, array $data): Enrollment;

    public function delete(int $id): bool;

    public function promoteStudent(int $studentId, int $newStreamId, int $academicYearId, string $status): Enrollment;

    public function getCurrentEnrollment(int $studentId): ?Enrollment;
}
