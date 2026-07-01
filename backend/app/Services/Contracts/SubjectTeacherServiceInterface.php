<?php

namespace App\Services\Contracts;

use App\Models\SubjectTeacher;
use Illuminate\Database\Eloquent\Collection;

interface SubjectTeacherServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?SubjectTeacher;

    public function create(array $data): SubjectTeacher;

    public function update(int $id, array $data): SubjectTeacher;

    public function delete(int $id): bool;

    public function getTeachersForSubject(int $subjectId, int $streamId): Collection;

    public function getSubjectsForTeacher(int $staffId): Collection;
}
