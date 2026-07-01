<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Resources\AnnouncementCollection;
use App\Http\Resources\AnnouncementResource;
use App\Services\Contracts\AnnouncementServiceInterface;

class AnnouncementController extends Controller
{
    public function __construct(
        protected AnnouncementServiceInterface $announcementService
    ) {}

    public function index()
    {
        return new AnnouncementCollection($this->announcementService->all());
    }

    public function show(int $id)
    {
        return new AnnouncementResource($this->announcementService->find($id));
    }

    public function store(AnnouncementRequest $request)
    {
        return new AnnouncementResource($this->announcementService->create($request->validated()));
    }

    public function update(AnnouncementRequest $request, int $id)
    {
        return new AnnouncementResource($this->announcementService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->announcementService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
