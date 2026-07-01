<?php

namespace App\Repositories\Contracts;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;

interface AttendanceRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Attendance;
    public function create(array $data): Attendance;
    public function update(int $id, array $data): Attendance;
    public function delete(int $id): bool;
    public function findByStudentAndTerm(int $studentId, int $termId): Collection;
    public function findByDate(string $date): Collection;
    public function attendanceSummary(int $studentId, int $termId): array;
}
