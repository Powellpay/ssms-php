<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentCollection;
use App\Http\Resources\StudentResource;
use App\Services\Contracts\StudentServiceInterface;

class StudentController extends Controller
{
    public function __construct(
        protected StudentServiceInterface $studentService
    ) {}

    public function index()
    {
        return new StudentCollection($this->studentService->all());
    }

    public function show(int $id)
    {
        return new StudentResource($this->studentService->find($id));
    }

    public function store(StudentRequest $request)
    {
        $student = $this->studentService->create($request->validated());
        return new StudentResource($student);
    }

    public function update(StudentRequest $request, int $id)
    {
        $student = $this->studentService->update($id, $request->validated());
        return new StudentResource($student);
    }

    public function destroy(int $id)
    {
        $this->studentService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function findByAdmissionNo(string $admissionNo)
    {
        return new StudentResource($this->studentService->findByAdmissionNo($admissionNo));
    }

    public function findByStatus(string $status)
    {
        return new StudentCollection($this->studentService->findByStatus($status));
    }

    public function currentEnrollment(int $id)
    {
        return response()->json($this->studentService->getCurrentEnrollment($id));
    }
}
