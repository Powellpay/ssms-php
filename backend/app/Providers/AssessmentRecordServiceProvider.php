<?php

namespace App\Providers;

use App\Repositories\Contracts\AssessmentRecordRepositoryInterface;
use App\Repositories\Eloquent\AssessmentRecordRepository;
use App\Services\Contracts\AssessmentRecordServiceInterface;
use App\Services\AssessmentRecordService;
use Illuminate\Support\ServiceProvider;

class AssessmentRecordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AssessmentRecordRepositoryInterface::class, AssessmentRecordRepository::class);
        $this->app->bind(AssessmentRecordServiceInterface::class, AssessmentRecordService::class);
    }
}
