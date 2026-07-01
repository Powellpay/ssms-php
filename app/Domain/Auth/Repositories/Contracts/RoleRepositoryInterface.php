<?php

namespace App\Domain\Auth\Repositories\Contracts;

use App\Domain\Auth\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Role;
    public function create(array $data): Role;
    public function update(int $id, array $data): Role;
    public function delete(int $id): bool;
    public function findByName(string $name): ?Role;
}
