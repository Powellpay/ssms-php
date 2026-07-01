<?php

namespace App\Domain\Discipline\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Discipline\Requests\DisciplineRecordRequest;
use App\Domain\Discipline\Resources\DisciplineRecordCollection;
use App\Domain\Discipline\Resources\DisciplineRecordResource;
use App\Domain\Discipline\Services\Contracts\DisciplineRecordServiceInterface;

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
