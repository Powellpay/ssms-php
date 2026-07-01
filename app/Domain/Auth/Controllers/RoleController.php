<?php

namespace App\Domain\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Auth\Requests\RoleRequest;
use App\Domain\Auth\Resources\RoleCollection;
use App\Domain\Auth\Resources\RoleResource;
use App\Domain\Auth\Services\Contracts\RoleServiceInterface;

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
