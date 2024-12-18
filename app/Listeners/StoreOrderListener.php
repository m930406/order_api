<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\OrderByCurrency;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreOrderListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $orderData = $event->orderData;

        DB::beginTransaction();

        try {
            // Create the basic order record
            $orderBasic = new Order();
            $orderBasic->order_id = $orderData['order_id'];
            $orderBasic->currency = $orderData['currency'];
            $orderBasic->save();

            // Create the order detail record in the currency-specific table
            $orderDetail = new OrderByCurrency();
            $orderDetail->setTableCurrency($orderData['currency']);
            $orderDetail->order_id = $orderData['order_id'];
            $orderDetail->name = $orderData['name'];
            $orderDetail->address = $orderData['address'];
            $orderDetail->price = $orderData['price'];
            $orderDetail->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
