<?php

namespace App\Domain\Curriculum\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Curriculum\Requests\CurriculumThemeRequest;
use App\Domain\Curriculum\Resources\CurriculumThemeCollection;
use App\Domain\Curriculum\Resources\CurriculumThemeResource;
use App\Domain\Curriculum\Services\Contracts\CurriculumThemeServiceInterface;

class CurriculumThemeController extends Controller
{
    public function __construct(
        protected CurriculumThemeServiceInterface $curriculumThemeService
    ) {}

    public function index()
    {
        return new CurriculumThemeCollection($this->curriculumThemeService->all());
    }

    public function show(int $id)
    {
        return new CurriculumThemeResource($this->curriculumThemeService->find($id));
    }

    public function store(CurriculumThemeRequest $request)
    {
        return new CurriculumThemeResource($this->curriculumThemeService->create($request->validated()));
    }

    public function update(CurriculumThemeRequest $request, int $id)
    {
        return new CurriculumThemeResource($this->curriculumThemeService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->curriculumThemeService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
