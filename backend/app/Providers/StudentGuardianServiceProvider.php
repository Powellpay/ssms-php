<?php

namespace App\Providers;

use App\Repositories\Contracts\StudentGuardianRepositoryInterface;
use App\Repositories\Eloquent\StudentGuardianRepository;
use App\Services\Contracts\StudentGuardianServiceInterface;
use App\Services\StudentGuardianService;
use Illuminate\Support\ServiceProvider;

class StudentGuardianServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StudentGuardianRepositoryInterface::class, StudentGuardianRepository::class);
        $this->app->bind(StudentGuardianServiceInterface::class, StudentGuardianService::class);
    }
}
