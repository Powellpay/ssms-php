<?php

namespace App\Domain\Library\Services;

use App\Domain\Library\Models\BookLoan;
use App\Domain\Library\Repositories\Contracts\BookLoanRepositoryInterface;
use App\Domain\Library\Repositories\Contracts\LibraryBookRepositoryInterface;
use App\Domain\Library\Services\Contracts\BookLoanServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class BookLoanService implements BookLoanServiceInterface
{
    public function __construct(
        protected BookLoanRepositoryInterface $bookLoanRepository,
        protected LibraryBookRepositoryInterface $libraryBookRepository
    ) {}

    public function all(): Collection
    {
        return $this->bookLoanRepository->all();
    }

    public function find(int $id): ?BookLoan
    {
        return $this->bookLoanRepository->find($id);
    }

    public function create(array $data): BookLoan
    {
        return $this->bookLoanRepository->create($data);
    }

    public function update(int $id, array $data): BookLoan
    {
        return $this->bookLoanRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->bookLoanRepository->delete($id);
    }

    public function getActiveLoans(int $studentId): Collection
    {
        return BookLoan::with('book')
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->get();
    }

    public function getOverdueLoans(): Collection
    {
        return BookLoan::with('book', 'student')
            ->where('status', 'active')
            ->where('due_date', '<', now())
            ->get();
    }
}
