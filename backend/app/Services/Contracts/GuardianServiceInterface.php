<?php

namespace App\Services\Contracts;

use App\Models\Guardian;
use Illuminate\Database\Eloquent\Collection;

interface GuardianServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Guardian;

    public function create(array $data): Guardian;

    public function update(int $id, array $data): Guardian;

    public function delete(int $id): bool;
}
