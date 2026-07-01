<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassLevelRequest;
use App\Http\Resources\ClassLevelCollection;
use App\Http\Resources\ClassLevelResource;
use App\Services\Contracts\ClassLevelServiceInterface;

class ClassLevelController extends Controller
{
    public function __construct(
        protected ClassLevelServiceInterface $classLevelService
    ) {}

    public function index()
    {
        return new ClassLevelCollection($this->classLevelService->all());
    }

    public function show(int $id)
    {
        return new ClassLevelResource($this->classLevelService->find($id));
    }

    public function store(ClassLevelRequest $request)
    {
        return new ClassLevelResource($this->classLevelService->create($request->validated()));
    }

    public function update(ClassLevelRequest $request, int $id)
    {
        return new ClassLevelResource($this->classLevelService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->classLevelService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
