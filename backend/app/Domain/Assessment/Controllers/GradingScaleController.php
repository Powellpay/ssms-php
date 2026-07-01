<?php

namespace App\Domain\Assessment\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Assessment\Requests\GradingScaleRequest;
use App\Domain\Assessment\Resources\GradingScaleCollection;
use App\Domain\Assessment\Resources\GradingScaleResource;
use App\Domain\Assessment\Services\Contracts\GradingScaleServiceInterface;

class GradingScaleController extends Controller
{
    public function __construct(
        protected GradingScaleServiceInterface $gradingScaleService
    ) {}

    public function index()
    {
        return new GradingScaleCollection($this->gradingScaleService->all());
    }

    public function show(int $id)
    {
        return new GradingScaleResource($this->gradingScaleService->find($id));
    }

    public function store(GradingScaleRequest $request)
    {
        return new GradingScaleResource($this->gradingScaleService->create($request->validated()));
    }

    public function update(GradingScaleRequest $request, int $id)
    {
        return new GradingScaleResource($this->gradingScaleService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->gradingScaleService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
