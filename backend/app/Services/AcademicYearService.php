<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Repositories\Contracts\AcademicYearRepositoryInterface;
use App\Services\Contracts\AcademicYearServiceInterface;
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
