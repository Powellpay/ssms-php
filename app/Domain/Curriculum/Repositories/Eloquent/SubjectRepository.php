<?php

namespace App\Domain\Curriculum\Repositories\Eloquent;

use App\Domain\Curriculum\Models\Subject;
use App\Domain\Curriculum\Repositories\Contracts\SubjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubjectRepository implements SubjectRepositoryInterface
{
    public function all(): Collection
    {
        return Subject::all();
    }

    public function find(int $id): ?Subject
    {
        return Subject::find($id);
    }

    public function create(array $data): Subject
    {
        return Subject::create($data);
    }

    public function update(int $id, array $data): Subject
    {
        $subject = $this->find($id);
        $subject->update($data);
        return $subject;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByCode(string $code): ?Subject
    {
        return Subject::where('subject_code', $code)->first();
    }

    public function findByCategory(string $category): Collection
    {
        return Subject::where('category', $category)->get();
    }
}
