<?php

namespace App\Providers;

use App\Repositories\Contracts\CurriculumThemeRepositoryInterface;
use App\Repositories\Eloquent\CurriculumThemeRepository;
use App\Services\Contracts\CurriculumThemeServiceInterface;
use App\Services\CurriculumThemeService;
use Illuminate\Support\ServiceProvider;

class CurriculumThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CurriculumThemeRepositoryInterface::class, CurriculumThemeRepository::class);
        $this->app->bind(CurriculumThemeServiceInterface::class, CurriculumThemeService::class);
    }
}
