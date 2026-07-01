<?php

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Invoice;
    public function create(array $data): Invoice;
    public function update(int $id, array $data): Invoice;
    public function delete(int $id): bool;
    public function findByStudent(int $studentId): Collection;
    public function findByStudentAndTerm(int $studentId, int $termId): Collection;
    public function findByStatus(string $status): Collection;
}
