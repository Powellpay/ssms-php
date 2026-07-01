<?php

namespace App\Repositories\Eloquent;

use App\Models\ReportCard;
use App\Repositories\Contracts\ReportCardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ReportCardRepository implements ReportCardRepositoryInterface
{
    public function all(): Collection
    {
        return ReportCard::all();
    }

    public function find(int $id): ?ReportCard
    {
        return ReportCard::find($id);
    }

    public function create(array $data): ReportCard
    {
        return ReportCard::create($data);
    }

    public function update(int $id, array $data): ReportCard
    {
        $reportCard = $this->find($id);
        $reportCard->update($data);
        return $reportCard;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStudentAndTerm(int $studentId, int $termId): ?ReportCard
    {
        return ReportCard::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->first();
    }
}
