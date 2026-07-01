<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest;
use App\Http\Resources\AttendanceCollection;
use App\Http\Resources\AttendanceResource;
use App\Services\Contracts\AttendanceServiceInterface;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceServiceInterface $attendanceService
    ) {}

    public function index()
    {
        return new AttendanceCollection($this->attendanceService->all());
    }

    public function show(int $id)
    {
        return new AttendanceResource($this->attendanceService->find($id));
    }

    public function store(AttendanceRequest $request)
    {
        return new AttendanceResource($this->attendanceService->create($request->validated()));
    }

    public function update(AttendanceRequest $request, int $id)
    {
        return new AttendanceResource($this->attendanceService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->attendanceService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
