<?php

namespace App\Domain\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Finance\Requests\PaymentRequest;
use App\Domain\Finance\Resources\PaymentCollection;
use App\Domain\Finance\Resources\PaymentResource;
use App\Domain\Finance\Services\Contracts\PaymentServiceInterface;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentServiceInterface $paymentService
    ) {}

    public function index()
    {
        return new PaymentCollection($this->paymentService->all());
    }

    public function show(int $id)
    {
        return new PaymentResource($this->paymentService->find($id));
    }

    public function store(PaymentRequest $request)
    {
        return new PaymentResource($this->paymentService->create($request->validated()));
    }

    public function update(PaymentRequest $request, int $id)
    {
        return new PaymentResource($this->paymentService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->paymentService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
