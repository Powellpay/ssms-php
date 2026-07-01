<?php

namespace App\Repositories\Contracts;

use App\Models\SubjectTeacher;
use Illuminate\Database\Eloquent\Collection;

interface SubjectTeacherRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?SubjectTeacher;
    public function create(array $data): SubjectTeacher;
    public function update(int $id, array $data): SubjectTeacher;
    public function delete(int $id): bool;
    public function findBySubject(int $subjectId): Collection;
    public function findByStream(int $streamId): Collection;
    public function findByStaff(int $staffId): Collection;
    public function findByAcademicYear(int $academicYearId): Collection;
}
