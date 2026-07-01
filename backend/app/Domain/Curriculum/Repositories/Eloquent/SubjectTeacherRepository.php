<?php

namespace App\Domain\Curriculum\Repositories\Eloquent;

use App\Domain\Curriculum\Models\SubjectTeacher;
use App\Domain\Curriculum\Repositories\Contracts\SubjectTeacherRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubjectTeacherRepository implements SubjectTeacherRepositoryInterface
{
    public function all(): Collection
    {
        return SubjectTeacher::all();
    }

    public function find(int $id): ?SubjectTeacher
    {
        return SubjectTeacher::find($id);
    }

    public function create(array $data): SubjectTeacher
    {
        return SubjectTeacher::create($data);
    }

    public function update(int $id, array $data): SubjectTeacher
    {
        $subjectTeacher = $this->find($id);
        $subjectTeacher->update($data);
        return $subjectTeacher;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findBySubject(int $subjectId): Collection
    {
        return SubjectTeacher::where('subject_id', $subjectId)->get();
    }

    public function findByStream(int $streamId): Collection
    {
        return SubjectTeacher::where('stream_id', $streamId)->get();
    }

    public function findByStaff(int $staffId): Collection
    {
        return SubjectTeacher::where('staff_id', $staffId)->get();
    }

    public function findByAcademicYear(int $academicYearId): Collection
    {
        return SubjectTeacher::where('academic_year_id', $academicYearId)->get();
    }
}
