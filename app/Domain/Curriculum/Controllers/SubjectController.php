<?php

namespace App\Domain\Curriculum\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Curriculum\Requests\SubjectRequest;
use App\Domain\Curriculum\Resources\SubjectCollection;
use App\Domain\Curriculum\Resources\SubjectResource;
use App\Domain\Curriculum\Services\Contracts\SubjectServiceInterface;

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
