<?php

namespace App\Services\Contracts;

use App\Models\Term;
use Illuminate\Database\Eloquent\Collection;

interface TermServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Term;

    public function create(array $data): Term;

    public function update(int $id, array $data): Term;

    public function delete(int $id): bool;

    public function setCurrent(int $id): void;

    public function getCurrentTerm(): ?Term;
}
