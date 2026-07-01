<?php

namespace App\Providers;

use App\Repositories\Contracts\ClassLevelRepositoryInterface;
use App\Repositories\Eloquent\ClassLevelRepository;
use App\Services\Contracts\ClassLevelServiceInterface;
use App\Services\ClassLevelService;
use Illuminate\Support\ServiceProvider;

class ClassLevelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClassLevelRepositoryInterface::class, ClassLevelRepository::class);
        $this->app->bind(ClassLevelServiceInterface::class, ClassLevelService::class);
    }
}
