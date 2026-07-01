<?php

namespace App\Providers;

use App\Repositories\Contracts\AnnouncementRepositoryInterface;
use App\Repositories\Eloquent\AnnouncementRepository;
use App\Services\Contracts\AnnouncementServiceInterface;
use App\Services\AnnouncementService;
use Illuminate\Support\ServiceProvider;

class AnnouncementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AnnouncementRepositoryInterface::class, AnnouncementRepository::class);
        $this->app->bind(AnnouncementServiceInterface::class, AnnouncementService::class);
    }
}
