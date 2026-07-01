<?php

namespace App\Repositories\Contracts;

use App\Models\DisciplineRecord;
use Illuminate\Database\Eloquent\Collection;

interface DisciplineRecordRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?DisciplineRecord;
    public function create(array $data): DisciplineRecord;
    public function update(int $id, array $data): DisciplineRecord;
    public function delete(int $id): bool;
    public function findByStudent(int $studentId): Collection;
}
