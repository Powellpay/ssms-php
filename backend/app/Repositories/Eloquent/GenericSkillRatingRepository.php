<?php

namespace App\Repositories\Eloquent;

use App\Models\GenericSkillRating;
use App\Repositories\Contracts\GenericSkillRatingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GenericSkillRatingRepository implements GenericSkillRatingRepositoryInterface
{
    public function all(): Collection
    {
        return GenericSkillRating::all();
    }

    public function find(int $id): ?GenericSkillRating
    {
        return GenericSkillRating::find($id);
    }

    public function create(array $data): GenericSkillRating
    {
        return GenericSkillRating::create($data);
    }

    public function update(int $id, array $data): GenericSkillRating
    {
        $genericSkillRating = $this->find($id);
        $genericSkillRating->update($data);
        return $genericSkillRating;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStudentAndTerm(int $studentId, int $termId): Collection
    {
        return GenericSkillRating::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();
    }

    public function upsert(array $data): GenericSkillRating
    {
        return GenericSkillRating::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'term_id' => $data['term_id'],
                'generic_skill_id' => $data['generic_skill_id'],
            ],
            $data
        );
    }
}
