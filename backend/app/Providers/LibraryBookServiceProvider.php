<?php

namespace App\Providers;

use App\Repositories\Contracts\LibraryBookRepositoryInterface;
use App\Repositories\Eloquent\LibraryBookRepository;
use App\Services\Contracts\LibraryBookServiceInterface;
use App\Services\LibraryBookService;
use Illuminate\Support\ServiceProvider;

class LibraryBookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LibraryBookRepositoryInterface::class, LibraryBookRepository::class);
        $this->app->bind(LibraryBookServiceInterface::class, LibraryBookService::class);
    }
}
