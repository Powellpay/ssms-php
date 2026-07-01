<?php

namespace App\Providers;

use App\Repositories\Contracts\ClassSubjectRepositoryInterface;
use App\Repositories\Eloquent\ClassSubjectRepository;
use App\Services\Contracts\ClassSubjectServiceInterface;
use App\Services\ClassSubjectService;
use Illuminate\Support\ServiceProvider;

class ClassSubjectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClassSubjectRepositoryInterface::class, ClassSubjectRepository::class);
        $this->app->bind(ClassSubjectServiceInterface::class, ClassSubjectService::class);
    }
}
