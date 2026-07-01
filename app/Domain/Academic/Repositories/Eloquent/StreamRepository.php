<?php

namespace App\Domain\Academic\Repositories\Eloquent;

use App\Domain\Academic\Models\Stream;
use App\Domain\Academic\Repositories\Contracts\StreamRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StreamRepository implements StreamRepositoryInterface
{
    public function all(): Collection
    {
        return Stream::all();
    }

    public function find(int $id): ?Stream
    {
        return Stream::find($id);
    }

    public function create(array $data): Stream
    {
        return Stream::create($data);
    }

    public function update(int $id, array $data): Stream
    {
        $stream = $this->find($id);
        $stream->update($data);
        return $stream;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByClassLevel(int $classLevelId): Collection
    {
        return Stream::where('class_level_id', $classLevelId)->get();
    }

    public function findByAcademicYear(int $academicYearId): Collection
    {
        return Stream::where('academic_year_id', $academicYearId)->get();
    }

    public function findByClassTeacher(int $staffId): Collection
    {
        return Stream::where('class_teacher_id', $staffId)->get();
    }
}
