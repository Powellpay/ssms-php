<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentRequest;
use App\Http\Resources\EnrollmentCollection;
use App\Http\Resources\EnrollmentResource;
use App\Services\Contracts\EnrollmentServiceInterface;

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
