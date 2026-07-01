<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicYearRequest;
use App\Http\Resources\AcademicYearCollection;
use App\Http\Resources\AcademicYearResource;
use App\Services\Contracts\AcademicYearServiceInterface;

class AcademicYearController extends Controller
{
    public function __construct(
        protected AcademicYearServiceInterface $academicYearService
    ) {}

    public function index()
    {
        return new AcademicYearCollection($this->academicYearService->all());
    }

    public function show(int $id)
    {
        return new AcademicYearResource($this->academicYearService->find($id));
    }

    public function store(AcademicYearRequest $request)
    {
        return new AcademicYearResource($this->academicYearService->create($request->validated()));
    }

    public function update(AcademicYearRequest $request, int $id)
    {
        return new AcademicYearResource($this->academicYearService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->academicYearService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function current()
    {
        return response()->json($this->academicYearService->findCurrent());
    }

    public function setCurrent(int $id)
    {
        $this->academicYearService->setCurrent($id);
        return response()->json(['message' => 'Current academic year updated']);
    }
}
