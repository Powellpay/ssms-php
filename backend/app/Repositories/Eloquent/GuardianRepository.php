<?php

namespace App\Repositories\Eloquent;

use App\Models\Guardian;
use App\Repositories\Contracts\GuardianRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GuardianRepository implements GuardianRepositoryInterface
{
    public function all(): Collection
    {
        return Guardian::all();
    }

    public function find(int $id): ?Guardian
    {
        return Guardian::find($id);
    }

    public function create(array $data): Guardian
    {
        return Guardian::create($data);
    }

    public function update(int $id, array $data): Guardian
    {
        $guardian = $this->find($id);
        $guardian->update($data);
        return $guardian;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }
}
