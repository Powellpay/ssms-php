<?php

namespace App\Domain\Curriculum\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Curriculum\Requests\SubjectTeacherRequest;
use App\Domain\Curriculum\Resources\SubjectTeacherCollection;
use App\Domain\Curriculum\Resources\SubjectTeacherResource;
use App\Domain\Curriculum\Services\Contracts\SubjectTeacherServiceInterface;

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
