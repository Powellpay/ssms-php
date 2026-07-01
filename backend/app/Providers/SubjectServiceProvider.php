<?php

namespace App\Providers;

use App\Repositories\Contracts\SubjectRepositoryInterface;
use App\Repositories\Eloquent\SubjectRepository;
use App\Services\Contracts\SubjectServiceInterface;
use App\Services\SubjectService;
use Illuminate\Support\ServiceProvider;

class SubjectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
        $this->app->bind(SubjectServiceInterface::class, SubjectService::class);
    }
}
