<?php

namespace App\Services\Contracts;

use App\Models\GenericSkillRating;
use Illuminate\Database\Eloquent\Collection;

interface GenericSkillRatingServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?GenericSkillRating;

    public function create(array $data): GenericSkillRating;

    public function update(int $id, array $data): GenericSkillRating;

    public function delete(int $id): bool;

    public function getStudentRatings(int $studentId, int $termId): Collection;

    public function upsertRating(array $data): GenericSkillRating;
}
