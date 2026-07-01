<?php

namespace App\Domain\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Academic\Requests\AcademicYearRequest;
use App\Domain\Academic\Resources\AcademicYearCollection;
use App\Domain\Academic\Resources\AcademicYearResource;
use App\Domain\Academic\Services\Contracts\AcademicYearServiceInterface;

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
