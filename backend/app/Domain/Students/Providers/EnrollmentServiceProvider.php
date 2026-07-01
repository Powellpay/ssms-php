<?php

namespace App\Domain\Students\Providers;

use App\Domain\Students\Repositories\Contracts\EnrollmentRepositoryInterface;
use App\Domain\Students\Repositories\Eloquent\EnrollmentRepository;
use App\Domain\Students\Services\Contracts\EnrollmentServiceInterface;
use App\Domain\Students\Services\EnrollmentService;
use Illuminate\Support\ServiceProvider;

class EnrollmentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EnrollmentRepositoryInterface::class, EnrollmentRepository::class);
        $this->app->bind(EnrollmentServiceInterface::class, EnrollmentService::class);
    }
}
