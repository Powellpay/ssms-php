<?php

namespace App\Domain\Curriculum\Services\Contracts;

use App\Domain\Curriculum\Models\Subject;
use Illuminate\Database\Eloquent\Collection;

interface SubjectServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Subject;

    public function create(array $data): Subject;

    public function update(int $id, array $data): Subject;

    public function delete(int $id): bool;
}
