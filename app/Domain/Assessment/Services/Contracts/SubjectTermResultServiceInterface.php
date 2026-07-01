<?php

namespace App\Domain\Assessment\Services\Contracts;

use App\Domain\Assessment\Models\SubjectTermResult;
use Illuminate\Database\Eloquent\Collection;

interface SubjectTermResultServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?SubjectTermResult;

    public function create(array $data): SubjectTermResult;

    public function update(int $id, array $data): SubjectTermResult;

    public function delete(int $id): bool;

    public function computeResult(int $studentId, int $subjectId, int $termId, float $caScore, float $eotScore): SubjectTermResult;
}
