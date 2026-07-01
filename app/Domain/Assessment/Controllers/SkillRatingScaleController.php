<?php

namespace App\Domain\Assessment\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Assessment\Requests\SkillRatingScaleRequest;
use App\Domain\Assessment\Resources\SkillRatingScaleCollection;
use App\Domain\Assessment\Resources\SkillRatingScaleResource;
use App\Domain\Assessment\Services\Contracts\SkillRatingScaleServiceInterface;

class SkillRatingScaleController extends Controller
{
    public function __construct(
        protected SkillRatingScaleServiceInterface $skillRatingScaleService
    ) {}

    public function index()
    {
        return new SkillRatingScaleCollection($this->skillRatingScaleService->all());
    }

    public function show(int $id)
    {
        return new SkillRatingScaleResource($this->skillRatingScaleService->find($id));
    }

    public function store(SkillRatingScaleRequest $request)
    {
        return new SkillRatingScaleResource($this->skillRatingScaleService->create($request->validated()));
    }

    public function update(SkillRatingScaleRequest $request, int $id)
    {
        return new SkillRatingScaleResource($this->skillRatingScaleService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->skillRatingScaleService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
