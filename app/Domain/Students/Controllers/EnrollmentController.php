<?php

namespace App\Domain\Students\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Students\Requests\EnrollmentRequest;
use App\Domain\Students\Resources\EnrollmentCollection;
use App\Domain\Students\Resources\EnrollmentResource;
use App\Domain\Students\Services\Contracts\EnrollmentServiceInterface;

class EnrollmentController extends Controller
{
    public function __construct(
        protected EnrollmentServiceInterface $enrollmentService
    ) {}

    public function index()
    {
        return new EnrollmentCollection($this->enrollmentService->all());
    }

    public function show(int $id)
    {
        return new EnrollmentResource($this->enrollmentService->find($id));
    }

    public function store(EnrollmentRequest $request)
    {
        return new EnrollmentResource($this->enrollmentService->create($request->validated()));
    }

    public function update(EnrollmentRequest $request, int $id)
    {
        return new EnrollmentResource($this->enrollmentService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->enrollmentService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
