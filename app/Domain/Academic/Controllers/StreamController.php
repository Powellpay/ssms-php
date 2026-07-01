<?php

namespace App\Domain\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Academic\Requests\StreamRequest;
use App\Domain\Academic\Resources\StreamCollection;
use App\Domain\Academic\Resources\StreamResource;
use App\Domain\Academic\Services\Contracts\StreamServiceInterface;

class StreamController extends Controller
{
    public function __construct(
        protected StreamServiceInterface $streamService
    ) {}

    public function index()
    {
        return new StreamCollection($this->streamService->all());
    }

    public function show(int $id)
    {
        return new StreamResource($this->streamService->find($id));
    }

    public function store(StreamRequest $request)
    {
        return new StreamResource($this->streamService->create($request->validated()));
    }

    public function update(StreamRequest $request, int $id)
    {
        return new StreamResource($this->streamService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->streamService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
