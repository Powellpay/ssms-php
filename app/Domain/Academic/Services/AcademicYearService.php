<?php

namespace App\Domain\Academic\Services;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;
use App\Domain\Academic\Services\Contracts\AcademicYearServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class AcademicYearService implements AcademicYearServiceInterface
{
    public function __construct(
        protected AcademicYearRepositoryInterface $academicYearRepository
    ) {}

    public function all(): Collection
    {
        return $this->academicYearRepository->all();
    }

    public function find(int $id): ?AcademicYear
    {
        return $this->academicYearRepository->find($id);
    }

    public function create(array $data): AcademicYear
    {
        return $this->academicYearRepository->create($data);
    }

    public function update(int $id, array $data): AcademicYear
    {
        return $this->academicYearRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->academicYearRepository->delete($id);
    }

    public function setCurrent(int $id): void
    {
        $current = $this->findCurrent();
        if ($current) {
            $this->academicYearRepository->update($current->id, ['is_current' => false]);
        }
        $this->academicYearRepository->update($id, ['is_current' => true]);
    }

    public function findCurrent(): ?AcademicYear
    {
        return $this->academicYearRepository->findCurrent();
    }
}
