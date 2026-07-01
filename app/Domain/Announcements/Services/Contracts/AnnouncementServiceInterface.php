<?php

namespace App\Domain\Announcements\Services\Contracts;

use App\Domain\Announcements\Models\Announcement;
use Illuminate\Database\Eloquent\Collection;

interface AnnouncementServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Announcement;

    public function create(array $data): Announcement;

    public function update(int $id, array $data): Announcement;

    public function delete(int $id): bool;

    public function getAnnouncementsForRole(string $roleName): Collection;
}
