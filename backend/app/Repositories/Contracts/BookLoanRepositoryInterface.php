<?php

namespace App\Repositories\Contracts;

use App\Models\BookLoan;
use Illuminate\Database\Eloquent\Collection;

interface BookLoanRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?BookLoan;
    public function create(array $data): BookLoan;
    public function update(int $id, array $data): BookLoan;
    public function delete(int $id): bool;
    public function findByBook(int $bookId): Collection;
    public function findByStudent(int $studentId): Collection;
    public function findOverdue(): Collection;
    public function findActiveByStudent(int $studentId): Collection;
}
