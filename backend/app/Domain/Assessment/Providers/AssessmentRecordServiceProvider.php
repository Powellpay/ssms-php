<?php

namespace App\Domain\Assessment\Providers;

use App\Domain\Assessment\Repositories\Contracts\AssessmentRecordRepositoryInterface;
use App\Domain\Assessment\Repositories\Eloquent\AssessmentRecordRepository;
use App\Domain\Assessment\Services\Contracts\AssessmentRecordServiceInterface;
use App\Domain\Assessment\Services\AssessmentRecordService;
use Illuminate\Support\ServiceProvider;

class AssessmentRecordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AssessmentRecordRepositoryInterface::class, AssessmentRecordRepository::class);
        $this->app->bind(AssessmentRecordServiceInterface::class, AssessmentRecordService::class);
    }
}
