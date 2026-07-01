<?php

namespace App\Services\Contracts;

use App\Models\GradingScale;
use Illuminate\Database\Eloquent\Collection;

interface GradingScaleServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?GradingScale;

    public function create(array $data): GradingScale;

    public function update(int $id, array $data): GradingScale;

    public function delete(int $id): bool;

    public function getGrade(float $score): ?GradingScale;
}
