<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CurriculumThemeRequest;
use App\Http\Resources\CurriculumThemeCollection;
use App\Http\Resources\CurriculumThemeResource;
use App\Services\Contracts\CurriculumThemeServiceInterface;

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
