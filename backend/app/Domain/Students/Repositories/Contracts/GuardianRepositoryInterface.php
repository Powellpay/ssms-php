<?php

namespace App\Domain\Students\Repositories\Contracts;

use App\Domain\Students\Models\Guardian;
use Illuminate\Database\Eloquent\Collection;

interface GuardianRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Guardian;
    public function create(array $data): Guardian;
    public function update(int $id, array $data): Guardian;
    public function delete(int $id): bool;
}
