<?php

namespace App\Providers;

use App\Repositories\Contracts\ReportCardRepositoryInterface;
use App\Repositories\Eloquent\ReportCardRepository;
use App\Services\Contracts\ReportCardServiceInterface;
use App\Services\ReportCardService;
use Illuminate\Support\ServiceProvider;

class ReportCardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReportCardRepositoryInterface::class, ReportCardRepository::class);
        $this->app->bind(ReportCardServiceInterface::class, ReportCardService::class);
    }
}
