<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function all(): Collection
    {
        return Payment::all();
    }

    public function find(int $id): ?Payment
    {
        return Payment::find($id);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(int $id, array $data): Payment
    {
        $payment = $this->find($id);
        $payment->update($data);
        return $payment;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByInvoice(int $invoiceId): Collection
    {
        return Payment::where('invoice_id', $invoiceId)->get();
    }

    public function findByStudent(int $studentId): Collection
    {
        return Payment::where('student_id', $studentId)->get();
    }
}
