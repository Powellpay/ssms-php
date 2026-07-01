<?php

namespace App\Domain\Announcements\Providers;

use App\Domain\Announcements\Repositories\Contracts\AnnouncementRepositoryInterface;
use App\Domain\Announcements\Repositories\Eloquent\AnnouncementRepository;
use App\Domain\Announcements\Services\Contracts\AnnouncementServiceInterface;
use App\Domain\Announcements\Services\AnnouncementService;
use Illuminate\Support\ServiceProvider;

class AnnouncementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AnnouncementRepositoryInterface::class, AnnouncementRepository::class);
        $this->app->bind(AnnouncementServiceInterface::class, AnnouncementService::class);
    }
}
