<?php

namespace App\Providers;

use App\Repositories\Contracts\SubjectTeacherRepositoryInterface;
use App\Repositories\Eloquent\SubjectTeacherRepository;
use App\Services\Contracts\SubjectTeacherServiceInterface;
use App\Services\SubjectTeacherService;
use Illuminate\Support\ServiceProvider;

class SubjectTeacherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SubjectTeacherRepositoryInterface::class, SubjectTeacherRepository::class);
        $this->app->bind(SubjectTeacherServiceInterface::class, SubjectTeacherService::class);
    }
}
