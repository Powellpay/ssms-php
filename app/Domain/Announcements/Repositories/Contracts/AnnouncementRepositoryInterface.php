<?php

namespace App\Domain\Announcements\Repositories\Contracts;

use App\Domain\Announcements\Models\Announcement;
use Illuminate\Database\Eloquent\Collection;

interface AnnouncementRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Announcement;
    public function create(array $data): Announcement;
    public function update(int $id, array $data): Announcement;
    public function delete(int $id): bool;
    public function findByTargetRole(string $role): Collection;
}
