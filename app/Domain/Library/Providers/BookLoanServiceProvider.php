<?php

namespace App\Domain\Library\Providers;

use App\Domain\Library\Repositories\Contracts\BookLoanRepositoryInterface;
use App\Domain\Library\Repositories\Eloquent\BookLoanRepository;
use App\Domain\Library\Services\Contracts\BookLoanServiceInterface;
use App\Domain\Library\Services\BookLoanService;
use Illuminate\Support\ServiceProvider;

class BookLoanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookLoanRepositoryInterface::class, BookLoanRepository::class);
        $this->app->bind(BookLoanServiceInterface::class, BookLoanService::class);
    }
}
