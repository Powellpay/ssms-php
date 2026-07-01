<?php

namespace App\Services;

use App\Models\SubjectTermResult;
use App\Repositories\Contracts\SubjectTermResultRepositoryInterface;
use App\Services\Contracts\GradingScaleServiceInterface;
use App\Services\Contracts\SubjectTermResultServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class SubjectTermResultService implements SubjectTermResultServiceInterface
{
    public function __construct(
        protected SubjectTermResultRepositoryInterface $subjectTermResultRepository,
        protected GradingScaleServiceInterface $gradingScaleService
    ) {}

    public function all(): Collection
    {
        return $this->subjectTermResultRepository->all();
    }

    public function find(int $id): ?SubjectTermResult
    {
        return $this->subjectTermResultRepository->find($id);
    }

    public function create(array $data): SubjectTermResult
    {
        return $this->subjectTermResultRepository->create($data);
    }

    public function update(int $id, array $data): SubjectTermResult
    {
        return $this->subjectTermResultRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->subjectTermResultRepository->delete($id);
    }

    public function computeResult(int $studentId, int $subjectId, int $termId, float $caScore, float $eotScore): SubjectTermResult
    {
        $finalScore = $caScore + $eotScore;
        $grade = $this->gradingScaleService->getGrade($finalScore);

        $existing = $this->subjectTermResultRepository->findByStudentSubjectTerm($studentId, $subjectId, $termId);

        $data = [
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'term_id' => $termId,
            'ca_score' => $caScore,
            'eot_score' => $eotScore,
            'final_score' => $finalScore,
            'final_grade' => $grade?->grade,
        ];

        if ($existing) {
            return $this->subjectTermResultRepository->update($existing->id, $data);
        }

        return $this->subjectTermResultRepository->create($data);
    }
}
