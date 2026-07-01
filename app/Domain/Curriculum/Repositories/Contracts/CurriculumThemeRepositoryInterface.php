<?php

namespace App\Domain\Curriculum\Repositories\Contracts;

use App\Domain\Curriculum\Models\CurriculumTheme;
use Illuminate\Database\Eloquent\Collection;

interface CurriculumThemeRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?CurriculumTheme;
    public function create(array $data): CurriculumTheme;
    public function update(int $id, array $data): CurriculumTheme;
    public function delete(int $id): bool;
    public function findBySubject(int $subjectId): Collection;
    public function findByClassLevel(int $classLevelId): Collection;
}
