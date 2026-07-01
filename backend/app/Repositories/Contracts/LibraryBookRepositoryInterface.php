<?php

namespace App\Repositories\Contracts;

use App\Models\LibraryBook;
use Illuminate\Database\Eloquent\Collection;

interface LibraryBookRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?LibraryBook;
    public function create(array $data): LibraryBook;
    public function update(int $id, array $data): LibraryBook;
    public function delete(int $id): bool;
    public function findByIsbn(string $isbn): ?LibraryBook;
    public function findAvailable(): Collection;
}
