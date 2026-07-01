<?php

namespace App\Providers;

use App\Repositories\Contracts\SubjectTermResultRepositoryInterface;
use App\Repositories\Eloquent\SubjectTermResultRepository;
use App\Services\Contracts\SubjectTermResultServiceInterface;
use App\Services\SubjectTermResultService;
use Illuminate\Support\ServiceProvider;

class SubjectTermResultServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SubjectTermResultRepositoryInterface::class, SubjectTermResultRepository::class);
        $this->app->bind(SubjectTermResultServiceInterface::class, SubjectTermResultService::class);
    }
}
