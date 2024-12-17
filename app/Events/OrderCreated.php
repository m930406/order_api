<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCreated implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $order;

    public function __construct(array $order)
    {
        $this->order = $order;
    }
}