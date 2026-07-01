<?php

namespace App\Repositories\Eloquent;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StudentRepository implements StudentRepositoryInterface
{
    public function all(): Collection
    {
        return Student::all();
    }

    public function find(int $id): ?Student
    {
        return Student::find($id);
    }

    public function create(array $data): Student
    {
        return Student::create($data);
    }

    public function update(int $id, array $data): Student
    {
        $student = $this->find($id);
        $student->update($data);
        return $student;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByAdmissionNo(string $admissionNo): ?Student
    {
        return Student::where('admission_no', $admissionNo)->first();
    }

    public function findByStatus(string $status): Collection
    {
        return Student::where('status', $status)->get();
    }
}
