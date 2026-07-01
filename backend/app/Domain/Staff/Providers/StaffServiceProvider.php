<?php

namespace App\Domain\Staff\Providers;

use App\Domain\Staff\Repositories\Contracts\StaffRepositoryInterface;
use App\Domain\Staff\Repositories\Eloquent\StaffRepository;
use App\Domain\Staff\Services\Contracts\StaffServiceInterface;
use App\Domain\Staff\Services\StaffService;
use Illuminate\Support\ServiceProvider;

class StaffServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StaffRepositoryInterface::class, StaffRepository::class);
        $this->app->bind(StaffServiceInterface::class, StaffService::class);
    }
}
