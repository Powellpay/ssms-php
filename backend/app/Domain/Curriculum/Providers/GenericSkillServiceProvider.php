<?php

namespace App\Domain\Curriculum\Providers;

use App\Domain\Curriculum\Repositories\Contracts\GenericSkillRepositoryInterface;
use App\Domain\Curriculum\Repositories\Eloquent\GenericSkillRepository;
use App\Domain\Curriculum\Services\Contracts\GenericSkillServiceInterface;
use App\Domain\Curriculum\Services\GenericSkillService;
use Illuminate\Support\ServiceProvider;

class GenericSkillServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GenericSkillRepositoryInterface::class, GenericSkillRepository::class);
        $this->app->bind(GenericSkillServiceInterface::class, GenericSkillService::class);
    }
}
