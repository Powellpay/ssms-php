<?php

namespace App\Providers;

use App\Repositories\Contracts\BookLoanRepositoryInterface;
use App\Repositories\Eloquent\BookLoanRepository;
use App\Services\Contracts\BookLoanServiceInterface;
use App\Services\BookLoanService;
use Illuminate\Support\ServiceProvider;

class BookLoanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookLoanRepositoryInterface::class, BookLoanRepository::class);
        $this->app->bind(BookLoanServiceInterface::class, BookLoanService::class);
    }
}
