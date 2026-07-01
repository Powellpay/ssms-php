<?php

namespace App\Domain\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Finance\Requests\FeeStructureRequest;
use App\Domain\Finance\Resources\FeeStructureCollection;
use App\Domain\Finance\Resources\FeeStructureResource;
use App\Domain\Finance\Services\Contracts\FeeStructureServiceInterface;

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
