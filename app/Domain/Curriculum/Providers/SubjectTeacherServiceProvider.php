<?php

namespace App\Domain\Curriculum\Providers;

use App\Domain\Curriculum\Repositories\Contracts\SubjectTeacherRepositoryInterface;
use App\Domain\Curriculum\Repositories\Eloquent\SubjectTeacherRepository;
use App\Domain\Curriculum\Services\Contracts\SubjectTeacherServiceInterface;
use App\Domain\Curriculum\Services\SubjectTeacherService;
use Illuminate\Support\ServiceProvider;

class SubjectTeacherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SubjectTeacherRepositoryInterface::class, SubjectTeacherRepository::class);
        $this->app->bind(SubjectTeacherServiceInterface::class, SubjectTeacherService::class);
    }
}
