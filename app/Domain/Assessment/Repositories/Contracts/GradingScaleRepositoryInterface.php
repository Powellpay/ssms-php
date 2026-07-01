<?php

namespace App\Domain\Assessment\Repositories\Contracts;

use App\Domain\Assessment\Models\GradingScale;
use Illuminate\Database\Eloquent\Collection;

interface GradingScaleRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?GradingScale;
    public function create(array $data): GradingScale;
    public function update(int $id, array $data): GradingScale;
    public function delete(int $id): bool;
    public function findByGrade(string $grade): ?GradingScale;
    public function findScoreGrade(float $score): ?GradingScale;
}
