<?php

namespace App\Repositories\Eloquent;

use App\Models\AssessmentRecord;
use App\Repositories\Contracts\AssessmentRecordRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AssessmentRecordRepository implements AssessmentRecordRepositoryInterface
{
    public function all(): Collection
    {
        return AssessmentRecord::all();
    }

    public function find(int $id): ?AssessmentRecord
    {
        return AssessmentRecord::find($id);
    }

    public function create(array $data): AssessmentRecord
    {
        return AssessmentRecord::create($data);
    }

    public function update(int $id, array $data): AssessmentRecord
    {
        $assessmentRecord = $this->find($id);
        $assessmentRecord->update($data);
        return $assessmentRecord;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStudent(int $studentId): Collection
    {
        return AssessmentRecord::where('student_id', $studentId)->get();
    }

    public function findBySubject(int $subjectId): Collection
    {
        return AssessmentRecord::where('subject_id', $subjectId)->get();
    }

    public function findByTerm(int $termId): Collection
    {
        return AssessmentRecord::where('term_id', $termId)->get();
    }

    public function findByStudentAndTerm(int $studentId, int $termId): Collection
    {
        return AssessmentRecord::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();
    }
}
