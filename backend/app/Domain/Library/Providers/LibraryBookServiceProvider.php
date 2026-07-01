<?php

namespace App\Domain\Library\Providers;

use App\Domain\Library\Repositories\Contracts\LibraryBookRepositoryInterface;
use App\Domain\Library\Repositories\Eloquent\LibraryBookRepository;
use App\Domain\Library\Services\Contracts\LibraryBookServiceInterface;
use App\Domain\Library\Services\LibraryBookService;
use Illuminate\Support\ServiceProvider;

class LibraryBookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LibraryBookRepositoryInterface::class, LibraryBookRepository::class);
        $this->app->bind(LibraryBookServiceInterface::class, LibraryBookService::class);
    }
}
