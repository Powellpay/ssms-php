<?php

namespace App\Domain\Attendance\Providers;

use App\Domain\Attendance\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Domain\Attendance\Repositories\Eloquent\AttendanceRepository;
use App\Domain\Attendance\Services\Contracts\AttendanceServiceInterface;
use App\Domain\Attendance\Services\AttendanceService;
use Illuminate\Support\ServiceProvider;

class AttendanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
        $this->app->bind(AttendanceServiceInterface::class, AttendanceService::class);
    }
}
