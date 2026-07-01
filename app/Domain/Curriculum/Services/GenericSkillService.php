<?php

namespace App\Domain\Curriculum\Services;

use App\Domain\Curriculum\Models\GenericSkill;
use App\Domain\Curriculum\Repositories\Contracts\GenericSkillRepositoryInterface;
use App\Domain\Curriculum\Services\Contracts\GenericSkillServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class GenericSkillService implements GenericSkillServiceInterface
{
    public function __construct(
        protected GenericSkillRepositoryInterface $genericSkillRepository
    ) {}

    public function all(): Collection
    {
        return $this->genericSkillRepository->all();
    }

    public function find(int $id): ?GenericSkill
    {
        return $this->genericSkillRepository->find($id);
    }

    public function create(array $data): GenericSkill
    {
        return $this->genericSkillRepository->create($data);
    }

    public function update(int $id, array $data): GenericSkill
    {
        return $this->genericSkillRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->genericSkillRepository->delete($id);
    }
}
