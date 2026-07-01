<?php

namespace App\Domain\Academic\Providers;

use App\Domain\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;
use App\Domain\Academic\Repositories\Eloquent\AcademicYearRepository;
use App\Domain\Academic\Services\Contracts\AcademicYearServiceInterface;
use App\Domain\Academic\Services\AcademicYearService;
use Illuminate\Support\ServiceProvider;

class AcademicYearServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AcademicYearRepositoryInterface::class, AcademicYearRepository::class);
        $this->app->bind(AcademicYearServiceInterface::class, AcademicYearService::class);
    }
}
