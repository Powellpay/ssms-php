<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectTeacherRequest;
use App\Http\Resources\SubjectTeacherCollection;
use App\Http\Resources\SubjectTeacherResource;
use App\Services\Contracts\SubjectTeacherServiceInterface;

class SubjectTeacherController extends Controller
{
    public function __construct(
        protected SubjectTeacherServiceInterface $subjectTeacherService
    ) {}

    public function index()
    {
        return new SubjectTeacherCollection($this->subjectTeacherService->all());
    }

    public function show(int $id)
    {
        return new SubjectTeacherResource($this->subjectTeacherService->find($id));
    }

    public function store(SubjectTeacherRequest $request)
    {
        return new SubjectTeacherResource($this->subjectTeacherService->create($request->validated()));
    }

    public function update(SubjectTeacherRequest $request, int $id)
    {
        return new SubjectTeacherResource($this->subjectTeacherService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->subjectTeacherService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
