<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookLoanRequest;
use App\Http\Resources\BookLoanCollection;
use App\Http\Resources\BookLoanResource;
use App\Services\Contracts\BookLoanServiceInterface;

class BookLoanController extends Controller
{
    public function __construct(
        protected BookLoanServiceInterface $bookLoanService
    ) {}

    public function index()
    {
        return new BookLoanCollection($this->bookLoanService->all());
    }

    public function show(int $id)
    {
        return new BookLoanResource($this->bookLoanService->find($id));
    }

    public function store(BookLoanRequest $request)
    {
        return new BookLoanResource($this->bookLoanService->create($request->validated()));
    }

    public function update(BookLoanRequest $request, int $id)
    {
        return new BookLoanResource($this->bookLoanService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->bookLoanService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
