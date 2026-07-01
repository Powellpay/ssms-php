<?php

namespace App\Domain\Reports\Repositories\Contracts;

use App\Domain\Reports\Models\ReportCard;
use Illuminate\Database\Eloquent\Collection;

interface ReportCardRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?ReportCard;
    public function create(array $data): ReportCard;
    public function update(int $id, array $data): ReportCard;
    public function delete(int $id): bool;
    public function findByStudentAndTerm(int $studentId, int $termId): ?ReportCard;
}
