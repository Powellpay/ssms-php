<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Http\Resources\StaffCollection;
use App\Http\Resources\StaffResource;
use App\Services\Contracts\StaffServiceInterface;

class StaffController extends Controller
{
    public function __construct(
        protected StaffServiceInterface $staffService
    ) {}

    public function index()
    {
        return new StaffCollection($this->staffService->all());
    }

    public function show(int $id)
    {
        return new StaffResource($this->staffService->find($id));
    }

    public function store(StaffRequest $request)
    {
        return new StaffResource($this->staffService->create($request->validated()));
    }

    public function update(StaffRequest $request, int $id)
    {
        return new StaffResource($this->staffService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->staffService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
