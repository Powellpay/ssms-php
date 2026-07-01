<?php

namespace App\Domain\Assessment\Providers;

use App\Domain\Assessment\Repositories\Contracts\GenericSkillRatingRepositoryInterface;
use App\Domain\Assessment\Repositories\Eloquent\GenericSkillRatingRepository;
use App\Domain\Assessment\Services\Contracts\GenericSkillRatingServiceInterface;
use App\Domain\Assessment\Services\GenericSkillRatingService;
use Illuminate\Support\ServiceProvider;

class GenericSkillRatingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GenericSkillRatingRepositoryInterface::class, GenericSkillRatingRepository::class);
        $this->app->bind(GenericSkillRatingServiceInterface::class, GenericSkillRatingService::class);
    }
}
