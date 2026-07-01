<?php

namespace App\Providers;

use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use App\Repositories\Eloquent\EnrollmentRepository;
use App\Services\Contracts\EnrollmentServiceInterface;
use App\Services\EnrollmentService;
use Illuminate\Support\ServiceProvider;

class EnrollmentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EnrollmentRepositoryInterface::class, EnrollmentRepository::class);
        $this->app->bind(EnrollmentServiceInterface::class, EnrollmentService::class);
    }
}
