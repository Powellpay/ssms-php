<?php

namespace App\Domain\Announcements\Repositories\Eloquent;

use App\Domain\Announcements\Models\Announcement;
use App\Domain\Announcements\Repositories\Contracts\AnnouncementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    public function all(): Collection
    {
        return Announcement::all();
    }

    public function find(int $id): ?Announcement
    {
        return Announcement::find($id);
    }

    public function create(array $data): Announcement
    {
        return Announcement::create($data);
    }

    public function update(int $id, array $data): Announcement
    {
        $announcement = $this->find($id);
        $announcement->update($data);
        return $announcement;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByTargetRole(string $role): Collection
    {
        return Announcement::where('target_role', $role)->get();
    }
}
