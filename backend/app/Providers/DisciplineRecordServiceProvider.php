<?php

namespace App\Providers;

use App\Repositories\Contracts\DisciplineRecordRepositoryInterface;
use App\Repositories\Eloquent\DisciplineRecordRepository;
use App\Services\Contracts\DisciplineRecordServiceInterface;
use App\Services\DisciplineRecordService;
use Illuminate\Support\ServiceProvider;

class DisciplineRecordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DisciplineRecordRepositoryInterface::class, DisciplineRecordRepository::class);
        $this->app->bind(DisciplineRecordServiceInterface::class, DisciplineRecordService::class);
    }
}
