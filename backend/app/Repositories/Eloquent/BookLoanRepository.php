<?php

namespace App\Repositories\Eloquent;

use App\Models\BookLoan;
use App\Repositories\Contracts\BookLoanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BookLoanRepository implements BookLoanRepositoryInterface
{
    public function all(): Collection
    {
        return BookLoan::all();
    }

    public function find(int $id): ?BookLoan
    {
        return BookLoan::find($id);
    }

    public function create(array $data): BookLoan
    {
        return BookLoan::create($data);
    }

    public function update(int $id, array $data): BookLoan
    {
        $bookLoan = $this->find($id);
        $bookLoan->update($data);
        return $bookLoan;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByBook(int $bookId): Collection
    {
        return BookLoan::where('book_id', $bookId)->get();
    }

    public function findByStudent(int $studentId): Collection
    {
        return BookLoan::where('student_id', $studentId)->get();
    }

    public function findOverdue(): Collection
    {
        return BookLoan::whereNull('return_date')
            ->where('due_date', '<', now())
            ->get();
    }

    public function findActiveByStudent(int $studentId): Collection
    {
        return BookLoan::where('student_id', $studentId)
            ->whereNull('return_date')
            ->get();
    }
}
