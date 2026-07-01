<?php

namespace App\Domain\Curriculum\Providers;

use App\Domain\Curriculum\Repositories\Contracts\ClassSubjectRepositoryInterface;
use App\Domain\Curriculum\Repositories\Eloquent\ClassSubjectRepository;
use App\Domain\Curriculum\Services\Contracts\ClassSubjectServiceInterface;
use App\Domain\Curriculum\Services\ClassSubjectService;
use Illuminate\Support\ServiceProvider;

class ClassSubjectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClassSubjectRepositoryInterface::class, ClassSubjectRepository::class);
        $this->app->bind(ClassSubjectServiceInterface::class, ClassSubjectService::class);
    }
}
