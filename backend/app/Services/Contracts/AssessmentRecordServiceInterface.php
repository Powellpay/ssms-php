<?php

namespace App\Services\Contracts;

use App\Models\AssessmentRecord;
use Illuminate\Database\Eloquent\Collection;

interface AssessmentRecordServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?AssessmentRecord;

    public function create(array $data): AssessmentRecord;

    public function update(int $id, array $data): AssessmentRecord;

    public function delete(int $id): bool;

    public function getStudentRecords(int $studentId, int $termId): Collection;
}
