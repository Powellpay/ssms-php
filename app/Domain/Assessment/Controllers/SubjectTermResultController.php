<?php

namespace App\Domain\Assessment\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Assessment\Requests\SubjectTermResultRequest;
use App\Domain\Assessment\Resources\SubjectTermResultCollection;
use App\Domain\Assessment\Resources\SubjectTermResultResource;
use App\Domain\Assessment\Services\Contracts\SubjectTermResultServiceInterface;

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
