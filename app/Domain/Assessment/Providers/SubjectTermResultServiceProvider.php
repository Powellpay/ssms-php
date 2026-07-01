<?php

namespace App\Domain\Assessment\Providers;

use App\Domain\Assessment\Repositories\Contracts\SubjectTermResultRepositoryInterface;
use App\Domain\Assessment\Repositories\Eloquent\SubjectTermResultRepository;
use App\Domain\Assessment\Services\Contracts\SubjectTermResultServiceInterface;
use App\Domain\Assessment\Services\SubjectTermResultService;
use Illuminate\Support\ServiceProvider;

class SubjectTermResultServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SubjectTermResultRepositoryInterface::class, SubjectTermResultRepository::class);
        $this->app->bind(SubjectTermResultServiceInterface::class, SubjectTermResultService::class);
    }
}
