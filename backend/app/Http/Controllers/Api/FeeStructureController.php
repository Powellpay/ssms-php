<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeeStructureRequest;
use App\Http\Resources\FeeStructureCollection;
use App\Http\Resources\FeeStructureResource;
use App\Services\Contracts\FeeStructureServiceInterface;

class FeeStructureController extends Controller
{
    public function __construct(
        protected FeeStructureServiceInterface $feeStructureService
    ) {}

    public function index()
    {
        return new FeeStructureCollection($this->feeStructureService->all());
    }

    public function show(int $id)
    {
        return new FeeStructureResource($this->feeStructureService->find($id));
    }

    public function store(FeeStructureRequest $request)
    {
        return new FeeStructureResource($this->feeStructureService->create($request->validated()));
    }

    public function update(FeeStructureRequest $request, int $id)
    {
        return new FeeStructureResource($this->feeStructureService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->feeStructureService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
