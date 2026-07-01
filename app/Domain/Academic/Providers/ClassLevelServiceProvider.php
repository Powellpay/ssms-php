<?php

namespace App\Domain\Academic\Providers;

use App\Domain\Academic\Repositories\Contracts\ClassLevelRepositoryInterface;
use App\Domain\Academic\Repositories\Eloquent\ClassLevelRepository;
use App\Domain\Academic\Services\Contracts\ClassLevelServiceInterface;
use App\Domain\Academic\Services\ClassLevelService;
use Illuminate\Support\ServiceProvider;

class ClassLevelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClassLevelRepositoryInterface::class, ClassLevelRepository::class);
        $this->app->bind(ClassLevelServiceInterface::class, ClassLevelService::class);
    }
}
