<?php

namespace App\Domain\Staff\Repositories\Eloquent;

use App\Domain\Staff\Models\Staff;
use App\Domain\Staff\Repositories\Contracts\StaffRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StaffRepository implements StaffRepositoryInterface
{
    public function all(): Collection
    {
        return Staff::all();
    }

    public function find(int $id): ?Staff
    {
        return Staff::find($id);
    }

    public function create(array $data): Staff
    {
        return Staff::create($data);
    }

    public function update(int $id, array $data): Staff
    {
        $staff = $this->find($id);
        $staff->update($data);
        return $staff;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStaffNo(string $staffNo): ?Staff
    {
        return Staff::where('staff_no', $staffNo)->first();
    }
}
