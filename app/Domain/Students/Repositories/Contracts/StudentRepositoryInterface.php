<?php

namespace App\Domain\Students\Repositories\Contracts;

use App\Domain\Students\Models\Student;
use Illuminate\Database\Eloquent\Collection;

interface StudentRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Student;
    public function create(array $data): Student;
    public function update(int $id, array $data): Student;
    public function delete(int $id): bool;
    public function findByAdmissionNo(string $admissionNo): ?Student;
    public function findByStatus(string $status): Collection;
}
