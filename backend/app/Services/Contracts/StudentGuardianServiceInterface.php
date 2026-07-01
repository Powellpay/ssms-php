<?php

namespace App\Services\Contracts;

use App\Models\StudentGuardian;
use Illuminate\Database\Eloquent\Collection;

interface StudentGuardianServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?StudentGuardian;

    public function create(array $data): StudentGuardian;

    public function update(int $id, array $data): StudentGuardian;

    public function delete(int $id): bool;

    public function getStudentGuardians(int $studentId): Collection;

    public function getPrimaryContact(int $studentId): ?StudentGuardian;

    public function addGuardianToStudent(int $studentId, int $guardianId, bool $isPrimary): StudentGuardian;
}
