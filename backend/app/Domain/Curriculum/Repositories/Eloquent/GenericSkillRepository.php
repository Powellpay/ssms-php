<?php

namespace App\Domain\Curriculum\Repositories\Eloquent;

use App\Domain\Curriculum\Models\GenericSkill;
use App\Domain\Curriculum\Repositories\Contracts\GenericSkillRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GenericSkillRepository implements GenericSkillRepositoryInterface
{
    public function all(): Collection
    {
        return GenericSkill::all();
    }

    public function find(int $id): ?GenericSkill
    {
        return GenericSkill::find($id);
    }

    public function create(array $data): GenericSkill
    {
        return GenericSkill::create($data);
    }

    public function update(int $id, array $data): GenericSkill
    {
        $genericSkill = $this->find($id);
        $genericSkill->update($data);
        return $genericSkill;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByName(string $name): ?GenericSkill
    {
        return GenericSkill::where('skill_name', $name)->first();
    }
}
