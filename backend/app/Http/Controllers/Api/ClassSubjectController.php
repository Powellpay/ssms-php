<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassSubjectRequest;
use App\Http\Resources\ClassSubjectCollection;
use App\Http\Resources\ClassSubjectResource;
use App\Services\Contracts\ClassSubjectServiceInterface;

class ClassSubjectController extends Controller
{
    public function __construct(
        protected ClassSubjectServiceInterface $classSubjectService
    ) {}

    public function index()
    {
        return new ClassSubjectCollection($this->classSubjectService->all());
    }

    public function show(int $id)
    {
        return new ClassSubjectResource($this->classSubjectService->find($id));
    }

    public function store(ClassSubjectRequest $request)
    {
        return new ClassSubjectResource($this->classSubjectService->create($request->validated()));
    }

    public function update(ClassSubjectRequest $request, int $id)
    {
        return new ClassSubjectResource($this->classSubjectService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->classSubjectService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
