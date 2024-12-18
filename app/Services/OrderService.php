<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderByCurrency;

class OrderService
{
    public function getOrderDetail($id)
    {
        $order = $this->getOrder($id);
        $orderDetail = $this->getOrderDetailByCurrency($order->currency, $id);
        $orderDetail->address = $this->decodeAddress($orderDetail->address);

        return $orderDetail;
    }

    protected function getOrder($id)
    {
        $order = Order::where('order_id', $id)->first();
        if (is_null($order)) {
            throw new \Exception('Order not found');
        }
        return $order;
    }

    protected function getOrderDetailByCurrency($currency, $id)
    {
        $orderDetail = new OrderByCurrency();
        $orderDetail->setTableCurrency($currency);
        $result = $orderDetail->where('order_id', $id)->first();
        if (is_null($result)) {
            throw new \Exception('Order not found');
        }
        return $result;
    }

    protected function decodeAddress($address)
    {
        return !empty($address) ? json_decode($address, true) : $address;
    }
}
