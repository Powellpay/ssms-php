<?php

namespace App\Domain\Curriculum\Providers;

use App\Domain\Curriculum\Repositories\Contracts\SubjectRepositoryInterface;
use App\Domain\Curriculum\Repositories\Eloquent\SubjectRepository;
use App\Domain\Curriculum\Services\Contracts\SubjectServiceInterface;
use App\Domain\Curriculum\Services\SubjectService;
use Illuminate\Support\ServiceProvider;

class SubjectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
        $this->app->bind(SubjectServiceInterface::class, SubjectService::class);
    }
}
