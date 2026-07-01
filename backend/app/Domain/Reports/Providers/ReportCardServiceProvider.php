<?php

namespace App\Domain\Reports\Providers;

use App\Domain\Reports\Repositories\Contracts\ReportCardRepositoryInterface;
use App\Domain\Reports\Repositories\Eloquent\ReportCardRepository;
use App\Domain\Reports\Services\Contracts\ReportCardServiceInterface;
use App\Domain\Reports\Services\ReportCardService;
use Illuminate\Support\ServiceProvider;

class ReportCardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReportCardRepositoryInterface::class, ReportCardRepository::class);
        $this->app->bind(ReportCardServiceInterface::class, ReportCardService::class);
    }
}
