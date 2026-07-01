<?php

namespace App\Providers;

use App\Repositories\Contracts\AssessmentTypeRepositoryInterface;
use App\Repositories\Eloquent\AssessmentTypeRepository;
use App\Services\Contracts\AssessmentTypeServiceInterface;
use App\Services\AssessmentTypeService;
use Illuminate\Support\ServiceProvider;

class AssessmentTypeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AssessmentTypeRepositoryInterface::class, AssessmentTypeRepository::class);
        $this->app->bind(AssessmentTypeServiceInterface::class, AssessmentTypeService::class);
    }
}
