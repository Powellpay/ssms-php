<?php

namespace App\Domain\Academic\Services;

use App\Domain\Academic\Models\Term;
use App\Domain\Academic\Repositories\Contracts\TermRepositoryInterface;
use App\Domain\Academic\Services\Contracts\TermServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class TermService implements TermServiceInterface
{
    public function __construct(
        protected TermRepositoryInterface $termRepository
    ) {}

    public function all(): Collection
    {
        return $this->termRepository->all();
    }

    public function find(int $id): ?Term
    {
        return $this->termRepository->find($id);
    }

    public function create(array $data): Term
    {
        return $this->termRepository->create($data);
    }

    public function update(int $id, array $data): Term
    {
        return $this->termRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->termRepository->delete($id);
    }

    public function setCurrent(int $id): void
    {
        Term::where('is_current', true)->update(['is_current' => false]);
        $this->termRepository->update($id, ['is_current' => true]);
    }

    public function getCurrentTerm(): ?Term
    {
        return Term::with('academicYear')->where('is_current', true)->first();
    }
}
