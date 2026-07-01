<?php

namespace App\Domain\Academic\Services\Contracts;

use App\Domain\Academic\Models\Stream;
use Illuminate\Database\Eloquent\Collection;

interface StreamServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Stream;

    public function create(array $data): Stream;

    public function update(int $id, array $data): Stream;

    public function delete(int $id): bool;

    public function findByClassLevel(int $levelId): Collection;

    public function findByAcademicYear(int $yearId): Collection;
}
