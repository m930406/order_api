<?php

namespace App\Events;

class OrderCreated
{
    public $orderData;

    public function __construct(array $orderData)
    {
        $this->orderData = $orderData;
    }
}
