<?php

namespace App\Providers;

use App\Repositories\Contracts\LearningOutcomeRepositoryInterface;
use App\Repositories\Eloquent\LearningOutcomeRepository;
use App\Services\Contracts\LearningOutcomeServiceInterface;
use App\Services\LearningOutcomeService;
use Illuminate\Support\ServiceProvider;

class LearningOutcomeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LearningOutcomeRepositoryInterface::class, LearningOutcomeRepository::class);
        $this->app->bind(LearningOutcomeServiceInterface::class, LearningOutcomeService::class);
    }
}
