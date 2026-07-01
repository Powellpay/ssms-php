<?php

namespace App\Domain\Timetable\Repositories\Eloquent;

use App\Domain\Timetable\Models\Timetable;
use App\Domain\Timetable\Repositories\Contracts\TimetableRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TimetableRepository implements TimetableRepositoryInterface
{
    public function all(): Collection
    {
        return Timetable::all();
    }

    public function find(int $id): ?Timetable
    {
        return Timetable::find($id);
    }

    public function create(array $data): Timetable
    {
        return Timetable::create($data);
    }

    public function update(int $id, array $data): Timetable
    {
        $timetable = $this->find($id);
        $timetable->update($data);
        return $timetable;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStream(int $streamId): Collection
    {
        return Timetable::where('stream_id', $streamId)->get();
    }

    public function findByStaff(int $staffId): Collection
    {
        return Timetable::where('staff_id', $staffId)->get();
    }

    public function findByDay(string $day): Collection
    {
        return Timetable::where('day_of_week', $day)->get();
    }
}
