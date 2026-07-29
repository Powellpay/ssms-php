<?php

namespace App\Domain\Staff\Services;

use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use App\Domain\Auth\Services\ModuleAccessService;
use App\Domain\Staff\Models\Staff;
use App\Domain\Staff\Repositories\Contracts\StaffRepositoryInterface;
use App\Domain\Staff\Services\Contracts\StaffServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

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
        $modules = $data['modules'] ?? [];
        $password = $data['password'] ?? null;
        $email = $data['email'] ?? null;
        unset($data['modules'], $data['password'], $data['password_confirmation']);

        $staff = $this->staffRepository->create($data);

        if ($email && $password) {
            $this->createOrUpdateUser($staff, $email, $password, $modules);
        } else {
            $this->syncStaffModules($staff, $modules);
        }

        return $staff->load('user.role');
    }

    public function update(int $id, array $data): Staff
    {
        $modules = $data['modules'] ?? null;
        $password = $data['password'] ?? null;
        $email = $data['email'] ?? null;
        unset($data['modules'], $data['password'], $data['password_confirmation']);

        $staff = $this->staffRepository->update($id, $data);

        if ($email && $password) {
            $this->createOrUpdateUser($staff, $email, $password, $modules ?? []);
        } else {
            $this->syncStaffModules($staff, $modules);
        }

        return $staff->load('user.role');
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

    private function createOrUpdateUser(Staff $staff, string $email, string $password, array $modules): User
    {
        $user = $staff->user;

        if ($user) {
            $user->update([
                'email' => $email,
                'password' => Hash::make($password),
                'modules' => $modules,
            ]);
            return $user;
        }

        $teacherRole = Role::findBySlug('teacher', $staff->school_id);

        $user = User::create([
            'school_id' => $staff->school_id,
            'role_id' => $teacherRole?->id ?? 4,
            'username' => strstr($email, '@', true),
            'name' => $staff->first_name . ' ' . $staff->last_name,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => 'active',
            'modules' => $modules,
        ]);

        $staff->update(['user_id' => $user->id]);

        return $user;
    }
}
