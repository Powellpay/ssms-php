<?php

namespace App\Domain\Students\Repositories\Contracts;

use App\Domain\Students\Models\StudentGuardian;
use Illuminate\Database\Eloquent\Collection;

interface StudentGuardianRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?StudentGuardian;
    public function create(array $data): StudentGuardian;
    public function update(int $id, array $data): StudentGuardian;
    public function delete(int $id): bool;
    public function findByStudent(int $studentId): Collection;
    public function findPrimaryContact(int $studentId): ?StudentGuardian;
}
