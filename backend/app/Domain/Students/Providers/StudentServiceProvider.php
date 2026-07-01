<?php

namespace App\Domain\Students\Providers;

use App\Domain\Students\Repositories\Contracts\StudentRepositoryInterface;
use App\Domain\Students\Repositories\Eloquent\StudentRepository;
use App\Domain\Students\Services\Contracts\StudentServiceInterface;
use App\Domain\Students\Services\StudentService;
use Illuminate\Support\ServiceProvider;

class StudentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            StudentRepositoryInterface::class,
            StudentRepository::class
        );

        $this->app->bind(
            StudentServiceInterface::class,
            StudentService::class
        );
    }

    public function boot(): void
    {
        //
    }
}
