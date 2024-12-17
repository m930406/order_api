<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(array $order)
    {
        $this->order = $order;
    }
}
