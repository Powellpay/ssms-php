<?php

namespace App\Domain\Auth\Services\Contracts;

use App\Domain\Auth\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Role;

    public function create(array $data): Role;

    public function update(int $id, array $data): Role;

    public function delete(int $id): bool;
}
