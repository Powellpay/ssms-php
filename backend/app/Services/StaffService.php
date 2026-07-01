<?php

namespace App\Services;

use App\Models\Staff;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Services\Contracts\StaffServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class StaffService implements StaffServiceInterface
{
    public function __construct(
        protected StaffRepositoryInterface $staffRepository
    ) {}

    public function all(): Collection
    {
        return $this->staffRepository->all();
    }

    public function find(int $id): ?Staff
    {
        return $this->staffRepository->find($id);
    }

    public function create(array $data): Staff
    {
        return $this->staffRepository->create($data);
    }

    public function update(int $id, array $data): Staff
    {
        return $this->staffRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->staffRepository->delete($id);
    }

    public function findByStaffNo(string $no): ?Staff
    {
        return Staff::where('staff_no', $no)->first();
    }
}
