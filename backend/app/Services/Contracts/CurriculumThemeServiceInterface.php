<?php

namespace App\Services\Contracts;

use App\Models\CurriculumTheme;
use Illuminate\Database\Eloquent\Collection;

interface CurriculumThemeServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?CurriculumTheme;

    public function create(array $data): CurriculumTheme;

    public function update(int $id, array $data): CurriculumTheme;

    public function delete(int $id): bool;

    public function getThemesForSubject(int $subjectId, int $classLevelId): Collection;
}
