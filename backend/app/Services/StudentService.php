<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Services\Contracts\StudentServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class StudentService implements StudentServiceInterface
{
    public function __construct(
        protected StudentRepositoryInterface $studentRepository
    ) {}

    public function all(): Collection
    {
        return $this->studentRepository->all();
    }

    public function find(int $id): ?Student
    {
        return $this->studentRepository->find($id);
    }

    public function create(array $data): Student
    {
        return $this->studentRepository->create($data);
    }

    public function update(int $id, array $data): Student
    {
        return $this->studentRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->studentRepository->delete($id);
    }

    public function findByAdmissionNo(string $no): ?Student
    {
        return Student::where('admission_no', $no)->first();
    }

    public function findByStatus(string $status): Collection
    {
        return Student::where('status', $status)->get();
    }

    public function getCurrentEnrollment(int $studentId): ?Enrollment
    {
        return Enrollment::where('student_id', $studentId)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_current', true);
            })
            ->first();
    }
}
