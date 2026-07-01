<?php

namespace App\Providers;

use App\Repositories\Contracts\GenericSkillRepositoryInterface;
use App\Repositories\Eloquent\GenericSkillRepository;
use App\Services\Contracts\GenericSkillServiceInterface;
use App\Services\GenericSkillService;
use Illuminate\Support\ServiceProvider;

class GenericSkillServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GenericSkillRepositoryInterface::class, GenericSkillRepository::class);
        $this->app->bind(GenericSkillServiceInterface::class, GenericSkillService::class);
    }
}
