<?php

namespace App\Domain\Finance\Providers;

use App\Domain\Finance\Repositories\Contracts\FeeStructureRepositoryInterface;
use App\Domain\Finance\Repositories\Eloquent\FeeStructureRepository;
use App\Domain\Finance\Services\Contracts\FeeStructureServiceInterface;
use App\Domain\Finance\Services\FeeStructureService;
use Illuminate\Support\ServiceProvider;

class FeeStructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FeeStructureRepositoryInterface::class, FeeStructureRepository::class);
        $this->app->bind(FeeStructureServiceInterface::class, FeeStructureService::class);
    }
}
