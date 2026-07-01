<?php

namespace App\Services;

use App\Models\StudentGuardian;
use App\Repositories\Contracts\StudentGuardianRepositoryInterface;
use App\Services\Contracts\StudentGuardianServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class StudentGuardianService implements StudentGuardianServiceInterface
{
    public function __construct(
        protected StudentGuardianRepositoryInterface $studentGuardianRepository
    ) {}

    public function all(): Collection
    {
        return $this->studentGuardianRepository->all();
    }

    public function find(int $id): ?StudentGuardian
    {
        return $this->studentGuardianRepository->find($id);
    }

    public function create(array $data): StudentGuardian
    {
        return $this->studentGuardianRepository->create($data);
    }

    public function update(int $id, array $data): StudentGuardian
    {
        return $this->studentGuardianRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->studentGuardianRepository->delete($id);
    }

    public function getStudentGuardians(int $studentId): Collection
    {
        return StudentGuardian::with('guardian')->where('student_id', $studentId)->get();
    }

    public function getPrimaryContact(int $studentId): ?StudentGuardian
    {
        return StudentGuardian::with('guardian')
            ->where('student_id', $studentId)
            ->where('is_primary_contact', true)
            ->first();
    }

    public function addGuardianToStudent(int $studentId, int $guardianId, bool $isPrimary): StudentGuardian
    {
        if ($isPrimary) {
            StudentGuardian::where('student_id', $studentId)
                ->where('is_primary_contact', true)
                ->update(['is_primary_contact' => false]);
        }

        return $this->studentGuardianRepository->create([
            'student_id' => $studentId,
            'guardian_id' => $guardianId,
            'is_primary_contact' => $isPrimary,
        ]);
    }
}
