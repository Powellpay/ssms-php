<?php

namespace App\Domain\Assessment\Services;

use App\Domain\Assessment\Models\AssessmentRecord;
use App\Domain\Assessment\Repositories\Contracts\AssessmentRecordRepositoryInterface;
use App\Domain\Assessment\Services\Contracts\AssessmentRecordServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class AssessmentRecordService implements AssessmentRecordServiceInterface
{
    public function __construct(
        protected AssessmentRecordRepositoryInterface $assessmentRecordRepository
    ) {}

    public function all(): Collection
    {
        return $this->assessmentRecordRepository->all();
    }

    public function find(int $id): ?AssessmentRecord
    {
        return $this->assessmentRecordRepository->find($id);
    }

    public function create(array $data): AssessmentRecord
    {
        return $this->assessmentRecordRepository->create($data);
    }

    public function update(int $id, array $data): AssessmentRecord
    {
        return $this->assessmentRecordRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->assessmentRecordRepository->delete($id);
    }

    public function getStudentRecords(int $studentId, int $termId): Collection
    {
        return AssessmentRecord::with('subject', 'theme', 'learningOutcome', 'assessmentType')
            ->where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();
    }
}
