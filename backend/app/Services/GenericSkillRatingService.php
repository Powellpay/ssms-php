<?php

namespace App\Services;

use App\Models\GenericSkillRating;
use App\Repositories\Contracts\GenericSkillRatingRepositoryInterface;
use App\Services\Contracts\GenericSkillRatingServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class GenericSkillRatingService implements GenericSkillRatingServiceInterface
{
    public function __construct(
        protected GenericSkillRatingRepositoryInterface $genericSkillRatingRepository
    ) {}

    public function all(): Collection
    {
        return $this->genericSkillRatingRepository->all();
    }

    public function find(int $id): ?GenericSkillRating
    {
        return $this->genericSkillRatingRepository->find($id);
    }

    public function create(array $data): GenericSkillRating
    {
        return $this->genericSkillRatingRepository->create($data);
    }

    public function update(int $id, array $data): GenericSkillRating
    {
        return $this->genericSkillRatingRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->genericSkillRatingRepository->delete($id);
    }

    public function getStudentRatings(int $studentId, int $termId): Collection
    {
        return GenericSkillRating::with('genericSkill', 'rating')
            ->where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();
    }

    public function upsertRating(array $data): GenericSkillRating
    {
        $existing = GenericSkillRating::where('student_id', $data['student_id'])
            ->where('term_id', $data['term_id'])
            ->where('generic_skill_id', $data['generic_skill_id'])
            ->first();

        if ($existing) {
            $existing->update($data);
            return $existing;
        }

        return $this->genericSkillRatingRepository->create($data);
    }
}
