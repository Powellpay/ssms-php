<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectRequest;
use App\Http\Resources\SubjectCollection;
use App\Http\Resources\SubjectResource;
use App\Services\Contracts\SubjectServiceInterface;

class SubjectController extends Controller
{
    public function __construct(
        protected SubjectServiceInterface $subjectService
    ) {}

    public function index()
    {
        return new SubjectCollection($this->subjectService->all());
    }

    public function show(int $id)
    {
        return new SubjectResource($this->subjectService->find($id));
    }

    public function store(SubjectRequest $request)
    {
        return new SubjectResource($this->subjectService->create($request->validated()));
    }

    public function update(SubjectRequest $request, int $id)
    {
        return new SubjectResource($this->subjectService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->subjectService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
