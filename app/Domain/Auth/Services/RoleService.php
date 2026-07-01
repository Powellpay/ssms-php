<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Repositories\Contracts\RoleRepositoryInterface;
use App\Domain\Auth\Services\Contracts\RoleServiceInterface;
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
