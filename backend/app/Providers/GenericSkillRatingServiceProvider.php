<?php

namespace App\Providers;

use App\Repositories\Contracts\GenericSkillRatingRepositoryInterface;
use App\Repositories\Eloquent\GenericSkillRatingRepository;
use App\Services\Contracts\GenericSkillRatingServiceInterface;
use App\Services\GenericSkillRatingService;
use Illuminate\Support\ServiceProvider;

class GenericSkillRatingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GenericSkillRatingRepositoryInterface::class, GenericSkillRatingRepository::class);
        $this->app->bind(GenericSkillRatingServiceInterface::class, GenericSkillRatingService::class);
    }
}
