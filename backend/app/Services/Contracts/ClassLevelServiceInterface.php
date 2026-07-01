<?php

namespace App\Services\Contracts;

use App\Models\ClassLevel;
use Illuminate\Database\Eloquent\Collection;

interface ClassLevelServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?ClassLevel;

    public function create(array $data): ClassLevel;

    public function update(int $id, array $data): ClassLevel;

    public function delete(int $id): bool;
}
