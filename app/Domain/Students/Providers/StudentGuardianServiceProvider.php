<?php

namespace App\Domain\Students\Providers;

use App\Domain\Students\Repositories\Contracts\StudentGuardianRepositoryInterface;
use App\Domain\Students\Repositories\Eloquent\StudentGuardianRepository;
use App\Domain\Students\Services\Contracts\StudentGuardianServiceInterface;
use App\Domain\Students\Services\StudentGuardianService;
use Illuminate\Support\ServiceProvider;

class StudentGuardianServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StudentGuardianRepositoryInterface::class, StudentGuardianRepository::class);
        $this->app->bind(StudentGuardianServiceInterface::class, StudentGuardianService::class);
    }
}
