<?php

namespace App\Domain\Curriculum\Services;

use App\Domain\Curriculum\Models\SubjectTeacher;
use App\Domain\Curriculum\Repositories\Contracts\SubjectTeacherRepositoryInterface;
use App\Domain\Curriculum\Services\Contracts\SubjectTeacherServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class SubjectTeacherService implements SubjectTeacherServiceInterface
{
    public function __construct(
        protected SubjectTeacherRepositoryInterface $subjectTeacherRepository
    ) {}

    public function all(): Collection
    {
        return $this->subjectTeacherRepository->all();
    }

    public function find(int $id): ?SubjectTeacher
    {
        return $this->subjectTeacherRepository->find($id);
    }

    public function create(array $data): SubjectTeacher
    {
        return $this->subjectTeacherRepository->create($data);
    }

    public function update(int $id, array $data): SubjectTeacher
    {
        return $this->subjectTeacherRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->subjectTeacherRepository->delete($id);
    }

    public function getTeachersForSubject(int $subjectId, int $streamId): Collection
    {
        return SubjectTeacher::with('staff')
            ->where('subject_id', $subjectId)
            ->where('stream_id', $streamId)
            ->get();
    }

    public function getSubjectsForTeacher(int $staffId): Collection
    {
        return SubjectTeacher::with('subject', 'stream')
            ->where('staff_id', $staffId)
            ->get();
    }
}
