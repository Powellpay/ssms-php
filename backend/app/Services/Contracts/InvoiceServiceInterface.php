<?php

namespace App\Services\Contracts;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Invoice;

    public function create(array $data): Invoice;

    public function update(int $id, array $data): Invoice;

    public function delete(int $id): bool;

    public function generateInvoice(int $studentId, int $termId, array $items): Invoice;

    public function getStudentInvoices(int $studentId): Collection;

    public function getStudentStatement(int $studentId): Collection;
}
