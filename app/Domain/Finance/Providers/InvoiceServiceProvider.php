<?php

namespace App\Domain\Finance\Providers;

use App\Domain\Finance\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Domain\Finance\Repositories\Eloquent\InvoiceRepository;
use App\Domain\Finance\Services\Contracts\InvoiceServiceInterface;
use App\Domain\Finance\Services\InvoiceService;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(InvoiceServiceInterface::class, InvoiceService::class);
    }
}
