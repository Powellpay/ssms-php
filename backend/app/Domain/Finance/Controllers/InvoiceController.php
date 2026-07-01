<?php

namespace App\Domain\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Finance\Requests\InvoiceRequest;
use App\Domain\Finance\Resources\InvoiceCollection;
use App\Domain\Finance\Resources\InvoiceResource;
use App\Domain\Finance\Services\Contracts\InvoiceServiceInterface;

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
