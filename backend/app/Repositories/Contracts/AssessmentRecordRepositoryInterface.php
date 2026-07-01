<?php

namespace App\Repositories\Contracts;

use App\Models\AssessmentRecord;
use Illuminate\Database\Eloquent\Collection;

interface AssessmentRecordRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?AssessmentRecord;
    public function create(array $data): AssessmentRecord;
    public function update(int $id, array $data): AssessmentRecord;
    public function delete(int $id): bool;
    public function findByStudent(int $studentId): Collection;
    public function findBySubject(int $subjectId): Collection;
    public function findByTerm(int $termId): Collection;
    public function findByStudentAndTerm(int $studentId, int $termId): Collection;
}
