<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use App\Services\Contracts\EnrollmentServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentService implements EnrollmentServiceInterface
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository
    ) {}

    public function all(): Collection
    {
        return $this->enrollmentRepository->all();
    }

    public function find(int $id): ?Enrollment
    {
        return $this->enrollmentRepository->find($id);
    }

    public function create(array $data): Enrollment
    {
        return $this->enrollmentRepository->create($data);
    }

    public function update(int $id, array $data): Enrollment
    {
        return $this->enrollmentRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->enrollmentRepository->delete($id);
    }

    public function promoteStudent(int $studentId, int $newStreamId, int $academicYearId, string $status): Enrollment
    {
        return $this->enrollmentRepository->create([
            'student_id' => $studentId,
            'stream_id' => $newStreamId,
            'academic_year_id' => $academicYearId,
            'enrollment_date' => now(),
            'status' => $status,
        ]);
    }

    public function getCurrentEnrollment(int $studentId): ?Enrollment
    {
        return Enrollment::with('stream.classLevel', 'academicYear')
            ->where('student_id', $studentId)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_current', true);
            })
            ->first();
    }
}
