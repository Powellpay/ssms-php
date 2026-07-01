<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Services\Contracts\RoleServiceInterface;

class RoleController extends Controller
{
    public function __construct(
        protected RoleServiceInterface $roleService
    ) {}

    public function index()
    {
        return new RoleCollection($this->roleService->all());
    }

    public function show(int $id)
    {
        return new RoleResource($this->roleService->find($id));
    }

    public function store(RoleRequest $request)
    {
        return new RoleResource($this->roleService->create($request->validated()));
    }

    public function update(RoleRequest $request, int $id)
    {
        return new RoleResource($this->roleService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->roleService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
