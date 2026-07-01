<?php

namespace App\Domain\Auth\Providers;

use App\Domain\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Domain\Auth\Repositories\Eloquent\UserRepository;
use App\Domain\Auth\Services\Contracts\UserServiceInterface;
use App\Domain\Auth\Services\UserService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            UserServiceInterface::class,
            UserService::class
        );
    }

    public function boot(): void
    {
        //
    }
}
