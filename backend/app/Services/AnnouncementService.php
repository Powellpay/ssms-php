<?php

namespace App\Services;

use App\Models\Announcement;
use App\Repositories\Contracts\AnnouncementRepositoryInterface;
use App\Services\Contracts\AnnouncementServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class AnnouncementService implements AnnouncementServiceInterface
{
    public function __construct(
        protected AnnouncementRepositoryInterface $announcementRepository
    ) {}

    public function all(): Collection
    {
        return $this->announcementRepository->all();
    }

    public function find(int $id): ?Announcement
    {
        return $this->announcementRepository->find($id);
    }

    public function create(array $data): Announcement
    {
        return $this->announcementRepository->create($data);
    }

    public function update(int $id, array $data): Announcement
    {
        return $this->announcementRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->announcementRepository->delete($id);
    }

    public function getAnnouncementsForRole(string $roleName): Collection
    {
        return Announcement::where('target_role', $roleName)->orderBy('created_at', 'desc')->get();
    }
}
