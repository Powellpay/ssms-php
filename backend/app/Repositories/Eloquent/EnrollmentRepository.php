<?php

namespace App\Repositories\Eloquent;

use App\Models\Enrollment;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentRepository implements EnrollmentRepositoryInterface
{
    public function all(): Collection
    {
        return Enrollment::all();
    }

    public function find(int $id): ?Enrollment
    {
        return Enrollment::find($id);
    }

    public function create(array $data): Enrollment
    {
        return Enrollment::create($data);
    }

    public function update(int $id, array $data): Enrollment
    {
        $enrollment = $this->find($id);
        $enrollment->update($data);
        return $enrollment;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStudent(int $studentId): Collection
    {
        return Enrollment::where('student_id', $studentId)->get();
    }

    public function findByStream(int $streamId): Collection
    {
        return Enrollment::where('stream_id', $streamId)->get();
    }

    public function findByAcademicYear(int $academicYearId): Collection
    {
        return Enrollment::where('academic_year_id', $academicYearId)->get();
    }

    public function findActiveByStudent(int $studentId): ?Enrollment
    {
        return Enrollment::where('student_id', $studentId)
            ->where('status', 'active')
            ->first();
    }
}
