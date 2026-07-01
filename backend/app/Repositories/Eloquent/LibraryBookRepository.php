<?php

namespace App\Repositories\Eloquent;

use App\Models\LibraryBook;
use App\Repositories\Contracts\LibraryBookRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LibraryBookRepository implements LibraryBookRepositoryInterface
{
    public function all(): Collection
    {
        return LibraryBook::all();
    }

    public function find(int $id): ?LibraryBook
    {
        return LibraryBook::find($id);
    }

    public function create(array $data): LibraryBook
    {
        return LibraryBook::create($data);
    }

    public function update(int $id, array $data): LibraryBook
    {
        $libraryBook = $this->find($id);
        $libraryBook->update($data);
        return $libraryBook;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByIsbn(string $isbn): ?LibraryBook
    {
        return LibraryBook::where('isbn', $isbn)->first();
    }

    public function findAvailable(): Collection
    {
        return LibraryBook::where('available_copies', '>', 0)->get();
    }
}
