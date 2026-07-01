<?php

namespace App\Repositories\Contracts;

use App\Models\Guardian;
use Illuminate\Database\Eloquent\Collection;

interface GuardianRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Guardian;
    public function create(array $data): Guardian;
    public function update(int $id, array $data): Guardian;
    public function delete(int $id): bool;
}
