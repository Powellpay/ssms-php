<?php

namespace App\Domain\Assessment\Providers;

use App\Domain\Assessment\Repositories\Contracts\SkillRatingScaleRepositoryInterface;
use App\Domain\Assessment\Repositories\Eloquent\SkillRatingScaleRepository;
use App\Domain\Assessment\Services\Contracts\SkillRatingScaleServiceInterface;
use App\Domain\Assessment\Services\SkillRatingScaleService;
use Illuminate\Support\ServiceProvider;

class SkillRatingScaleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SkillRatingScaleRepositoryInterface::class, SkillRatingScaleRepository::class);
        $this->app->bind(SkillRatingScaleServiceInterface::class, SkillRatingScaleService::class);
    }
}
