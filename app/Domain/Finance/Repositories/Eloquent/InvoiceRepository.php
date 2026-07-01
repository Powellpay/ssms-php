<?php

namespace App\Domain\Finance\Repositories\Eloquent;

use App\Domain\Finance\Models\Invoice;
use App\Domain\Finance\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function all(): Collection
    {
        return Invoice::all();
    }

    public function find(int $id): ?Invoice
    {
        return Invoice::find($id);
    }

    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function update(int $id, array $data): Invoice
    {
        $invoice = $this->find($id);
        $invoice->update($data);
        return $invoice;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStudent(int $studentId): Collection
    {
        return Invoice::where('student_id', $studentId)->get();
    }

    public function findByStudentAndTerm(int $studentId, int $termId): Collection
    {
        return Invoice::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();
    }

    public function findByStatus(string $status): Collection
    {
        return Invoice::where('status', $status)->get();
    }
}
