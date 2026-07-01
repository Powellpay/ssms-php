<?php

namespace App\Domain\Timetable\Services;

use App\Domain\Timetable\Models\Timetable;
use App\Domain\Timetable\Repositories\Contracts\TimetableRepositoryInterface;
use App\Domain\Timetable\Services\Contracts\TimetableServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class TimetableService implements TimetableServiceInterface
{
    public function __construct(
        protected TimetableRepositoryInterface $timetableRepository
    ) {}

    public function all(): Collection
    {
        return $this->timetableRepository->all();
    }

    public function find(int $id): ?Timetable
    {
        return $this->timetableRepository->find($id);
    }

    public function create(array $data): Timetable
    {
        return $this->timetableRepository->create($data);
    }

    public function update(int $id, array $data): Timetable
    {
        return $this->timetableRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->timetableRepository->delete($id);
    }

    public function getStreamTimetable(int $streamId): Collection
    {
        return Timetable::with('subject', 'staff')
            ->where('stream_id', $streamId)
            ->orderBy('day_of_week')
            ->orderBy('period_no')
            ->get();
    }

    public function getTeacherTimetable(int $staffId): Collection
    {
        return Timetable::with('subject', 'stream')
            ->where('staff_id', $staffId)
            ->orderBy('day_of_week')
            ->orderBy('period_no')
            ->get();
    }
}
