<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceResource;
use App\Services\Contracts\InvoiceServiceInterface;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceServiceInterface $invoiceService
    ) {}

    public function index()
    {
        return new InvoiceCollection($this->invoiceService->all());
    }

    public function show(int $id)
    {
        return new InvoiceResource($this->invoiceService->find($id));
    }

    public function store(InvoiceRequest $request)
    {
        return new InvoiceResource($this->invoiceService->create($request->validated()));
    }

    public function update(InvoiceRequest $request, int $id)
    {
        return new InvoiceResource($this->invoiceService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->invoiceService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
