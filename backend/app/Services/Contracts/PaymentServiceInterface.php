<?php

namespace App\Services\Contracts;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

interface PaymentServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Payment;

    public function create(array $data): Payment;

    public function update(int $id, array $data): Payment;

    public function delete(int $id): bool;

    public function recordPayment(int $invoiceId, int $studentId, float $amount, string $method): Payment;
}
