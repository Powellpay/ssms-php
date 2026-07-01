<?php

namespace App\Domain\Timetable\Providers;

use App\Domain\Timetable\Repositories\Contracts\TimetableRepositoryInterface;
use App\Domain\Timetable\Repositories\Eloquent\TimetableRepository;
use App\Domain\Timetable\Services\Contracts\TimetableServiceInterface;
use App\Domain\Timetable\Services\TimetableService;
use Illuminate\Support\ServiceProvider;

class TimetableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TimetableRepositoryInterface::class, TimetableRepository::class);
        $this->app->bind(TimetableServiceInterface::class, TimetableService::class);
    }
}
