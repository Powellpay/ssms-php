<?php

namespace App\Domain\Assessment\Providers;

use App\Domain\Assessment\Repositories\Contracts\GradingScaleRepositoryInterface;
use App\Domain\Assessment\Repositories\Eloquent\GradingScaleRepository;
use App\Domain\Assessment\Services\Contracts\GradingScaleServiceInterface;
use App\Domain\Assessment\Services\GradingScaleService;
use Illuminate\Support\ServiceProvider;

class GradingScaleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GradingScaleRepositoryInterface::class, GradingScaleRepository::class);
        $this->app->bind(GradingScaleServiceInterface::class, GradingScaleService::class);
    }
}
