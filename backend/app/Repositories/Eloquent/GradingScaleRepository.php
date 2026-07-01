<?php

namespace App\Repositories\Eloquent;

use App\Models\GradingScale;
use App\Repositories\Contracts\GradingScaleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GradingScaleRepository implements GradingScaleRepositoryInterface
{
    public function all(): Collection
    {
        return GradingScale::all();
    }

    public function find(int $id): ?GradingScale
    {
        return GradingScale::find($id);
    }

    public function create(array $data): GradingScale
    {
        return GradingScale::create($data);
    }

    public function update(int $id, array $data): GradingScale
    {
        $gradingScale = $this->find($id);
        $gradingScale->update($data);
        return $gradingScale;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByGrade(string $grade): ?GradingScale
    {
        return GradingScale::where('grade', $grade)->first();
    }

    public function findScoreGrade(float $score): ?GradingScale
    {
        return GradingScale::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();
    }
}
