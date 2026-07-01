<?php

namespace App\Providers;

use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Repositories\Eloquent\StaffRepository;
use App\Services\Contracts\StaffServiceInterface;
use App\Services\StaffService;
use Illuminate\Support\ServiceProvider;

class StaffServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StaffRepositoryInterface::class, StaffRepository::class);
        $this->app->bind(StaffServiceInterface::class, StaffService::class);
    }
}
