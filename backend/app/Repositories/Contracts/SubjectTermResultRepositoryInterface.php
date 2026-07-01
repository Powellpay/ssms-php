<?php

namespace App\Repositories\Contracts;

use App\Models\SubjectTermResult;
use Illuminate\Database\Eloquent\Collection;

interface SubjectTermResultRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?SubjectTermResult;
    public function create(array $data): SubjectTermResult;
    public function update(int $id, array $data): SubjectTermResult;
    public function delete(int $id): bool;
    public function findByStudent(int $studentId): Collection;
    public function findByStudentAndTerm(int $studentId, int $termId): Collection;
    public function findByStudentSubjectTerm(int $studentId, int $subjectId, int $termId): ?SubjectTermResult;
}
