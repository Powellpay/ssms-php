<?php

namespace App\Domain\Assessment\Providers;

use App\Domain\Assessment\Repositories\Contracts\AssessmentTypeRepositoryInterface;
use App\Domain\Assessment\Repositories\Eloquent\AssessmentTypeRepository;
use App\Domain\Assessment\Services\Contracts\AssessmentTypeServiceInterface;
use App\Domain\Assessment\Services\AssessmentTypeService;
use Illuminate\Support\ServiceProvider;

class AssessmentTypeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AssessmentTypeRepositoryInterface::class, AssessmentTypeRepository::class);
        $this->app->bind(AssessmentTypeServiceInterface::class, AssessmentTypeService::class);
    }
}
