<?php

namespace App\Domain\Academic\Providers;

use App\Domain\Academic\Repositories\Contracts\StreamRepositoryInterface;
use App\Domain\Academic\Repositories\Eloquent\StreamRepository;
use App\Domain\Academic\Services\Contracts\StreamServiceInterface;
use App\Domain\Academic\Services\StreamService;
use Illuminate\Support\ServiceProvider;

class StreamServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StreamRepositoryInterface::class, StreamRepository::class);
        $this->app->bind(StreamServiceInterface::class, StreamService::class);
    }
}
