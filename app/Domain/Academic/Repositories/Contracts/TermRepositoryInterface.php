<?php

namespace App\Domain\Academic\Repositories\Contracts;

use App\Domain\Academic\Models\Term;
use Illuminate\Database\Eloquent\Collection;

interface TermRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Term;
    public function create(array $data): Term;
    public function update(int $id, array $data): Term;
    public function delete(int $id): bool;
    public function findCurrent(): ?Term;
    public function findByAcademicYear(int $yearId): Collection;
}
