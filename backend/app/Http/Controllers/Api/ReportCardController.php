<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportCardRequest;
use App\Http\Resources\ReportCardCollection;
use App\Http\Resources\ReportCardResource;
use App\Services\Contracts\ReportCardServiceInterface;

class ReportCardController extends Controller
{
    public function __construct(
        protected ReportCardServiceInterface $reportCardService
    ) {}

    public function index()
    {
        return new ReportCardCollection($this->reportCardService->all());
    }

    public function show(int $id)
    {
        return new ReportCardResource($this->reportCardService->find($id));
    }

    public function store(ReportCardRequest $request)
    {
        return new ReportCardResource($this->reportCardService->create($request->validated()));
    }

    public function update(ReportCardRequest $request, int $id)
    {
        return new ReportCardResource($this->reportCardService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->reportCardService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
