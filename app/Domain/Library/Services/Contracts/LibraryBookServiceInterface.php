<?php

namespace App\Domain\Library\Services\Contracts;

use App\Domain\Library\Models\LibraryBook;
use Illuminate\Database\Eloquent\Collection;

interface LibraryBookServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?LibraryBook;

    public function create(array $data): LibraryBook;

    public function update(int $id, array $data): LibraryBook;

    public function delete(int $id): bool;

    public function borrowBook(int $bookId, int $studentId, int $staffId, int $days): \App\Domain\Library\Models\BookLoan;

    public function returnBook(int $loanId): \App\Domain\Library\Models\BookLoan;
}
