<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenericSkillRatingRequest;
use App\Http\Resources\GenericSkillRatingCollection;
use App\Http\Resources\GenericSkillRatingResource;
use App\Services\Contracts\GenericSkillRatingServiceInterface;

class GenericSkillRatingController extends Controller
{
    public function __construct(
        protected GenericSkillRatingServiceInterface $genericSkillRatingService
    ) {}

    public function index()
    {
        return new GenericSkillRatingCollection($this->genericSkillRatingService->all());
    }

    public function show(int $id)
    {
        return new GenericSkillRatingResource($this->genericSkillRatingService->find($id));
    }

    public function store(GenericSkillRatingRequest $request)
    {
        return new GenericSkillRatingResource($this->genericSkillRatingService->create($request->validated()));
    }

    public function update(GenericSkillRatingRequest $request, int $id)
    {
        return new GenericSkillRatingResource($this->genericSkillRatingService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->genericSkillRatingService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
