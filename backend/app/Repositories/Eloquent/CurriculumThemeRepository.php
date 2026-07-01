<?php

namespace App\Repositories\Eloquent;

use App\Models\CurriculumTheme;
use App\Repositories\Contracts\CurriculumThemeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CurriculumThemeRepository implements CurriculumThemeRepositoryInterface
{
    public function all(): Collection
    {
        return CurriculumTheme::all();
    }

    public function find(int $id): ?CurriculumTheme
    {
        return CurriculumTheme::find($id);
    }

    public function create(array $data): CurriculumTheme
    {
        return CurriculumTheme::create($data);
    }

    public function update(int $id, array $data): CurriculumTheme
    {
        $curriculumTheme = $this->find($id);
        $curriculumTheme->update($data);
        return $curriculumTheme;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findBySubject(int $subjectId): Collection
    {
        return CurriculumTheme::where('subject_id', $subjectId)->get();
    }

    public function findByClassLevel(int $classLevelId): Collection
    {
        return CurriculumTheme::where('class_level_id', $classLevelId)->get();
    }
}
