<?php

namespace App\Services\Contracts;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Collection;

interface AcademicYearServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?AcademicYear;

    public function create(array $data): AcademicYear;

    public function update(int $id, array $data): AcademicYear;

    public function delete(int $id): bool;

    public function setCurrent(int $id): void;

    public function findCurrent(): ?AcademicYear;
}
