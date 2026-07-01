<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkillRatingScaleRequest;
use App\Http\Resources\SkillRatingScaleCollection;
use App\Http\Resources\SkillRatingScaleResource;
use App\Services\Contracts\SkillRatingScaleServiceInterface;

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
