<?php

namespace App\Domain\Staff\Services;

use App\Domain\Staff\Models\Staff;
use App\Domain\Staff\Repositories\Contracts\StaffRepositoryInterface;
use App\Domain\Staff\Services\Contracts\StaffServiceInterface;
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
        $modules = $data['modules'] ?? null;
        unset($data['modules']);

        $staff = $this->staffRepository->create($data);
        $this->syncStaffModules($staff, $modules);

        return $staff->load('user');
    }

    public function update(int $id, array $data): Staff
    {
        $modules = $data['modules'] ?? null;
        unset($data['modules']);

        $staff = $this->staffRepository->update($id, $data);
        $this->syncStaffModules($staff, $modules);

        return $staff->load('user');
    }

    public function delete(int $id): bool
    {
        return $this->staffRepository->delete($id);
    }

    public function findByStaffNo(string $no): ?Staff
    {
        return Staff::where('staff_no', $no)->first();
    }

    private function syncStaffModules(Staff $staff, ?array $modules): void
    {
        if ($modules === null) return;

        $user = $staff->user;
        if (!$user) return;

        $user->update(['modules' => $modules]);
    }
}
