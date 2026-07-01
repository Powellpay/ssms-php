<?php

namespace App\Domain\Students\Providers;

use App\Domain\Students\Repositories\Contracts\GuardianRepositoryInterface;
use App\Domain\Students\Repositories\Eloquent\GuardianRepository;
use App\Domain\Students\Services\Contracts\GuardianServiceInterface;
use App\Domain\Students\Services\GuardianService;
use Illuminate\Support\ServiceProvider;

class GuardianServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GuardianRepositoryInterface::class, GuardianRepository::class);
        $this->app->bind(GuardianServiceInterface::class, GuardianService::class);
    }
}
