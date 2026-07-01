<?php

namespace App\Repositories\Contracts;

use App\Models\Timetable;
use Illuminate\Database\Eloquent\Collection;

interface TimetableRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Timetable;
    public function create(array $data): Timetable;
    public function update(int $id, array $data): Timetable;
    public function delete(int $id): bool;
    public function findByStream(int $streamId): Collection;
    public function findByStaff(int $staffId): Collection;
    public function findByDay(string $day): Collection;
}
