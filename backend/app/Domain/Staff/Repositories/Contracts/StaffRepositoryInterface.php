<?php

namespace App\Domain\Staff\Repositories\Contracts;

use App\Domain\Staff\Models\Staff;
use Illuminate\Database\Eloquent\Collection;

interface StaffRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Staff;
    public function create(array $data): Staff;
    public function update(int $id, array $data): Staff;
    public function delete(int $id): bool;
    public function findByStaffNo(string $staffNo): ?Staff;
}
