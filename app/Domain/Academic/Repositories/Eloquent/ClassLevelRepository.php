<?php

namespace App\Domain\Academic\Repositories\Eloquent;

use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Academic\Repositories\Contracts\ClassLevelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ClassLevelRepository implements ClassLevelRepositoryInterface
{
    public function all(): Collection
    {
        return ClassLevel::all();
    }

    public function find(int $id): ?ClassLevel
    {
        return ClassLevel::find($id);
    }

    public function create(array $data): ClassLevel
    {
        return ClassLevel::create($data);
    }

    public function update(int $id, array $data): ClassLevel
    {
        $classLevel = $this->find($id);
        $classLevel->update($data);
        return $classLevel;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }
}
