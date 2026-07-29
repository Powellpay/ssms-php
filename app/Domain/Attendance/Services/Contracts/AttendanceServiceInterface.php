<?php

namespace App\Domain\Attendance\Services\Contracts;

use App\Domain\Attendance\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;

interface AttendanceServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Attendance;

    public function create(array $data): Attendance;

    public function update(int $id, array $data): Attendance;

    public function delete(int $id): bool;

    public function markAttendance(int $studentId, int $termId, string $date, string $status): Attendance;

    public function getAttendanceSummary(int $studentId, int $termId): array;

    public function markBulkAttendance(array $records, int $termId, string $date, int $recordedBy): array;

    public function getRegister(int $termId, string $date, ?int $streamId): array;
}
