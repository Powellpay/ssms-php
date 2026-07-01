<?php

namespace App\Services;

use App\Models\LibraryBook;
use App\Models\BookLoan;
use App\Repositories\Contracts\LibraryBookRepositoryInterface;
use App\Services\Contracts\LibraryBookServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class LibraryBookService implements LibraryBookServiceInterface
{
    public function __construct(
        protected LibraryBookRepositoryInterface $libraryBookRepository
    ) {}

    public function all(): Collection
    {
        return $this->libraryBookRepository->all();
    }

    public function find(int $id): ?LibraryBook
    {
        return $this->libraryBookRepository->find($id);
    }

    public function create(array $data): LibraryBook
    {
        return $this->libraryBookRepository->create($data);
    }

    public function update(int $id, array $data): LibraryBook
    {
        return $this->libraryBookRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->libraryBookRepository->delete($id);
    }

    public function borrowBook(int $bookId, int $studentId, int $staffId, int $days): BookLoan
    {
        $book = LibraryBook::findOrFail($bookId);

        $book->decrement('available_copies');

        $loan = BookLoan::create([
            'book_id' => $bookId,
            'student_id' => $studentId,
            'staff_id' => $staffId,
            'borrow_date' => now(),
            'due_date' => now()->addDays($days),
            'status' => 'active',
        ]);

        return $loan;
    }

    public function returnBook(int $loanId): BookLoan
    {
        $loan = BookLoan::with('book')->findOrFail($loanId);

        $loan->book->increment('available_copies');

        $loan->update([
            'return_date' => now(),
            'status' => 'returned',
        ]);

        return $loan;
    }
}
