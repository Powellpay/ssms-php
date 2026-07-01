<?php

namespace App\Providers;

use App\Repositories\Contracts\GradingScaleRepositoryInterface;
use App\Repositories\Eloquent\GradingScaleRepository;
use App\Services\Contracts\GradingScaleServiceInterface;
use App\Services\GradingScaleService;
use Illuminate\Support\ServiceProvider;

class GradingScaleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GradingScaleRepositoryInterface::class, GradingScaleRepository::class);
        $this->app->bind(GradingScaleServiceInterface::class, GradingScaleService::class);
    }
}
