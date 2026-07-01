<?php

namespace App\Domain\Reports\Services\Contracts;

use App\Domain\Reports\Models\ReportCard;
use Illuminate\Database\Eloquent\Collection;

interface ReportCardServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?ReportCard;

    public function create(array $data): ReportCard;

    public function update(int $id, array $data): ReportCard;

    public function delete(int $id): bool;

    public function generateReportCard(int $studentId, int $termId): array;
}
