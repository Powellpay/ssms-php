<?php

namespace App\Services\Contracts;

use App\Models\BookLoan;
use Illuminate\Database\Eloquent\Collection;

interface BookLoanServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?BookLoan;

    public function create(array $data): BookLoan;

    public function update(int $id, array $data): BookLoan;

    public function delete(int $id): bool;

    public function getActiveLoans(int $studentId): Collection;

    public function getOverdueLoans(): Collection;
}
