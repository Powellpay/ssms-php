<?php

namespace App\Domain\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Auth\Requests\UserRequest;
use App\Domain\Auth\Resources\UserCollection;
use App\Domain\Auth\Resources\UserResource;
use App\Domain\Auth\Services\Contracts\UserServiceInterface;

class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    public function index()
    {
        return new UserCollection($this->userService->all());
    }

    public function show(int $id)
    {
        return new UserResource($this->userService->find($id));
    }

    public function store(UserRequest $request)
    {
        $user = $this->userService->create($request->validated());
        return new UserResource($user);
    }

    public function update(UserRequest $request, int $id)
    {
        $user = $this->userService->update($id, $request->validated());
        return new UserResource($user);
    }

    public function destroy(int $id)
    {
        $this->userService->delete($id);
        return response()->json(['message' => 'User deleted successfully']);
    }
}
