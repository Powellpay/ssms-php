<?php

namespace App\Services\Contracts;

use App\Models\Timetable;
use Illuminate\Database\Eloquent\Collection;

interface TimetableServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Timetable;

    public function create(array $data): Timetable;

    public function update(int $id, array $data): Timetable;

    public function delete(int $id): bool;

    public function getStreamTimetable(int $streamId): Collection;

    public function getTeacherTimetable(int $staffId): Collection;
}
