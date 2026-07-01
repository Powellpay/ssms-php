<?php

namespace App\Domain\Academic\Repositories\Eloquent;

use App\Domain\Academic\Models\Term;
use App\Domain\Academic\Repositories\Contracts\TermRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TermRepository implements TermRepositoryInterface
{
    public function all(): Collection
    {
        return Term::all();
    }

    public function find(int $id): ?Term
    {
        return Term::find($id);
    }

    public function create(array $data): Term
    {
        return Term::create($data);
    }

    public function update(int $id, array $data): Term
    {
        $term = $this->find($id);
        $term->update($data);
        return $term;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findCurrent(): ?Term
    {
        return Term::where('is_current', true)->first();
    }

    public function findByAcademicYear(int $yearId): Collection
    {
        return Term::where('academic_year_id', $yearId)->get();
    }
}
