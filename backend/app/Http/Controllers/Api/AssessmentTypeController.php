<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssessmentTypeRequest;
use App\Http\Resources\AssessmentTypeCollection;
use App\Http\Resources\AssessmentTypeResource;
use App\Services\Contracts\AssessmentTypeServiceInterface;

class AssessmentTypeController extends Controller
{
    public function __construct(
        protected AssessmentTypeServiceInterface $assessmentTypeService
    ) {}

    public function index()
    {
        return new AssessmentTypeCollection($this->assessmentTypeService->all());
    }

    public function show(int $id)
    {
        return new AssessmentTypeResource($this->assessmentTypeService->find($id));
    }

    public function store(AssessmentTypeRequest $request)
    {
        return new AssessmentTypeResource($this->assessmentTypeService->create($request->validated()));
    }

    public function update(AssessmentTypeRequest $request, int $id)
    {
        return new AssessmentTypeResource($this->assessmentTypeService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->assessmentTypeService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
