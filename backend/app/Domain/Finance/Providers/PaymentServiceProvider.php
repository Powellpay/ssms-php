<?php

namespace App\Domain\Finance\Providers;

use App\Domain\Finance\Repositories\Contracts\PaymentRepositoryInterface;
use App\Domain\Finance\Repositories\Eloquent\PaymentRepository;
use App\Domain\Finance\Services\Contracts\PaymentServiceInterface;
use App\Domain\Finance\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
    }
}
