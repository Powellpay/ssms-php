<?php

namespace App\Domain\Academic\Repositories\Eloquent;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AcademicYearRepository implements AcademicYearRepositoryInterface
{
    public function all(): Collection
    {
        return AcademicYear::all();
    }

    public function find(int $id): ?AcademicYear
    {
        return AcademicYear::find($id);
    }

    public function create(array $data): AcademicYear
    {
        return AcademicYear::create($data);
    }

    public function update(int $id, array $data): AcademicYear
    {
        $academicYear = $this->find($id);
        $academicYear->update($data);
        return $academicYear;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findCurrent(): ?AcademicYear
    {
        return AcademicYear::where('is_current', true)->first();
    }
}
