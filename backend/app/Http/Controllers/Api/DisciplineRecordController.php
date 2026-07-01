<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisciplineRecordRequest;
use App\Http\Resources\DisciplineRecordCollection;
use App\Http\Resources\DisciplineRecordResource;
use App\Services\Contracts\DisciplineRecordServiceInterface;

class DisciplineRecordController extends Controller
{
    public function __construct(
        protected DisciplineRecordServiceInterface $disciplineRecordService
    ) {}

    public function index()
    {
        return new DisciplineRecordCollection($this->disciplineRecordService->all());
    }

    public function show(int $id)
    {
        return new DisciplineRecordResource($this->disciplineRecordService->find($id));
    }

    public function store(DisciplineRecordRequest $request)
    {
        return new DisciplineRecordResource($this->disciplineRecordService->create($request->validated()));
    }

    public function update(DisciplineRecordRequest $request, int $id)
    {
        return new DisciplineRecordResource($this->disciplineRecordService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->disciplineRecordService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
