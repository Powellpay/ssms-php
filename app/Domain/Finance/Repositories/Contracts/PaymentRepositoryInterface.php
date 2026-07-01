<?php

namespace App\Domain\Finance\Repositories\Contracts;

use App\Domain\Finance\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Payment;
    public function create(array $data): Payment;
    public function update(int $id, array $data): Payment;
    public function delete(int $id): bool;
    public function findByInvoice(int $invoiceId): Collection;
    public function findByStudent(int $studentId): Collection;
}
