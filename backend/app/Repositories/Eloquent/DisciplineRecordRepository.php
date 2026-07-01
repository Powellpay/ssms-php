<?php

namespace App\Repositories\Eloquent;

use App\Models\DisciplineRecord;
use App\Repositories\Contracts\DisciplineRecordRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DisciplineRecordRepository implements DisciplineRecordRepositoryInterface
{
    public function all(): Collection
    {
        return DisciplineRecord::all();
    }

    public function find(int $id): ?DisciplineRecord
    {
        return DisciplineRecord::find($id);
    }

    public function create(array $data): DisciplineRecord
    {
        return DisciplineRecord::create($data);
    }

    public function update(int $id, array $data): DisciplineRecord
    {
        $disciplineRecord = $this->find($id);
        $disciplineRecord->update($data);
        return $disciplineRecord;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStudent(int $studentId): Collection
    {
        return DisciplineRecord::where('student_id', $studentId)->get();
    }
}
