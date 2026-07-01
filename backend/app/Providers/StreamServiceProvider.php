<?php

namespace App\Providers;

use App\Repositories\Contracts\StreamRepositoryInterface;
use App\Repositories\Eloquent\StreamRepository;
use App\Services\Contracts\StreamServiceInterface;
use App\Services\StreamService;
use Illuminate\Support\ServiceProvider;

class StreamServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StreamRepositoryInterface::class, StreamRepository::class);
        $this->app->bind(StreamServiceInterface::class, StreamService::class);
    }
}
