<?php

namespace App\Domain\Curriculum\Providers;

use App\Domain\Curriculum\Repositories\Contracts\CurriculumThemeRepositoryInterface;
use App\Domain\Curriculum\Repositories\Eloquent\CurriculumThemeRepository;
use App\Domain\Curriculum\Services\Contracts\CurriculumThemeServiceInterface;
use App\Domain\Curriculum\Services\CurriculumThemeService;
use Illuminate\Support\ServiceProvider;

class CurriculumThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CurriculumThemeRepositoryInterface::class, CurriculumThemeRepository::class);
        $this->app->bind(CurriculumThemeServiceInterface::class, CurriculumThemeService::class);
    }
}
