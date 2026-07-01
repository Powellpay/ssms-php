<?php

namespace App\Domain\Finance\Services;

use App\Domain\Finance\Models\Invoice;
use App\Domain\Finance\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Domain\Finance\Services\Contracts\InvoiceServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(
        protected InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function all(): Collection
    {
        return $this->invoiceRepository->all();
    }

    public function find(int $id): ?Invoice
    {
        return $this->invoiceRepository->find($id);
    }

    public function create(array $data): Invoice
    {
        return $this->invoiceRepository->create($data);
    }

    public function update(int $id, array $data): Invoice
    {
        return $this->invoiceRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->invoiceRepository->delete($id);
    }

    public function generateInvoice(int $studentId, int $termId, array $items): Invoice
    {
        $totalAmount = array_sum($items);

        return $this->invoiceRepository->create([
            'student_id' => $studentId,
            'term_id' => $termId,
            'total_amount' => $totalAmount,
            'amount_paid' => 0,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'pending',
        ]);
    }

    public function getStudentInvoices(int $studentId): Collection
    {
        return Invoice::with('term')
            ->where('student_id', $studentId)
            ->get();
    }

    public function getStudentStatement(int $studentId): Collection
    {
        return Invoice::with('payments', 'term')
            ->where('student_id', $studentId)
            ->get();
    }
}
