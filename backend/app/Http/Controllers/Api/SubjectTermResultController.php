<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectTermResultRequest;
use App\Http\Resources\SubjectTermResultCollection;
use App\Http\Resources\SubjectTermResultResource;
use App\Services\Contracts\SubjectTermResultServiceInterface;

class SubjectTermResultController extends Controller
{
    public function __construct(
        protected SubjectTermResultServiceInterface $subjectTermResultService
    ) {}

    public function index()
    {
        return new SubjectTermResultCollection($this->subjectTermResultService->all());
    }

    public function show(int $id)
    {
        return new SubjectTermResultResource($this->subjectTermResultService->find($id));
    }

    public function store(SubjectTermResultRequest $request)
    {
        return new SubjectTermResultResource($this->subjectTermResultService->create($request->validated()));
    }

    public function update(SubjectTermResultRequest $request, int $id)
    {
        return new SubjectTermResultResource($this->subjectTermResultService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->subjectTermResultService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
