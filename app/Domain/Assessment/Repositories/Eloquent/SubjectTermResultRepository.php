<?php

namespace App\Domain\Assessment\Repositories\Eloquent;

use App\Domain\Assessment\Models\SubjectTermResult;
use App\Domain\Assessment\Repositories\Contracts\SubjectTermResultRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubjectTermResultRepository implements SubjectTermResultRepositoryInterface
{
    public function all(): Collection
    {
        return SubjectTermResult::all();
    }

    public function find(int $id): ?SubjectTermResult
    {
        return SubjectTermResult::find($id);
    }

    public function create(array $data): SubjectTermResult
    {
        return SubjectTermResult::create($data);
    }

    public function update(int $id, array $data): SubjectTermResult
    {
        $subjectTermResult = $this->find($id);
        $subjectTermResult->update($data);
        return $subjectTermResult;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStudent(int $studentId): Collection
    {
        return SubjectTermResult::where('student_id', $studentId)->get();
    }

    public function findByStudentAndTerm(int $studentId, int $termId): Collection
    {
        return SubjectTermResult::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();
    }

    public function findByStudentSubjectTerm(int $studentId, int $subjectId, int $termId): ?SubjectTermResult
    {
        return SubjectTermResult::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->first();
    }
}
