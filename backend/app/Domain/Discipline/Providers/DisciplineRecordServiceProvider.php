<?php

namespace App\Domain\Discipline\Providers;

use App\Domain\Discipline\Repositories\Contracts\DisciplineRecordRepositoryInterface;
use App\Domain\Discipline\Repositories\Eloquent\DisciplineRecordRepository;
use App\Domain\Discipline\Services\Contracts\DisciplineRecordServiceInterface;
use App\Domain\Discipline\Services\DisciplineRecordService;
use Illuminate\Support\ServiceProvider;

class DisciplineRecordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DisciplineRecordRepositoryInterface::class, DisciplineRecordRepository::class);
        $this->app->bind(DisciplineRecordServiceInterface::class, DisciplineRecordService::class);
    }
}
