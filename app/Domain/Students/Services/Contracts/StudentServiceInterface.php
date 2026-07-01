<?php

namespace App\Domain\Students\Services\Contracts;

use App\Domain\Students\Models\Enrollment;
use App\Domain\Students\Models\Student;
use Illuminate\Database\Eloquent\Collection;

interface StudentServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Student;

    public function create(array $data): Student;

    public function update(int $id, array $data): Student;

    public function delete(int $id): bool;

    public function findByAdmissionNo(string $no): ?Student;

    public function findByStatus(string $status): Collection;

    public function getCurrentEnrollment(int $studentId): ?Enrollment;
}
