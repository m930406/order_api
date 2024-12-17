<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\OrderCreated;
use App\Listeners\StoreOrderListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderCreated::class => [
            StoreOrderListener::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
