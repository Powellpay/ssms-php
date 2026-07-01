<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LearningOutcomeRequest;
use App\Http\Resources\LearningOutcomeCollection;
use App\Http\Resources\LearningOutcomeResource;
use App\Services\Contracts\LearningOutcomeServiceInterface;

class LearningOutcomeController extends Controller
{
    public function __construct(
        protected LearningOutcomeServiceInterface $learningOutcomeService
    ) {}

    public function index()
    {
        return new LearningOutcomeCollection($this->learningOutcomeService->all());
    }

    public function show(int $id)
    {
        return new LearningOutcomeResource($this->learningOutcomeService->find($id));
    }

    public function store(LearningOutcomeRequest $request)
    {
        return new LearningOutcomeResource($this->learningOutcomeService->create($request->validated()));
    }

    public function update(LearningOutcomeRequest $request, int $id)
    {
        return new LearningOutcomeResource($this->learningOutcomeService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->learningOutcomeService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
