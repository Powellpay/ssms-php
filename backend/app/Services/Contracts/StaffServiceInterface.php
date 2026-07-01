<?php

namespace App\Services\Contracts;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Collection;

interface StaffServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Staff;

    public function create(array $data): Staff;

    public function update(int $id, array $data): Staff;

    public function delete(int $id): bool;

    public function findByStaffNo(string $no): ?Staff;
}
