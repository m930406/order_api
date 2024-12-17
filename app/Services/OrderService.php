<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderByCurrency;

class OrderService
{
    public function getOrderDetail($id)
    {        
        // Get the order currency
        $orderCurrencyDetetion = new Order();
        $result = $orderCurrencyDetetion->where('order_id', $id)->first();
        if (is_null($result)) {
            throw new \Exception('Order not found');
        }

        // Get the order detail
        $orderDetail = new OrderByCurrency();
        $orderDetail->setTableCurrency($result->currency);
        $result = $orderDetail->where('order_id', $id)->first();
        if (is_null($result)) {
            throw new \Exception('Order not found');
        }

        // Decode the address field
        if (!empty($result->address)) {
            $result->address = json_decode($result->address, true);
        }

        return $result;
    }
}