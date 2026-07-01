<?php

namespace App\Providers;

use App\Repositories\Contracts\AcademicYearRepositoryInterface;
use App\Repositories\Eloquent\AcademicYearRepository;
use App\Services\Contracts\AcademicYearServiceInterface;
use App\Services\AcademicYearService;
use Illuminate\Support\ServiceProvider;

class AcademicYearServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AcademicYearRepositoryInterface::class, AcademicYearRepository::class);
        $this->app->bind(AcademicYearServiceInterface::class, AcademicYearService::class);
    }
}
