<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TermRequest;
use App\Http\Resources\TermCollection;
use App\Http\Resources\TermResource;
use App\Services\Contracts\TermServiceInterface;

class TermController extends Controller
{
    public function __construct(
        protected TermServiceInterface $termService
    ) {}

    public function index()
    {
        return new TermCollection($this->termService->all());
    }

    public function show(int $id)
    {
        return new TermResource($this->termService->find($id));
    }

    public function store(TermRequest $request)
    {
        return new TermResource($this->termService->create($request->validated()));
    }

    public function update(TermRequest $request, int $id)
    {
        return new TermResource($this->termService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->termService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
