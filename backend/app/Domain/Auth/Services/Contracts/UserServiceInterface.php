<?php

namespace App\Domain\Auth\Services\Contracts;

use App\Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?User;

    public function create(array $data): User;

    public function update(int $id, array $data): User;

    public function delete(int $id): bool;

    public function findByEmail(string $email): ?User;

    public function authenticate(string $username, string $password): ?User;

    public function findByRole(string $roleName): Collection;
}
