<?php

namespace App\Domain\Academic\Providers;

use App\Domain\Academic\Repositories\Contracts\TermRepositoryInterface;
use App\Domain\Academic\Repositories\Eloquent\TermRepository;
use App\Domain\Academic\Services\Contracts\TermServiceInterface;
use App\Domain\Academic\Services\TermService;
use Illuminate\Support\ServiceProvider;

class TermServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TermRepositoryInterface::class, TermRepository::class);
        $this->app->bind(TermServiceInterface::class, TermService::class);
    }
}
