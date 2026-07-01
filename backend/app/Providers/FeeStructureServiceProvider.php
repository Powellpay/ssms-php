<?php

namespace App\Providers;

use App\Repositories\Contracts\FeeStructureRepositoryInterface;
use App\Repositories\Eloquent\FeeStructureRepository;
use App\Services\Contracts\FeeStructureServiceInterface;
use App\Services\FeeStructureService;
use Illuminate\Support\ServiceProvider;

class FeeStructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FeeStructureRepositoryInterface::class, FeeStructureRepository::class);
        $this->app->bind(FeeStructureServiceInterface::class, FeeStructureService::class);
    }
}
