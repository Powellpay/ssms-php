<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\Contracts\PaymentServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        protected PaymentRepositoryInterface $paymentRepository,
        protected InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function all(): Collection
    {
        return $this->paymentRepository->all();
    }

    public function find(int $id): ?Payment
    {
        return $this->paymentRepository->find($id);
    }

    public function create(array $data): Payment
    {
        return $this->paymentRepository->create($data);
    }

    public function update(int $id, array $data): Payment
    {
        return $this->paymentRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->paymentRepository->delete($id);
    }

    public function recordPayment(int $invoiceId, int $studentId, float $amount, string $method): Payment
    {
        $payment = $this->paymentRepository->create([
            'invoice_id' => $invoiceId,
            'student_id' => $studentId,
            'amount' => $amount,
            'payment_method' => $method,
            'payment_date' => now(),
        ]);

        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $invoice->amount_paid = ($invoice->amount_paid ?? 0) + $amount;

            if ($invoice->amount_paid >= $invoice->total_amount) {
                $invoice->status = 'paid';
            }

            $invoice->save();
        }

        return $payment;
    }
}
