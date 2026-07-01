<?php

namespace App\Domain\Academic\Repositories\Contracts;

use App\Domain\Academic\Models\ClassLevel;
use Illuminate\Database\Eloquent\Collection;

interface ClassLevelRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?ClassLevel;
    public function create(array $data): ClassLevel;
    public function update(int $id, array $data): ClassLevel;
    public function delete(int $id): bool;
}
