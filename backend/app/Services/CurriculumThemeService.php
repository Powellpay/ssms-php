<?php

namespace App\Services;

use App\Models\CurriculumTheme;
use App\Repositories\Contracts\CurriculumThemeRepositoryInterface;
use App\Services\Contracts\CurriculumThemeServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class CurriculumThemeService implements CurriculumThemeServiceInterface
{
    public function __construct(
        protected CurriculumThemeRepositoryInterface $curriculumThemeRepository
    ) {}

    public function all(): Collection
    {
        return $this->curriculumThemeRepository->all();
    }

    public function find(int $id): ?CurriculumTheme
    {
        return $this->curriculumThemeRepository->find($id);
    }

    public function create(array $data): CurriculumTheme
    {
        return $this->curriculumThemeRepository->create($data);
    }

    public function update(int $id, array $data): CurriculumTheme
    {
        return $this->curriculumThemeRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->curriculumThemeRepository->delete($id);
    }

    public function getThemesForSubject(int $subjectId, int $classLevelId): Collection
    {
        return CurriculumTheme::with('learningOutcomes')
            ->where('subject_id', $subjectId)
            ->where('class_level_id', $classLevelId)
            ->get();
    }
}
