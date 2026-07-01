<?php

namespace App\Domain\Curriculum\Repositories\Contracts;

use App\Domain\Curriculum\Models\Subject;
use Illuminate\Database\Eloquent\Collection;

interface SubjectRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Subject;
    public function create(array $data): Subject;
    public function update(int $id, array $data): Subject;
    public function delete(int $id): bool;
    public function findByCode(string $code): ?Subject;
    public function findByCategory(string $category): Collection;
}
