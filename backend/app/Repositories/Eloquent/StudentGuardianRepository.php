<?php

namespace App\Repositories\Eloquent;

use App\Models\StudentGuardian;
use App\Repositories\Contracts\StudentGuardianRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StudentGuardianRepository implements StudentGuardianRepositoryInterface
{
    public function all(): Collection
    {
        return StudentGuardian::all();
    }

    public function find(int $id): ?StudentGuardian
    {
        return StudentGuardian::find($id);
    }

    public function create(array $data): StudentGuardian
    {
        return StudentGuardian::create($data);
    }

    public function update(int $id, array $data): StudentGuardian
    {
        $studentGuardian = $this->find($id);
        $studentGuardian->update($data);
        return $studentGuardian;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStudent(int $studentId): Collection
    {
        return StudentGuardian::where('student_id', $studentId)->get();
    }

    public function findPrimaryContact(int $studentId): ?StudentGuardian
    {
        return StudentGuardian::where('student_id', $studentId)
            ->where('is_primary_contact', true)
            ->first();
    }
}
