<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LibraryBookRequest;
use App\Http\Resources\LibraryBookCollection;
use App\Http\Resources\LibraryBookResource;
use App\Services\Contracts\LibraryBookServiceInterface;

class LibraryBookController extends Controller
{
    public function __construct(
        protected LibraryBookServiceInterface $libraryBookService
    ) {}

    public function index()
    {
        return new LibraryBookCollection($this->libraryBookService->all());
    }

    public function show(int $id)
    {
        return new LibraryBookResource($this->libraryBookService->find($id));
    }

    public function store(LibraryBookRequest $request)
    {
        return new LibraryBookResource($this->libraryBookService->create($request->validated()));
    }

    public function update(LibraryBookRequest $request, int $id)
    {
        return new LibraryBookResource($this->libraryBookService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->libraryBookService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
