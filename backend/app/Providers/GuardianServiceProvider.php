<?php

namespace App\Providers;

use App\Repositories\Contracts\GuardianRepositoryInterface;
use App\Repositories\Eloquent\GuardianRepository;
use App\Services\Contracts\GuardianServiceInterface;
use App\Services\GuardianService;
use Illuminate\Support\ServiceProvider;

class GuardianServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GuardianRepositoryInterface::class, GuardianRepository::class);
        $this->app->bind(GuardianServiceInterface::class, GuardianService::class);
    }
}
