<?php

namespace App\Domain\Curriculum\Services\Contracts;

use App\Domain\Curriculum\Models\GenericSkill;
use Illuminate\Database\Eloquent\Collection;

interface GenericSkillServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?GenericSkill;

    public function create(array $data): GenericSkill;

    public function update(int $id, array $data): GenericSkill;

    public function delete(int $id): bool;
}
