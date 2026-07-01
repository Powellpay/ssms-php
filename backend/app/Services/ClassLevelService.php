<?php

namespace App\Services;

use App\Models\ClassLevel;
use App\Repositories\Contracts\ClassLevelRepositoryInterface;
use App\Services\Contracts\ClassLevelServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ClassLevelService implements ClassLevelServiceInterface
{
    public function __construct(
        protected ClassLevelRepositoryInterface $classLevelRepository
    ) {}

    public function all(): Collection
    {
        return $this->classLevelRepository->all();
    }

    public function find(int $id): ?ClassLevel
    {
        return $this->classLevelRepository->find($id);
    }

    public function create(array $data): ClassLevel
    {
        return $this->classLevelRepository->create($data);
    }

    public function update(int $id, array $data): ClassLevel
    {
        return $this->classLevelRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->classLevelRepository->delete($id);
    }
}
