<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenericSkillRequest;
use App\Http\Resources\GenericSkillCollection;
use App\Http\Resources\GenericSkillResource;
use App\Services\Contracts\GenericSkillServiceInterface;

class GenericSkillController extends Controller
{
    public function __construct(
        protected GenericSkillServiceInterface $genericSkillService
    ) {}

    public function index()
    {
        return new GenericSkillCollection($this->genericSkillService->all());
    }

    public function show(int $id)
    {
        return new GenericSkillResource($this->genericSkillService->find($id));
    }

    public function store(GenericSkillRequest $request)
    {
        return new GenericSkillResource($this->genericSkillService->create($request->validated()));
    }

    public function update(GenericSkillRequest $request, int $id)
    {
        return new GenericSkillResource($this->genericSkillService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->genericSkillService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
