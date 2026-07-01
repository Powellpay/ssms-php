<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimetableRequest;
use App\Http\Resources\TimetableCollection;
use App\Http\Resources\TimetableResource;
use App\Services\Contracts\TimetableServiceInterface;

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
