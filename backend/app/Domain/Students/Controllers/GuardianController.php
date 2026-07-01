<?php

namespace App\Domain\Students\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Students\Requests\GuardianRequest;
use App\Domain\Students\Resources\GuardianCollection;
use App\Domain\Students\Resources\GuardianResource;
use App\Domain\Students\Services\Contracts\GuardianServiceInterface;

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
