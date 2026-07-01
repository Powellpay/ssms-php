<?php

namespace App\Services;

use App\Models\DisciplineRecord;
use App\Repositories\Contracts\DisciplineRecordRepositoryInterface;
use App\Services\Contracts\DisciplineRecordServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class DisciplineRecordService implements DisciplineRecordServiceInterface
{
    public function __construct(
        protected DisciplineRecordRepositoryInterface $disciplineRecordRepository
    ) {}

    public function all(): Collection
    {
        return $this->disciplineRecordRepository->all();
    }

    public function find(int $id): ?DisciplineRecord
    {
        return $this->disciplineRecordRepository->find($id);
    }

    public function create(array $data): DisciplineRecord
    {
        return $this->disciplineRecordRepository->create($data);
    }

    public function update(int $id, array $data): DisciplineRecord
    {
        return $this->disciplineRecordRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->disciplineRecordRepository->delete($id);
    }

    public function getStudentDiscipline(int $studentId): Collection
    {
        return DisciplineRecord::where('student_id', $studentId)
            ->orderBy('incident_date', 'desc')
            ->get();
    }
}
