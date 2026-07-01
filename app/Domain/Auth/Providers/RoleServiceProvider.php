<?php

namespace App\Domain\Auth\Providers;

use App\Domain\Auth\Repositories\Contracts\RoleRepositoryInterface;
use App\Domain\Auth\Repositories\Eloquent\RoleRepository;
use App\Domain\Auth\Services\Contracts\RoleServiceInterface;
use App\Domain\Auth\Services\RoleService;
use Illuminate\Support\ServiceProvider;

class RoleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
    }
}
