<?php

namespace App\Domain\Library\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Library\Requests\LibraryBookRequest;
use App\Domain\Library\Resources\LibraryBookCollection;
use App\Domain\Library\Resources\LibraryBookResource;
use App\Domain\Library\Services\Contracts\LibraryBookServiceInterface;

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
