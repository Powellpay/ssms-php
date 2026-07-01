<?php

namespace App\Domain\Timetable\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Timetable\Requests\TimetableRequest;
use App\Domain\Timetable\Resources\TimetableCollection;
use App\Domain\Timetable\Resources\TimetableResource;
use App\Domain\Timetable\Services\Contracts\TimetableServiceInterface;

class TimetableController extends Controller
{
    public function __construct(
        protected TimetableServiceInterface $timetableService
    ) {}

    public function index()
    {
        return new TimetableCollection($this->timetableService->all());
    }

    public function show(int $id)
    {
        return new TimetableResource($this->timetableService->find($id));
    }

    public function store(TimetableRequest $request)
    {
        return new TimetableResource($this->timetableService->create($request->validated()));
    }

    public function update(TimetableRequest $request, int $id)
    {
        return new TimetableResource($this->timetableService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->timetableService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
