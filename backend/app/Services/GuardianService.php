<?php

namespace App\Services;

use App\Models\Guardian;
use App\Repositories\Contracts\GuardianRepositoryInterface;
use App\Repositories\Contracts\StudentGuardianRepositoryInterface;
use App\Services\Contracts\GuardianServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class GuardianService implements GuardianServiceInterface
{
    public function __construct(
        protected GuardianRepositoryInterface $guardianRepository,
        protected StudentGuardianRepositoryInterface $studentGuardianRepository
    ) {}

    public function all(): Collection
    {
        return $this->guardianRepository->all();
    }

    public function find(int $id): ?Guardian
    {
        return $this->guardianRepository->find($id);
    }

    public function create(array $data): Guardian
    {
        return $this->guardianRepository->create($data);
    }

    public function update(int $id, array $data): Guardian
    {
        return $this->guardianRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->guardianRepository->delete($id);
    }
}
