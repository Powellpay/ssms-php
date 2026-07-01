<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssessmentRecordRequest;
use App\Http\Resources\AssessmentRecordCollection;
use App\Http\Resources\AssessmentRecordResource;
use App\Services\Contracts\AssessmentRecordServiceInterface;

class AssessmentRecordController extends Controller
{
    public function __construct(
        protected AssessmentRecordServiceInterface $assessmentRecordService
    ) {}

    public function index()
    {
        return new AssessmentRecordCollection($this->assessmentRecordService->all());
    }

    public function show(int $id)
    {
        return new AssessmentRecordResource($this->assessmentRecordService->find($id));
    }

    public function store(AssessmentRecordRequest $request)
    {
        return new AssessmentRecordResource($this->assessmentRecordService->create($request->validated()));
    }

    public function update(AssessmentRecordRequest $request, int $id)
    {
        return new AssessmentRecordResource($this->assessmentRecordService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->assessmentRecordService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
