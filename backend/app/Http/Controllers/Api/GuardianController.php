<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuardianRequest;
use App\Http\Resources\GuardianCollection;
use App\Http\Resources\GuardianResource;
use App\Services\Contracts\GuardianServiceInterface;

class GuardianController extends Controller
{
    public function __construct(
        protected GuardianServiceInterface $guardianService
    ) {}

    public function index()
    {
        return new GuardianCollection($this->guardianService->all());
    }

    public function show(int $id)
    {
        return new GuardianResource($this->guardianService->find($id));
    }

    public function store(GuardianRequest $request)
    {
        return new GuardianResource($this->guardianService->create($request->validated()));
    }

    public function update(GuardianRequest $request, int $id)
    {
        return new GuardianResource($this->guardianService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->guardianService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
