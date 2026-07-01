<?php

namespace App\Domain\Curriculum\Providers;

use App\Domain\Curriculum\Repositories\Contracts\LearningOutcomeRepositoryInterface;
use App\Domain\Curriculum\Repositories\Eloquent\LearningOutcomeRepository;
use App\Domain\Curriculum\Services\Contracts\LearningOutcomeServiceInterface;
use App\Domain\Curriculum\Services\LearningOutcomeService;
use Illuminate\Support\ServiceProvider;

class LearningOutcomeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LearningOutcomeRepositoryInterface::class, LearningOutcomeRepository::class);
        $this->app->bind(LearningOutcomeServiceInterface::class, LearningOutcomeService::class);
    }
}
