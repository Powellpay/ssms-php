<?php

namespace App\Providers;

use App\Repositories\Contracts\SkillRatingScaleRepositoryInterface;
use App\Repositories\Eloquent\SkillRatingScaleRepository;
use App\Services\Contracts\SkillRatingScaleServiceInterface;
use App\Services\SkillRatingScaleService;
use Illuminate\Support\ServiceProvider;

class SkillRatingScaleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SkillRatingScaleRepositoryInterface::class, SkillRatingScaleRepository::class);
        $this->app->bind(SkillRatingScaleServiceInterface::class, SkillRatingScaleService::class);
    }
}
