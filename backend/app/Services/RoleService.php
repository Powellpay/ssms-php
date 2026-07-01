<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Services\Contracts\RoleServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class RoleService implements RoleServiceInterface
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository
    ) {}

    public function all(): Collection
    {
        return $this->roleRepository->all();
    }

    public function find(int $id): ?Role
    {
        return $this->roleRepository->find($id);
    }

    public function create(array $data): Role
    {
        return $this->roleRepository->create($data);
    }

    public function update(int $id, array $data): Role
    {
        return $this->roleRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->roleRepository->delete($id);
    }
}
