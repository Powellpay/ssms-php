<?php

namespace App\Providers;

use App\Repositories\Contracts\TimetableRepositoryInterface;
use App\Repositories\Eloquent\TimetableRepository;
use App\Services\Contracts\TimetableServiceInterface;
use App\Services\TimetableService;
use Illuminate\Support\ServiceProvider;

class TimetableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TimetableRepositoryInterface::class, TimetableRepository::class);
        $this->app->bind(TimetableServiceInterface::class, TimetableService::class);
    }
}
